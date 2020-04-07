<?php


namespace donations;


/**
 * Class ReportGenerator
 *
 * logic for donation report generation
 *
 * @package donations
 */
class ReportGenerator
{

    /**
     * main method for report generation
     *
     * @param ReportGenerationModel $reportGenerationModel
     */
    public static function generateReport(ReportGenerationModel $reportGenerationModel): void
    {
        // the time should be set properly in report generation model, but to be sure, we repeat it here
        $timeRangeStart = $reportGenerationModel->getStartDate()
            ->setTime(0, 0, 0);
        $timeRangeEnd = $reportGenerationModel->getEndDate()
            ->setTime(23, 59, 59);

        if ($timeRangeStart > $timeRangeEnd) {
            error_log(Plugin::$pluginFile . ': invalid time range');
            return;
        }
        $reportingInterval = $reportGenerationModel->getIntervalMode();
        $isRegular = $reportGenerationModel->isRegular();

        // results: slug => money, string => float
        $results = [];
        $sum = 0.0;
        foreach (CampaignManager::getAllCampaignTypes() as $campaignSlug) {
            $results[$campaignSlug] =
                CampaignManager::getRevenueOfCampaignInTimeRange($campaignSlug, $timeRangeStart, $timeRangeEnd);
            $sum += $results[$campaignSlug];
        }

        // - 'subject' - string representation of month
        // - 'revenues' - array with campaignSlug => revenue, string => float
        // - 'startDate'
        // - 'endDate'
        // - 'sum'
        // - 'isRegular' - boolean
        // - 'content' - set later after body was rendered
        $args = [];

        /**
         * @param \DateTime $startDate
         * @param \DateTime $endDate
         * @return string
         */
        $timeRangeString = function(\DateTime $startDate, \DateTime $endDate): string {
            $startStringFormat = 'd/m';
            if ($startDate->format('Y') !== $endDate->format('Y')) {
                $startStringFormat = 'd/m/Y';
            }
            return $startDate->format($startStringFormat) . ' - ' . $endDate->format('d/m/Y');
        };

        if (!$isRegular) {
            $args['subject'] = 'Manueller Bericht: ' . 'Spenden | '
                . $timeRangeString($timeRangeStart, $timeRangeEnd) . ' | ' . get_bloginfo('name');
        } else {
            $args['subject'] = 'Automatischer Bericht: Spenden | '
                . SettingsManager::getReportingIntervals()[$reportingInterval]
                . ' | ' . $timeRangeString($timeRangeStart, $timeRangeEnd) . ' | ' . get_bloginfo('name');
        }

        $args['revenues'] = $results;
        $args['startDate'] = $timeRangeStart;
        $args['endDate'] = $timeRangeEnd;
        $args['sum'] = $sum;
        $args['isRegular'] = $isRegular;

        // get mail body content - used for report post type also
        ob_start();
        include(plugin_dir_path(Plugin::$pluginFile) . 'donations/mail/content.php');
        $mailBody = ob_get_contents();
        ob_end_clean();
        $args['content'] = $mailBody;

        // render full mail template
        ob_start();
        include(plugin_dir_path(Plugin::$pluginFile) . 'donations/mail/report.php');
        $mailContent = ob_get_contents();
        ob_end_clean();

        wp_insert_post([
            'post_title' => $args['subject'],
            'post_content' => $mailBody,
            'post_type' => Plugin::$customPostType,
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ]);

        $recipient = SettingsManager::getOptionReportRecipientMail();

        if ($reportGenerationModel->isSendMail()) {
            wp_mail($recipient, esc_html($args['subject']), $mailContent);
        }
        // update last execution time
        $lastExecutionDate = ($timeRangeEnd)->add(new \DateInterval('P1D'));
        $lastExecutionDate->setTime(0, 0, 0);
        SettingsManager::setOptionReportLastGeneration($lastExecutionDate);
    }

