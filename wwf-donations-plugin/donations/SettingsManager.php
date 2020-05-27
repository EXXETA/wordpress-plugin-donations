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

    /**
     * @var array option name => default value
     */
    private static $options = [
        'wwf_donations_reporting_interval' => self::REPORT_INTERVAL_MODE_MONTHLY,
        'wwf_donations_reporting_live_days_in_past' => 30,
        'wwf_donations_reporting_recipient' => 'Eshop-Spenden@wwf.de',
        'wwf_donations_reporting_last_generation_date' => null,
        'wwf_donations_reporting_last_check_date' => null,
        'wwf_donations_reporting_counter' => 0,
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
            $optionValue = get_option($key);
            if (!$optionValue || $optionValue !== $defaultValue) {
                // default value
                update_option($key, $defaultValue);
            }
        }
        register_setting(Plugin::$pluginSlug,
            'wwf_donations_reporting_interval', 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            'wwf_donations_reporting_live_days_in_past', 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            'wwf_donations_reporting_recipient', 'esc_attr');
    }

    /**
     * uninstall logic of settings we manage in this class
     */
    public static function uninstall()
    {
        foreach (self::$options as $key => $defaultValue) {
            // default value = monthly
            delete_option($key);
        }
        unregister_setting(Plugin::$pluginSlug, 'wwf_donations_reporting_interval');
        unregister_setting(Plugin::$pluginSlug, 'wwf_donations_reporting_live_days_in_past');
        unregister_setting(Plugin::$pluginSlug, 'wwf_donations_reporting_recipient');
    }

    /**
     * method to get the currently chosen report interval mode of this plugin.
     * Only valid values are returned: weekly, monthly, quarterly
     *
     * @return string
     */
    public static function getOptionCurrentReportingInterval(): string
    {
        $default = self::$options['wwf_donations_reporting_interval'];
        $intervalMode = strval(get_option('wwf_donations_reporting_interval', $default));
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
        return intval(get_option('wwf_donations_reporting_live_days_in_past',
            self::$options['wwf_donations_reporting_live_days_in_past']));
    }

    /**
     * @return string
     */
    public static function getOptionReportRecipientMail(): string
    {
        return strval(get_option('wwf_donations_reporting_recipient',
            self::$options['wwf_donations_reporting_recipient']));
    }

    /**
     * @return \DateTime|null
     */
    public static function getOptionReportLastGenerationDate(): ?\DateTime
    {
        $storedValue = strval(get_option('wwf_donations_reporting_last_generation_date',
            self::$options['wwf_donations_reporting_last_generation_date']));
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
        update_option('wwf_donations_reporting_last_generation_date', $dateTime->format('c'));
    }

    public static function getOptionReportLastCheck(): ?\DateTime
    {
        $storedValue = strval(get_option('wwf_donations_reporting_last_check_date',
            self::$options['wwf_donations_reporting_last_check_date']));
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
        update_option('wwf_donations_reporting_last_check_date', date('c'));
    }

    /**
     * report counter
     *
     * @return int
     */
    public static function getOptionReportCounterIncremented(): int
    {
        $incrementedCounter = intval(get_option('wwf_donations_reporting_counter', 0)) + 1;
        update_option('wwf_donations_reporting_counter', $incrementedCounter);
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