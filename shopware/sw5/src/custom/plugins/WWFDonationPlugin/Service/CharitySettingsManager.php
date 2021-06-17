<?php

namespace WWFDonationPlugin\Service;

use exxeta\wwf\banner\AbstractSettingsManager;
use Shopware\Bundle\PluginInstallerBundle\Exception\ShopNotFoundException;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Models\Shop\Shop;
use WWFDonationPlugin\WWFDonationPlugin;

/**
 * Class CharitySettingsManager
 *
 * FIXME: migrate to sw5
 *
 * @package WWFDonationPlugin\Service
 */
class CharitySettingsManager extends AbstractSettingsManager
{
    const SETTING_PREFIX = 'WWFDonationPlugin.config';

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * only used in static install context!
     *
     * @var SystemConfigService
     */
    private static $systemConfigServiceStatic;

    /**
     * CharitySettingsManager constructor.
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public static function init(): void
    {
        $shop = Shopware()->Models()
            ->getRepository(Shop::class)
            ->findOneBy(['default' => true, 'active' => true]);
        /* @var Shop $shop */
        if (!$shop || !$shop instanceof Shop) {
            throw new ShopNotFoundException("Could not find an active default shop!");
        }
        $pluginManager = Shopware()->Container()->get('shopware_plugininstaller.plugin_manager');
        /* @var InstallerService $pluginManager */
        $plugin = $pluginManager->getPluginByName(WWFDonationPlugin::PLUGIN_NAME);

        foreach (static::$settings as $settingKey => $defaultValue) {
            $pluginManager->saveConfigElement($plugin, static::convertSettingKey($settingKey), $defaultValue, $shop);
        }
    }

    public static function uninstall()
    {
        foreach (static::$settings as $settingKey => $value) {
            static::$systemConfigServiceStatic->delete($settingKey);
        }
    }

    public static function getPluginName(): string
    {
        return 'WWFDonationPlugin';
    }

    public function getSetting(string $settingKey, $defaultValue)
    {
        $value = $this->systemConfigService->get(static::convertSettingKey($settingKey));
        if ($value === null) {
            return $defaultValue;
        }
        return $value;
    }

    public function updateSetting(string $settingKey, $value): void
    {
        $this->systemConfigService->set(static::convertSettingKey($settingKey), $value);
    }

    /**
     * we need to convert the setting keys here from snake_case to lowerCamelCase.
     * Only the latter is supported by shopware 6.
     *
     * @param string $settingKey
     * @return string
     */
    public static function convertSettingKey(string $settingKey): string
    {
        $output = str_replace('_', ' ', trim($settingKey));
        $output = lcfirst(ucwords($output)); // lower camel case
        $output = str_replace(' ', '', $output);
        return sprintf('%s', $output);
    }

    /**
     * used in (un-)install context only
     *
     * @param SystemConfigService $systemConfigService
     */
    public static function setSystemConfigServiceStatic(SystemConfigService $systemConfigService): void
    {
        self::$systemConfigServiceStatic = $systemConfigService;
    }
}