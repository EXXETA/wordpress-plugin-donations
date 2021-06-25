<?php

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