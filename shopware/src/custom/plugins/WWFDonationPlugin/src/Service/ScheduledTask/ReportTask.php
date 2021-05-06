<?php


namespace WWFDonationPlugin\Service\ScheduledTask;


use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ReportTask extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'wwf_donation_plugin.donation_report_task';
    }

    public static function getDefaultInterval(): int
    {
        // 1 day in secs
        return 24 * 60 * 60;
    }
}