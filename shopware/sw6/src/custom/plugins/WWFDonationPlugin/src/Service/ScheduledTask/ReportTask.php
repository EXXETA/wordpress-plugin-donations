<?php
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


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
        return 24 * 60 * 60; // daily
    }
}