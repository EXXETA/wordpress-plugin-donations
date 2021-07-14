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


namespace WWFDonationPlugin\Service;


use Shopware\Bundle\PluginInstallerBundle\Exception\ShopNotFoundException;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\ConfigReader;
use Shopware\Components\Plugin\ConfigWriter;
use Shopware\Models\Shop\Shop;
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
     * @var InstallerService
     */
    private $pluginManager;

    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * SystemConfigService constructor.
     * @param ConfigReader $configReader
     * @param ConfigWriter $configWriter
     */
    public function __construct(ConfigReader $configReader, ConfigWriter $configWriter)
    {
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;

        $shop = Shopware()->Models()
            ->getRepository(Shop::class)
            ->findOneBy(['default' => true, 'active' => true]);
        /* @var Shop $shop */
        if (!$shop || !$shop instanceof Shop) {
            throw new ShopNotFoundException("Could not find an active default shop!");
        }
        $this->pluginManager = Shopware()->Container()->get('shopware_plugininstaller.plugin_manager');
        $this->plugin = $this->pluginManager->getPluginByName(WWFDonationPlugin::PLUGIN_NAME);
    }

    public function get(string $string)
    {
        $conf = $this->configReader->getByPluginName(WWFDonationPlugin::PLUGIN_NAME);
        $settingKey = CharitySettingsManager::convertSettingKey($string);
        if (isset($conf[$settingKey])) {
            return $conf[$settingKey];
        }
        return null;
    }

    public function set(string $settingKey, $defaultValue)
    {
        $this->pluginManager->saveConfigElement($this->plugin, $settingKey, $defaultValue);
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