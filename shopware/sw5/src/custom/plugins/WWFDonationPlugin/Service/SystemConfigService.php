<?php


namespace WWFDonationPlugin\Service;


use Shopware\Components\Plugin\ConfigReader;
use Shopware\Components\Plugin\ConfigWriter;
use WWFDonationPlugin\WWFDonationPlugin;

class SystemConfigService
{

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var ConfigWriter
     */
    private $configWriter;

    /**
     * SystemConfigService constructor.
     * @param ConfigReader $configReader
     * @param ConfigWriter $configWriter
     */
    public function __construct(ConfigReader $configReader, ConfigWriter $configWriter)
    {
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
    }

    public function get(string $string)
    {
        $conf = $this->configReader->getByPluginName(WWFDonationPlugin::PLUGIN_NAME);
//        VarDumper::dump($conf);
        $settingKey = CharitySettingsManager::convertSettingKey($string);
        if (isset($conf[$settingKey])) {
            return $conf[$settingKey];
        }
        return null;
    }

    public function delete(string $convertSettingKey)
    {
        // TODO implement
    }

    public function set(string $convertSettingKey, $defaultValue)
    {
        // TODO implement
    }

    /**
     * @return ConfigReader
     */
    public function getConfigReader(): ConfigReader
    {
        return $this->configReader;
    }

    /**
     * @return ConfigWriter
     */
    public function getConfigWriter(): ConfigWriter
    {
        return $this->configWriter;
    }
}