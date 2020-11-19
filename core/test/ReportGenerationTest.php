<?php

namespace test;

use DateTime;
use exxeta\wwf\banner\AbstractSettingsManager;
use exxeta\wwf\banner\model\ReportGenerationModel;
use exxeta\wwf\banner\ReportGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class ReportGenerationTest
 *
 * testing most important (Date) methods of report generation to ensure functionality in any circumstance
 *
 * @package test
 */
final class ReportGenerationTest extends TestCase
{
    public function testAllIntervalModes(): void
    {
        $modes = array_keys(AbstractSettingsManager::getReportingIntervals());
        foreach ($modes as $mode) {
            $nextDate = ReportGenerator::calculateNextExecutionDate($mode, null);
            $this->assertNotNull($nextDate);
        }
    }

    public function testNextReportGenerationDateWeekly(): void
    {
        $thisMonday = (new DateTime('monday this week'))->setTime(0, 0, 0);
        $nextMonday = (new DateTime('monday next week'))->setTime(0, 0, 0);

        $weeklyMode = AbstractSettingsManager::REPORT_INTERVAL_MODE_WEEKLY;
        $nextDate = ReportGenerator::calculateNextExecutionDate($weeklyMode, null);
        $this->assertEquals($thisMonday, $nextDate);

        $lastSunday = (new DateTime('sunday last week'))->setTime(0, 0, 0);
        $nextDate = ReportGenerator::calculateNextExecutionDate($weeklyMode, $lastSunday);
        $this->assertEquals($thisMonday, $nextDate);

        // 1 min in last week
        $nextDate = ReportGenerator::calculateNextExecutionDate($weeklyMode, (clone $thisMonday)
            ->sub(new \DateInterval('PT1M')));
        $this->assertEquals($thisMonday, $nextDate);

        $nextDate = ReportGenerator::calculateNextExecutionDate($weeklyMode, $thisMonday);
        $this->assertEquals($nextMonday, $nextDate);

        $thisWednesDay = (new DateTime('wednesday this week'))->setTime(0, 0, 0);
        $nextDate = ReportGenerator::calculateNextExecutionDate($weeklyMode, $thisWednesDay);
        $this->assertEquals($nextMonday, $nextDate);

        $pastDate1 = (new DateTime('2017-12-01'))->setTime(0, 0, 0);
        $pastDate1NextQuarter = (new DateTime('2017-12-04'))->setTime(0, 0, 0);
        $nextDate = ReportGenerator::calculateNextExecutionDate($weeklyMode, $pastDate1);
        $this->assertEquals($pastDate1NextQuarter, $nextDate);
    }

