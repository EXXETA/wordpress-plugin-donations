<?php

namespace WWFDonationPlugin\Service;

use exxeta\wwf\banner\AbstractSettingsManager;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * Class CharitySettingsManager
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
        foreach (static::$settings as $settingKey => $defaultValue) {
            static::$systemConfigServiceStatic->set(static::convertSettingKey($settingKey), $defaultValue);
        }
    }

    public static function uninstall()
    {
        foreach (static::$settings as $settingKey => $defaultValue) {
            static::$systemConfigServiceStatic->delete(static::convertSettingKey($settingKey));
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
    private static function convertSettingKey(string $settingKey): string
    {
        $output = str_replace('_', ' ', $settingKey);
        $output = lcfirst(ucwords($output)); // lower camel case
        $output = str_replace(' ', '', $output);
        return sprintf('%s.%s', self::SETTING_PREFIX, $output);
    }

    /**
     * @param SystemConfigService $systemConfigServiceStatic
     */
    public static function setSystemConfigServiceStatic(SystemConfigService $systemConfigServiceStatic): void
    {
        self::$systemConfigServiceStatic = $systemConfigServiceStatic;
    }
}