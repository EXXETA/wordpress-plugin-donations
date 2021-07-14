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


namespace donations;

use exxeta\wwf\banner\AbstractSettingsManager;

/**
 * Class SettingsManager
 *
 * this static class manages settings of this plugin - except product ids of donation products
 *
 * @package donations
 */
class SettingsManager extends AbstractSettingsManager
{
    /**
     * is called during 'admin_init' hook
     */
    public static function init(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // ensure options are present
        foreach (static::$settings as $key => $defaultValue) {
            $optionValue = get_option($key, 'no-init');
            if ($optionValue == 'no-init'
                || ($key === self::WWF_DONATIONS_REPORTING_RECIPIENT && $optionValue != $defaultValue) // do not allow different report recipient as defined
                || ($key === self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE && !$optionValue)) { // auto-"repair" campaign target page - if something went wrong (= $optionValue is empty/0/null)
                // dynamically set cart page as default
                if ($key === self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE) {
                    $defaultValue = wc_get_page_id('cart');
                }
                // set default value
                update_option($key, $defaultValue);
            }
        }

        // editable settings (of settings page) need to be registered here
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_REPORTING_INTERVAL, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_REPORTING_RECIPIENT, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_MINI_BANNER_SHOW_IN_MINI_CART, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN, 'esc_attr');
        register_setting(Plugin::$pluginSlug,
            self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE, 'esc_attr');
    }

    /**
     * uninstall logic of settings we manage in this class
     */
    public static function uninstall()
    {
        unregister_setting(Plugin::$pluginSlug, self::WWF_DONATIONS_REPORTING_INTERVAL);
        unregister_setting(Plugin::$pluginSlug, self::WWF_DONATIONS_REPORTING_LIVE_DAYS_IN_PAST);
        unregister_setting(Plugin::$pluginSlug, self::WWF_DONATIONS_REPORTING_RECIPIENT);
        foreach (self::$settings as $key => $defaultValue) {
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
     * wordpress-specific override for AbstractSettingsManager
     *
     * @return int|null
     */
    public function getMiniBannerCampaignTargetPageId(): ?int
    {
        return intval($this->getSetting(self::WWF_DONATIONS_MINI_BANNER_CAMPAIGN_TARGET_PAGE, wc_get_page_id('cart')));
    }

    public function getSetting(string $settingKey, $defaultValue)
    {
        return get_option($settingKey, $defaultValue);
    }

    public function updateSetting(string $settingKey, $value): void
    {
        update_option($settingKey, $value);
    }

    public static function getPluginName(): string
    {
        return Plugin::getPluginFile();
    }
}