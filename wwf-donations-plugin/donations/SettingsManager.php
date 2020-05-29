<?php


namespace donations;

/**
 * Class SettingsManager
 *
 * this static class manages settings of this plugin - except product ids of donation products
 *
 * @package donations
 */
class SettingsManager
{
    public const REPORT_INTERVAL_MODE_WEEKLY = 'weekly';
    public const REPORT_INTERVAL_MODE_MONTHLY = 'monthly';
    public const REPORT_INTERVAL_MODE_QUARTERLY = 'quarterly';
    // option keys
    const WWF_DONATIONS_REPORTING_LAST_CHECK_DATE = 'wwf_donations_reporting_last_check_date';
    const WWF_DONATIONS_REPORTING_INTERVAL = 'wwf_donations_reporting_interval';
    const WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST = 'wwf_donations_reporting_live_days_in_past';
    const WWF_DONATIONS_REPORTING_RECIPIENT = 'wwf_donations_reporting_recipient';
    const WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE = 'wwf_donations_reporting_last_generation_date';
    const WWF_DONATIONS_REPORTING_COUNTER = 'wwf_donations_reporting_counter';

    /**
     * @var array option name => default value
     */
    private static $options = [
        self::WWF_DONATIONS_REPORTING_INTERVAL => self::REPORT_INTERVAL_MODE_MONTHLY,
        self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST => 30,
        self::WWF_DONATIONS_REPORTING_RECIPIENT => 'Eshop-Spenden@wwf.de',
        self::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE => null,
        self::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE => null,
        self::WWF_DONATIONS_REPORTING_COUNTER => 0,
    ];

    private static $reportingIntervalOptions = [
        self::REPORT_INTERVAL_MODE_WEEKLY => 'Wöchentlich',
        self::REPORT_INTERVAL_MODE_MONTHLY => 'Monatlich',
        self::REPORT_INTERVAL_MODE_QUARTERLY => 'Quartalsweise',
    ];

    /**
     * is called during 'admin_init' hook
     */
    public static function init(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        // ensure options are present
        foreach (self::$options as $key => $defaultValue) {
            $optionValue = get_option($key, 'no-init');
            if ($optionValue == 'no-init') {
                // set default value
                update_option($key, $defaultValue);
            }
        }
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_REPORTING_INTERVAL, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_REPORTING_RECIPIENT, 'esc_attr');
    }

    /**
     * uninstall logic of settings we manage in this class
     */
    public static function uninstall()
    {
        unregister_setting(Plugin::$pluginSlug, self::WWF_DONATIONS_REPORTING_INTERVAL);
        unregister_setting(Plugin::$pluginSlug, self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST);
        unregister_setting(Plugin::$pluginSlug, self::WWF_DONATIONS_REPORTING_RECIPIENT);
        foreach (self::$options as $key => $defaultValue) {
            // default value = monthly
            if (in_array($key, [
                self::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE,
                self::WWF_DONATIONS_REPORTING_COUNTER,
            ])) {
                // preserve several options, to avoid multiple reports after plugin updates
                // should not trigger a new report: uninstall -> install -> activate -> deactivate -> activate -> repeat
                continue;
            }
            delete_option($key);
        }
    }

    /**
     * method to get the currently chosen report interval mode of this plugin.
     * Only valid values are returned: weekly, monthly, quarterly
     *
     * @return string
     */
    public static function getOptionCurrentReportingInterval(): string
    {
        $default = self::$options[self::WWF_DONATIONS_REPORTING_INTERVAL];
        $intervalMode = strval(get_option(self::WWF_DONATIONS_REPORTING_INTERVAL, $default));
        if (!in_array($intervalMode, array_keys(self::$reportingIntervalOptions))) {
            return $default;
        }
        return $intervalMode;
    }

    /**
     * @return int
     */
    public static function getOptionLiveReportDaysInPast(): int
    {
        return intval(get_option(self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST,
            self::$options[self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST]));
    }

    /**
     * @return string
     */
    public static function getOptionReportRecipientMail(): string
    {
        return strval(get_option(self::WWF_DONATIONS_REPORTING_RECIPIENT,
            self::$options[self::WWF_DONATIONS_REPORTING_RECIPIENT]));
    }

    /**
     * @return \DateTime|null
     */
    public static function getOptionReportLastGenerationDate(): ?\DateTime
    {
        $storedValue = strval(get_option(self::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE,
            self::$options[self::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE]));
        if (!$storedValue) {
            return null;
        }
        try {
            return new \DateTime($storedValue);
        } catch (\Exception $ex) {
            error_log(sprintf('%s: invalid date value "%s"', Plugin::getPluginFile(), $storedValue));
            return null;
        }
    }

    /**
     * @param \DateTime|null $dateTime
     */
    public static function setOptionReportLastGeneration(?\DateTime $dateTime): void
    {
        if (!$dateTime || !$dateTime instanceof \DateTime) {
            try {
                $dateTime = new \DateTime();
            } catch (\Exception $ex) {
                $dateTime = strtotime('now');
            }
        }
        update_option(self::WWF_DONATIONS_REPORTING_LAST_GENERATION_DATE, $dateTime->format('c'));
    }

    public static function getOptionReportLastCheck(): ?\DateTime
    {
        $storedValue = strval(get_option(self::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE,
            self::$options[self::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE]));
        if (!$storedValue) {
            return null;
        }
        try {
            return new \DateTime($storedValue);
        } catch (\Exception $ex) {
            error_log(sprintf('%s: invalid date value "%s"', Plugin::getPluginFile(), $storedValue));
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public static function setOptionReportLastCheck(): void
    {
        update_option(self::WWF_DONATIONS_REPORTING_LAST_CHECK_DATE, date('c'));
    }

    /**
     * report counter
     *
     * @return int
     */
    public static function getOptionReportCounterIncremented(): int
    {
        $incrementedCounter = intval(get_option(self::WWF_DONATIONS_REPORTING_COUNTER, 0)) + 1;
        update_option(self::WWF_DONATIONS_REPORTING_COUNTER, $incrementedCounter);
        return $incrementedCounter;
    }

    /**
     * @return array str => str, value => label
     */
    public static function getReportingIntervals(): array
    {
        return self::$reportingIntervalOptions;
    }
}