    public function testNextReportGenerationDateMonthly(): void
    {
        $firstDayThisMonth = (new DateTime('first day of this month'))->setTime(0, 0, 0);
        $firstDayNextMonth = (new DateTime('first day of next month'))->setTime(0, 0, 0);

        $monthlyMode = AbstractSettingsManager::REPORT_INTERVAL_MODE_MONTHLY;
        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, null);
        $this->assertEquals($firstDayThisMonth, $nextDate);

        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, $firstDayThisMonth);
        $this->assertEquals($firstDayNextMonth, $nextDate);
        // 1 sec in last month
        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, (clone $firstDayThisMonth)
            ->sub(new \DateInterval('PT1S')));
        $this->assertEquals($firstDayThisMonth, $nextDate);

        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, (clone $firstDayThisMonth)
            ->add(new \DateInterval('PT1S')));
        $this->assertEquals($firstDayNextMonth, $nextDate);

        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, new DateTime('now'));
        $this->assertEquals($firstDayNextMonth, $nextDate);

        $pastDate1 = (new DateTime('2017-12-01'))->setTime(0, 0, 0);
        $pastDate1NextMonth = (new DateTime('2018-01-01'))->setTime(0, 0, 0);
        $pastDate2NextMonth = (new DateTime('2018-02-01'))->setTime(0, 0, 0);
        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, $pastDate1);
        $this->assertEquals($pastDate1NextMonth, $nextDate);
        $nextDate = ReportGenerator::calculateNextExecutionDate($monthlyMode, $pastDate1NextMonth);
        $this->assertEquals($pastDate2NextMonth, $nextDate);
    }

    public function testNextReportGenerationDateQuarterly(): void
    {
        $currentQuarterBegin = $this->getBeginOfCurrentQuarter();
        $quarterlyMode = AbstractSettingsManager::REPORT_INTERVAL_MODE_QUARTERLY;
        $nextDate = ReportGenerator::calculateNextExecutionDate($quarterlyMode, null);
        $this->assertEquals($currentQuarterBegin, $nextDate);

        $nextDate = ReportGenerator::calculateNextExecutionDate($quarterlyMode, new DateTime('now'));
        $this->assertEquals($this->getBeginOfNextQuarter(), $nextDate);

        $nextDate = ReportGenerator::calculateNextExecutionDate($quarterlyMode, (new DateTime('now'))
            ->sub(new \DateInterval('P3M')));
        $this->assertEquals($currentQuarterBegin, $nextDate);

        // assume last execution in december two years ago
        $pastDate1 = (new DateTime('2017-12-01'))->setTime(0, 0, 0);
        $pastDate1NextQuarter = (new DateTime('2018-01-01'))->setTime(0, 0, 0);
        $nextDate = ReportGenerator::calculateNextExecutionDate($quarterlyMode, $pastDate1);
        $this->assertEquals($pastDate1NextQuarter, $nextDate);

        $pastDate2 = (new DateTime('2017-07-01'))->setTime(0, 0, 0);
        $pastDate2NextQuarter = (new DateTime('2017-10-01'))->setTime(0, 0, 0);
        $nextDate = ReportGenerator::calculateNextExecutionDate($quarterlyMode, $pastDate2);
        $this->assertEquals($pastDate2NextQuarter, $nextDate);
    }

    public function testReportGenerationModelWeekly(): void
    {
        $mode = AbstractSettingsManager::REPORT_INTERVAL_MODE_WEEKLY;
        // test one week
        foreach (["2020-04-06", "2020-04-07", "2020-04-08", "2020-04-09", "2020-04-10", "2020-04-11", "2020-04-12"] as $dateValue) {
            $model = ReportGenerator::getReportModel($mode, new DateTime($dateValue));
            $this->assertNotNull($model);
            $this->assertEquals(new DateTime('2020-03-30 00:00:00'), $model->getStartDate());
            $this->assertEquals(new DateTime('2020-04-05 23:59:59'), $model->getEndDate());
            $this->assertEquals($mode, $model->getIntervalMode());
        }

        // before
        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-04-05"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2020-03-23 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2020-03-29 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());

        // after
        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-04-13"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2020-04-06 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2020-04-12 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());
    }

    public function testReportGenerationModelMonthly(): void
    {
        // test one week
        $mode = AbstractSettingsManager::REPORT_INTERVAL_MODE_MONTHLY;
        foreach (["2020-04-01", "2020-04-06", "2020-04-07", "2020-04-08",
                     "2020-04-09", "2020-04-10", "2020-04-30"] as $dateValue) {
            $model = ReportGenerator::getReportModel($mode, new DateTime($dateValue));
            $this->assertNotNull($model);
            $this->assertEquals(new DateTime('2020-03-01 00:00:00'), $model->getStartDate());
            $this->assertEquals(new DateTime('2020-03-31 23:59:59'), $model->getEndDate());
            $this->assertEquals($mode, $model->getIntervalMode());
        }

        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-05-31"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2020-04-01 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2020-04-30 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());

        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-03-31"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2020-02-01 00:00:00'), $model->getStartDate());
        // even leap years should work
        $this->assertEquals(new DateTime('2020-02-29 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());
    }

    public function testReportGenerationModelQuarterly(): void
    {
        // test one week
        $mode = AbstractSettingsManager::REPORT_INTERVAL_MODE_QUARTERLY;
        foreach (["2020-04-01", "2020-05-06", "2020-06-07", "2020-04-08",
                     "2020-05-31", "2020-06-30", "2020-04-31"] as $dateValue) {
            $model = ReportGenerator::getReportModel($mode, new DateTime($dateValue));
            $this->assertNotNull($model);
            $this->assertEquals(new DateTime('2020-01-01 00:00:00'), $model->getStartDate());
            $this->assertEquals(new DateTime('2020-03-31 23:59:59'), $model->getEndDate());
            $this->assertEquals($mode, $model->getIntervalMode());
        }

        // after
        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-07-31"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2020-04-01 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2020-06-30 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());

        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-10-10"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2020-07-01 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2020-09-30 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());

        // before
        $model = ReportGenerator::getReportModel($mode, new DateTime("2020-01-02"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2019-10-01 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2019-12-31 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());

        $model = ReportGenerator::getReportModel($mode, new DateTime("2019-12-31"));
        $this->assertNotNull($model);
        $this->assertEquals(new DateTime('2019-07-01 00:00:00'), $model->getStartDate());
        $this->assertEquals(new DateTime('2019-09-30 23:59:59'), $model->getEndDate());
        $this->assertEquals($mode, $model->getIntervalMode());
    }

    public function testAllDaysOfYearInAllModes(): void
    {
        $modes = array_keys(AbstractSettingsManager::getReportingIntervals());

        foreach ($modes as $mode) {
            $startDate = new DateTime(sprintf('%s-1-1 0:0:0', (new DateTime('now'))->format('Y')));

            for ($i = 0; $i < 370; $i++) {
                $model = ReportGenerator::getReportModel($mode, $startDate);
                $this->assertNotNull($model);
                $this->assertInstanceOf(ReportGenerationModel::class, $model);

                $date = ReportGenerator::calculateNextExecutionDate($mode, $startDate);
                $this->assertNotNull($date);
                $this->assertInstanceOf(DateTime::class, $date);

                $startDate->add(new \DateInterval('P1D'));
            }
        }
    }

    /** helper methods */
    private function getBeginOfCurrentQuarter(): DateTime
    {
        $today = new DateTime('now');
        switch (intval($today->format('m'))) {
            case 1:
            case 2:
            case 3:
                return (new DateTime('first day of january this year'))->setTime(0, 0, 0);
            case 4:
            case 5:
            case 6:
                return (new DateTime('first day of april this year'))->setTime(0, 0, 0);
            case 7:
            case 8:
            case 9:
                return (new DateTime('first day of july this year'))->setTime(0, 0, 0);
            case 10:
            case 11:
            case 12:
                return (new DateTime('first day of october this year'))->setTime(0, 0, 0);
            default:
                die('invalid month');
        }
    }

    private function getBeginOfNextQuarter(): DateTime
    {
        $today = new DateTime('now');
        switch (intval($today->format('m'))) {
            case 1:
            case 2:
            case 3:
                return (new DateTime('first day of april this year'))->setTime(0, 0, 0);
            case 4:
            case 5:
            case 6:
                return (new DateTime('first day of july this year'))->setTime(0, 0, 0);
            case 7:
            case 8:
            case 9:
                return (new DateTime('first day of october this year'))->setTime(0, 0, 0);
            case 10:
            case 11:
            case 12:
                return (new DateTime('first day of january next year'))->setTime(0, 0, 0);
            default:
                die('invalid month');
        }
    }
}