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

namespace exxeta\wwf\banner;


use DateTime;

/**
 * Interface SettingsManagerInterface
 *
 * this interface provides methods to manage settings of this plugin in a concrete instantiation.
 *
 * @package exxeta\wwf\banner
 */
interface SettingsManagerInterface
{
    // supported interval modes of report generation
    public const REPORT_INTERVAL_MODE_WEEKLY = 'weekly';
    public const REPORT_INTERVAL_MODE_MONTHLY = 'monthly';
    public const REPORT_INTERVAL_MODE_QUARTERLY = 'quarterly';

    // setting keys
    const WWF_DONATIONS_REPORTING_LAST_CHECK_DATE = 'wwf_donations_reporting_last_check_date';
    const WWF_DONATIONS_REPORTING_INTERVAL = 'wwf_donations_reporting_interval';
    const WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST = 'wwf_donations_reporting_live_days_in_past';
    const WWF_DONATIONS_REPORTING_RECIPIENT = 'wwf_donations_reporting_recipient';
    const WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE = 'wwf_donations_reporting_last_generation_date';
    const WWF_DONATIONS_REPORTING_COUNTER = 'wwf_donations_reporting_counter';
    const WWF_DONATIONS_MINI_BANNER_SHOW_IN_MINI_CART = 'wwf_donations_mini_banner_show_mini_cart';
    const WWF_DONATIONS_MINI_BANNER_CAMPAIGN = 'wwf_donations_mini_banner_campaign';
    const WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE = 'wwf_donations_mini_banner_campaign_target_page';

    /**
     * Init-Method of a concrete shop-specific SettingsManager.
     * Can be used as an install-hook too.
     *
     * E.g. should be called during 'admin_init' hook in a wordpress system
     */
    public static function init(): void;

    /**
     * This should be called when this plugin needs to be uninstalled.
     * Uninstall logic of settings we manage in this class has to be placed here.
     */
    public static function uninstall();

    /**
     * method to provide a (shop-specific) plugin name
     *
     * @return string
     */
    public static function getPluginName(): string;

    /**
     * shop-specific implementation to get a setting/option of this plugin
     *
     * @param string $settingKey
     * @param mixed $defaultValue
     * @return mixed but mostly string|int|boolean
     */
    public function getSetting(string $settingKey, $defaultValue);

    /**
     * shop-specific implementation to update a single setting/option of this plugin
     *
     * @param string $settingKey
     * @param $value
     * @return mixed
     */
    public function updateSetting(string $settingKey, $value): void;

    /**
     * indicator to show mini banner in mini cart
     *
     * @return bool
     */
    public function getMiniBannerIsShownInMiniCart(): bool;

    /**
     * @return string|null campaign slug or possible "null"
     */
    public function getMiniBannerCampaign(): ?string;

    /**
     * usage of this option is shop-specific, but this setting could store a page id (e.g. in a woocommerce shop)
     *
     * @return int|null
     */
    public function getMiniBannerCampaignTargetPageId(): ?int;

    /**
     * Get all reporting intervals known to this plugin
     *
     * @return array str => str, value => label
     */
    public function getReportingIntervals(): array;

    /**
     * Report counter number already incremented and updated in settings.
     * Ready to use during report generation.
     *
     * @return int
     */
    public function getReportCounterIncremented(): int;

    /**
     * get current value of the report counter setting
     *
     * @return int
     */
    public function getReportCounter(): int;

    /**
     * update date time of last check if a report should be generated to now.
     */
    public function setReportLastCheck(): void;

    /**
     * Method to get the datetime of the last check the plugin did to check if reports should be generated.
     * Returns null if there is no previous check, e.g. after a clean install.
     *
     * @return DateTime|null
     */
    public function getReportLastCheck(): ?DateTime;

    /**
     * Method to explicitly set the date and time of the last report generation.
     *
     * @param DateTime|null $dateTime
     */
    public function setReportLastGeneration(?DateTime $dateTime): void;

    /**
     * Method to get a date and time of the last date when a report was generated.
     *
     * @return DateTime|null
     */
    public function getReportLastGenerationDate(): ?DateTime;

    /**
     * Method to get the recipient mail address for generated reports.
     *
     * @return string
     */
    public function getReportRecipientMail(): string;

    /**
     * Method to get the number of days in past for the "live" report view.
     *
     * @return int
     */
    public function getLiveReportDaysInPast(): int;

    /**
     * Method to get the currently configured reporting interval
     *
     * @return string
     */
    public function getCurrentReportingInterval(): string;

    /**
     * Method to get inline js setting of this library.
     *
     * @return bool
     */
    public function isInlineJsEnabled(): bool;
}