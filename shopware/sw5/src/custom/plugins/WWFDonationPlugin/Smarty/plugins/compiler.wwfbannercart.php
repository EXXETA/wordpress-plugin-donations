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

/**
 * Smarty Compiler function to get the wwf banner markup
 *
 * File: compiler.wwfbannercart.php
 * Type: compiler
 * Name: wwfbanner
 * Purpose: get wwf banner markup
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_compiler_wwfbannercart(array $params, Smarty &$smarty)
{
    $mediaService = Shopware()->Container()->get(\WWFDonationPlugin\Service\MediaService::class);
    $productService = Shopware()->Container()->get(\WWFDonationPlugin\Service\ProductService::class);
    $pluginLogger = Shopware()->Container()->get('pluginlogger');
    $donationPluginInstance = Shopware()->Container()->get(\WWFDonationPlugin\Service\DonationPluginInstance::class);
    /* @var $donationPluginInstance \WWFDonationPlugin\Service\DonationPluginInstance */

    // get configuration options of the banner
    $charitySettingsManager = $donationPluginInstance->getSettingsManagerInstance();
    $campaign = $charitySettingsManager->getSetting(\WWFDonationPlugin\Service\CharitySettingsManager::wwfCartCampaignSettingKey, 'protect_species_coin');

    $isMiniBanner = false;
    $miniBannerTargetPage = '';
    $isAjax = false;

    $bannerHandler = new \WWFDonationPlugin\Service\ShopwareBannerHandler(
        $mediaService, $productService, $miniBannerTargetPage, $isAjax, $pluginLogger
    );

    if ($isMiniBanner) {
        $banner = new \exxeta\wwf\banner\MiniBanner($bannerHandler, $donationPluginInstance, $campaign);
    } else {
        $banner = new \exxeta\wwf\banner\Banner($bannerHandler, $donationPluginInstance, $campaign);
    }
    return $banner->render();
}