    /**
     * light-weight method to decide if its time for donation report generation
     *
     * @throws \Exception
     */
    public static function checkReportGeneration(): void
    {
        // calculate next execution date
        $today = (new \DateTime('now'))->setTime(0, 0, 0);
        $mode = SettingsManager::getOptionCurrentReportingInterval();
        $lastExecutionDate = SettingsManager::getOptionReportLastGenerationDate();

        try {
            $nextExecutionDate = self::calculateNextExecutionDate($mode, $lastExecutionDate);
        } catch (\Exception $ex) {
            error_log(Plugin::$pluginFile . ': problem calculating nextExecutionDate for report generation');
            return;
        }
        if ($nextExecutionDate <= $today) {
            try {
                $model = self::getReportModel($mode, $nextExecutionDate);
            } catch (\Exception $ex) {
                error_log(Plugin::$pluginFile . ': problem calculating report model for report generation');
                return;
            }
            if ($model instanceof ReportGenerationModel) {
                // trigger report generation
                self::generateReport($model);
            } else {
                error_log(Plugin::$pluginFile . ': problem calculating report model for report generation');
                return;
            }
        }
        // nothing to do here
    }

    /**
     * calculating dynamic next execution date. Can be in past. never null.
     * In case of any error: today's date is returned.
     * time of returned date: 0 h, 0 m, 0 s
     *
     * weekly: this monday in case of empty last execution date, otherwise next monday relative to last execution date
     * monthly: first day of this month in case of empty last execution date, otherwise first day of next month relative
     *          to last execution date
     * quarterly: first day of current quarter in case of last execution date, otherwise first day of next
     *          january/april/july/october relative to last execution date
     *
     * this method is covered by unit tests
     *
     * @param string $mode
     * @param \DateTime|null $lastExecution
     * @return \DateTime
     * @throws \Exception
     * @see ReportGenerationTest
     *
     */
    public static function calculateNextExecutionDate(string $mode, ?\DateTime $lastExecution): \DateTime
    {
        $today = (new \DateTime('now'))->setTime(0, 0, 0);
        switch ($mode) {
            case SettingsManager::REPORT_INTERVAL_MODE_WEEKLY:
                $currentMonday = (new \DateTime('monday this week'))
                    ->setTime(0, 0, 0);
                // check if last week was covered
                if (!$lastExecution) {
                    return $currentMonday;
                }
                return (clone $lastExecution)->modify('next monday')
                    ->setTime(0, 0, 0);
            case SettingsManager::REPORT_INTERVAL_MODE_MONTHLY:
                $firstDayOfThisMonth = (new \DateTime('first day of this month'))
                    ->setTime(0, 0, 0);
                if (!$lastExecution) {
                    return $firstDayOfThisMonth;
                }
                return (clone $lastExecution)->modify('first day of next month')
                    ->setTime(0, 0, 0);
            case SettingsManager::REPORT_INTERVAL_MODE_QUARTERLY:
                $firstOfJanuary = (new \DateTime('first day of january this year'));
                $firstOfApril = (new \DateTime('first day of april this year'));
                $firstOfJuly = (new \DateTime('first day of july this year'));
                $firstOfOctober = (new \DateTime('first day of october this year'));

                if (!$lastExecution) {
                    switch (intval($today->format('m'))) {
                        case 1:
                        case 2:
                        case 3:
                            return $firstOfJanuary;
                        case 4:
                        case 5:
                        case 6:
                            return $firstOfApril;
                        case 7:
                        case 8:
                        case 9:
                            return $firstOfJuly;
                        case 10:
                        case 11:
                        case 12:
                            return $firstOfOctober;
                        default:
                            error_log(sprintf('invalid month "%s"', $today->format('m')));
                    }
                }
                // same year of today and last execution time
                $year = intval($lastExecution->format('Y'));
                switch (intval($lastExecution->format('m'))) {
                    case 1:
                    case 2:
                    case 3:
                        return (new \DateTime(sprintf('first day of april %d', $year)))
                            ->setTime(0, 0, 0);
                    case 4:
                    case 5:
                    case 6:
                        return (new \DateTime(sprintf('first day of july %d', $year)))
                            ->setTime(0, 0, 0);
                    case 7:
                    case 8:
                    case 9:
                        return (new \DateTime(sprintf('first day of october %d', $year)))
                            ->setTime(0, 0, 0);
                    case 10:
                    case 11:
                    case 12:
                        return (new \DateTime(sprintf('first day of january %d', $year + 1)))
                            ->setTime(0, 0, 0);
                    default:
                        error_log(sprintf('invalid month "%s"', $today->format('m')));
                }
                return $firstOfJanuary;
            default:
                error_log(sprintf('invalid unhandled interval mode "%s"', $mode));
        }
        return $today;
    }

