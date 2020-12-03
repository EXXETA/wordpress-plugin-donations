<?php

namespace exxeta\wwf\banner;


use DateTime;
use Exception;

/**
 * Class AbstractSettingsManager
 *
 * Abstract generic implementation of settings management this plugin uses
 *
 * @package exxeta\wwf\banner
 */
abstract class AbstractSettingsManager implements SettingsManagerInterface
{
    /**
     * @var array setting name/key => default value
     */
    protected static $settings = [
        self::WWF_DONATIONS_REPORTING_INTERVAL => self::REPORT_INTERVAL_MODE_MONTHLY,
        self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST => 30,
        self::WWF_DONATIONS_REPORTING_RECIPIENT => 'Eshop-Spenden@wwf.de',
        self::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE => null,
        self::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE => null,
        self::WWF_DONATIONS_REPORTING_COUNTER => 0,
        self::WWF_DONATIONS_MINI_BANNER_SHOW_IN_MINI_CART => 0,
        self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN => null,
        self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE => null,
    ];
    /**
     * @var string[] label intervals
     */
    protected static $reportingIntervalOptions = [
        self::REPORT_INTERVAL_MODE_WEEKLY => 'Wöchentlich',
        self::REPORT_INTERVAL_MODE_MONTHLY => 'Monatlich',
        self::REPORT_INTERVAL_MODE_QUARTERLY => 'Quartalsweise',
    ];

    /**
     * Method to get the currently chosen report interval mode of this plugin.
     * Only valid values should be returned: weekly, monthly, quarterly
     *
     * @return string
     */
    public static function getCurrentReportingInterval(): string
    {
        $default = static::$settings[static::WWF_DONATIONS_REPORTING_INTERVAL];
        $intervalMode = strval(static::getSetting(static::WWF_DONATIONS_REPORTING_INTERVAL, $default));
        if (!in_array($intervalMode, array_keys(static::$reportingIntervalOptions))) {
            return $default;
        }
        return $intervalMode;
    }

    /**
     * Method to get the number of days in past for the "live" report view.
     *
     * @return int
     */
    public static function getLiveReportDaysInPast(): int
    {
        return intval(static::getSetting(static::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST,
            static::$settings[static::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST]));
    }

    /**
     * Method to get the recipient mail address for generated reports.
     *
     * @return string
     */
    public static function getReportRecipientMail(): string
    {
        return strval(static::getSetting(static::WWF_DONATIONS_REPORTING_RECIPIENT,
            static::$settings[static::WWF_DONATIONS_REPORTING_RECIPIENT]));
    }

    /**
     * Method to get a date and time of the last date when a report was generated.
     *
     * @return DateTime|null
     */
    public static function getReportLastGenerationDate(): ?DateTime
    {
        $storedValue = strval(static::getSetting(static::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE,
            static::$settings[static::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE]));
        if (!$storedValue) {
            return null;
        }
        try {
            return new DateTime($storedValue);
        } catch (Exception $ex) {
            error_log(sprintf('%s: invalid date value "%s"', static::getPluginName(), $storedValue));
            return null;
        }
    }

    /**
     * Method to explicitly set the date and time of the last report generation.
     *
     * @param DateTime|null $dateTime
     */
    public static function setReportLastGeneration(?DateTime $dateTime): void
    {
        if (!$dateTime || !$dateTime instanceof DateTime) {
            try {
                $dateTime = new DateTime();
            } catch (Exception $ex) {
                $dateTime = strtotime('now');
            }
        }
        static::updateSetting(static::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE, $dateTime->format('c'));
    }

    /**
     * Method to get the datetime of the last check the plugin did to check if reports should be generated.
     * Returns null if there is no previous check, e.g. after a clean install.
     *
     * @return DateTime|null
     */
    public static function getReportLastCheck(): ?DateTime
    {
        $storedValue = strval(static::getSetting(static::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE,
            static::$settings[static::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE]));
        if (!$storedValue || empty($storedValue)) {
            return null;
        }
        try {
            return new DateTime($storedValue);
        } catch (Exception $ex) {
            error_log(sprintf('%s: invalid date value "%s"', static::getPluginName(), $storedValue));
            return null;
        }
    }

    /**
     * update date time of last check if a report should be generated to now.
     *
     * @throws Exception
     */
    public static function setReportLastCheck(): void
    {
        static::updateSetting(static::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE, date('c'));
    }

    /**
     * Report counter number already incremented and updated in settings.
     * Ready to use during report generation.
     *
     * @return int
     */
    public static function getReportCounterIncremented(): int
    {
        $incrementedCounter = intval(static::getSetting(static::WWF_DONATIONS_REPORTING_COUNTER, 0)) + 1;
        static::updateSetting(static::WWF_DONATIONS_REPORTING_COUNTER, $incrementedCounter);
        return $incrementedCounter;
    }

    /**
     * indicator to show mini banner in mini cart
     *
     * @return bool
     */
    public static function getMiniBannerIsShownInMiniCart(): bool
    {
        return boolval(static::getSetting(static::WWF_DONATIONS_MINI_BANNER_SHOW_IN_MINI_CART,
            static::$settings[static::WWF_DONATIONS_MINI_BANNER_SHOW_IN_MINI_CART]));
    }

    /**
     * @return string campaign slug or possible "null"
     */
    public static function getMiniBannerCampaign(): ?string
    {
        return strval(static::getSetting(static::WWF_DONATIONS_MINI_BANNER_CAMPAIGN,
            static::$settings[static::WWF_DONATIONS_MINI_BANNER_CAMPAIGN]));
    }

    /**
     * usage of this option is shop-specific, but this setting could store a page id (e.g. in a woocommerce shop)
     *
     * @return int|null
     */
    public static function getMiniBannerCampaignTargetPageId(): ?int
    {
        return intval(static::getSetting(static::WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE, null));
    }

    /**
     * @return array str => str, value => label
     */
    public static function getReportingIntervals(): array
    {
        return static::$reportingIntervalOptions;
    }
}