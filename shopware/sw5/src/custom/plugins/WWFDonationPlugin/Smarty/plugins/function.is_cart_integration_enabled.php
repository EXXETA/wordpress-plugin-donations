<?php

function smarty_function_is_cart_integration_enabled(array $params, Smarty_Internal_Template &$smarty)
{
    $charitySettingsManager = Shopware()->Container()->get(\WWFDonationPlugin\Service\CharitySettingsManager::class);
    /* @var $charitySettingsManager \WWFDonationPlugin\Service\CharitySettingsManager */
    $smarty->assign('isCartIntegrationEnabled', $charitySettingsManager->isCartIntegrationEnabled(), true);
    $smarty->assign('isOffCanvasCartIntegrationEnabled', $charitySettingsManager->getMiniBannerIsShownInMiniCart(), true);
}