    /**
     * @param string $mode
     * @param \DateTime $nextExecutionDate output of #calculateNextExecutionDate
     * @return ReportGenerationModel|null
     * @throws \Exception
     */
    public static function getReportModel(string $mode, \DateTime $nextExecutionDate): ?ReportGenerationModel
    {
        // execution required
        $executionYear = $nextExecutionDate->format('Y');
        switch ($mode) {
            case SettingsManager::REPORT_INTERVAL_MODE_WEEKLY:
                $startDate = (clone $nextExecutionDate)
                    ->sub(new \DateInterval('P1W'))
                    ->modify('monday this week');
                $endDate = (clone $nextExecutionDate)
                    ->sub(new \DateInterval('P1W'))
                    ->modify('sunday this week');
                break;
            case SettingsManager::REPORT_INTERVAL_MODE_MONTHLY:
                // to subtract one month safely, use "highest" day of month = 28
                $dayOfMonth = intval($nextExecutionDate->format('d'));
                if (($dayOfMonth > 28 && $dayOfMonth < 32)) {
                    $nextExecutionDate->setDate($executionYear, $nextExecutionDate->format('m'), 28);
                }
                $startDate = (clone $nextExecutionDate)
                    ->sub(new \DateInterval('P1M'))
                    ->modify('first day of this month');
                $endDate = (clone $nextExecutionDate)
                    ->sub(new \DateInterval('P1M'))
                    ->modify('last day of this month');
                break;
            case SettingsManager::REPORT_INTERVAL_MODE_QUARTERLY:
                $firstJanuaryOfYear = new \DateTime(sprintf('%s-01-01 00:00:00', $executionYear));
                $firstJanuaryOfNextYear = new \DateTime(sprintf('%s-01-01 00:00:00', intval($executionYear) + 1));
                $firstAprilOfYear = new \DateTime(sprintf('%s-04-01 00:00:00', $executionYear));
                $firstJulyOfYear = new \DateTime(sprintf('%s-07-01 00:00:00', $executionYear));
                $firstOctoberOfYear = new \DateTime(sprintf('%s-10-01 00:00:00', $executionYear));
                // "normalize" execution date to first of quarter
                if ($firstJanuaryOfYear < $nextExecutionDate && $firstAprilOfYear > $nextExecutionDate) {
                    $nextExecutionDate = $firstJanuaryOfYear;
                }
                if ($firstAprilOfYear < $nextExecutionDate && $firstJulyOfYear > $nextExecutionDate) {
                    $nextExecutionDate = $firstAprilOfYear;
                }
                if ($firstJulyOfYear < $nextExecutionDate && $firstOctoberOfYear > $nextExecutionDate) {
                    $nextExecutionDate = $firstJulyOfYear;
                }
                if ($firstOctoberOfYear < $nextExecutionDate && $firstJanuaryOfNextYear > $nextExecutionDate) {
                    $nextExecutionDate = $firstOctoberOfYear;
                }

                $dayOfMonth = intval($nextExecutionDate->format('d'));
                if (($dayOfMonth > 28 && $dayOfMonth < 32)) {
                    $nextExecutionDate->setDate($executionYear,
                        $nextExecutionDate->format('m'), 28);
                }

                $startDate = (clone $nextExecutionDate)
                    ->sub(new \DateInterval('P3M'))
                    ->modify('first day of this month');
                $endDate = (clone $nextExecutionDate)
                    ->sub(new \DateInterval('P1M'))
                    ->modify('last day of this month');
                break;
            default:
                error_log(sprintf('invalid interval mode "%s" for report generation', $mode));
                return null;
        }
        // set time
        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);
        return new ReportGenerationModel($startDate, $endDate, $mode, true, true);
    }
}