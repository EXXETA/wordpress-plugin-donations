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
 * Smarty function to get the wwf banner markup with the possibility to provide arguments
 *
 * File: function.wwfbanner.php
 * Type: function
 * Name: wwfbanner
 * Purpose: get wwf banner markup
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_wwfbanner(array $params, Smarty_Internal_Template &$smarty)
{
    // get params or use default values
    if (isset($params['campaign'])) {
        $campaign = strval($params['campaign']);
    } else {
        // the default fallback campaign
        $campaign = \WWFDonationPlugin\Service\SimpleCharityProductManager::$PROTECT_SPECIES_COIN;
    }
    if (isset($params['isAjax'])) {
        $isAjax = boolval($params['isAjax']);
    } else {
        $isAjax = true;
    }
    if (isset($params['isMini'])) {
        $isMiniBanner = boolval($params['isMini']);
    } else {
        $isMiniBanner = false;
    }
    if (isset($params['miniBannerTargetPage'])) {
        $miniBannerTargetPage = strval($params['miniBannerTargetPage']);
    } else {
        $miniBannerTargetPage = '';
    }

    $mediaService = Shopware()->Container()->get(\WWFDonationPlugin\Service\MediaService::class);
    $productService = Shopware()->Container()->get(\WWFDonationPlugin\Service\ProductService::class);
    $pluginLogger = Shopware()->Container()->get('pluginlogger');
    $donationPluginInstance = Shopware()->Container()->get(\WWFDonationPlugin\Service\DonationPluginInstance::class);
    /* @var $donationPluginInstance \WWFDonationPlugin\Service\DonationPluginInstance */

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