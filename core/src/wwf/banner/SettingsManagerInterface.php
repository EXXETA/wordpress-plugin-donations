<?php

namespace exxeta\wwf\banner;


use DateTime;

/**
 * Interface SettingsManagerInterface
 * TODO add docs from abstract settings manager class!
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
     * @return mixed
     */
    public static function getPluginName();

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

    public function getMiniBannerIsShownInMiniCart(): bool;

    public function getMiniBannerCampaign(): ?string;

    public function getMiniBannerCampaignTargetPageId(): ?int;

    public function getReportingIntervals(): array;

    public function getReportCounterIncremented(): int;

    public function setReportLastCheck(): void;

    public function getReportLastCheck(): ?DateTime;

    public function setReportLastGeneration(?DateTime $dateTime): void;

    public function getReportLastGenerationDate(): ?DateTime;

    public function getReportRecipientMail(): string;

    public function getLiveReportDaysInPast(): int;

    public function getCurrentReportingInterval(): string;
}