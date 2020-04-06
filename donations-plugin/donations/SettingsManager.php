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
    /**
     * @var array option name => default value
     */
    private static $options = [
        'wp_donations_reporting_interval' => 'monthly',
        'wp_donations_reporting_live_days_in_past' => 30,
    ];

    private static $reportingIntervalOptions = [
        'monthly' => 'Monatlich',
        'weekly' => 'Wöchentlich',
    ];

    /**
     * is called during 'admin_init' hook
     */
    public static function init()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        // ensure options are present
        foreach (self::$options as $key => $defaultValue) {
            if (!get_option($key)) {
                // default value = monthly
                update_option($key, $defaultValue);
            }
        }
        register_setting('wp_donations',
            'wp_donations_reporting_interval', 'esc_attr');
        register_setting('wp_donations',
            'wp_donations_reporting_live_days_in_past', 'esc_attr');
    }

    public static function uninstall()
    {
        foreach (self::$options as $key => $defaultValue) {
            // default value = monthly
            delete_option($key);
        }
    }

    public static function getOptionCurrentReportingInterval(): string
    {
        return strval(get_option('wp_donations_reporting_interval',
            self::$options['wp_donations_reporting_interval']));
    }

    public static function getOptionLiveReportDaysInPast(): int
    {
        return intval(get_option('wp_donations_reporting_live_days_in_past',
            self::$options['wp_donations_reporting_live_days_in_past']));
    }

    /**
     * @return array str => str, value => label
     */
    public static function getReportingIntervals()
    {
        return self::$reportingIntervalOptions;
    }
}