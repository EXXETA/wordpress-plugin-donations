<?php


namespace WWFDonationPlugin\Service\ScheduledTask;


use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * Class ReportTask
 *
 * Daily check if it is time to generate a donation report of the WWF products.
 *
 * @package WWFDonationPlugin\Service\ScheduledTask
 */
class ReportTask extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'wwf_donation_plugin.donation_report_task';
    }

    public static function getDefaultInterval(): int
    {
        return 24 * 60 * 60;
    }
}