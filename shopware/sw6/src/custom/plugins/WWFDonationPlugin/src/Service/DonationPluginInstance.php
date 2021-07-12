<?php


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\DonationPlugin;

/**
 * Class DonationPluginInstance
 * @package WWFDonationPlugin\Service
 */
class DonationPluginInstance extends DonationPlugin
{
    /**
     * DonationPluginInstance constructor.
     *
     * @param CharitySettingsManager $settingsManager
     * @param ProductService $productService
     */
    public function __construct(CharitySettingsManager $settingsManager, ProductService $productService)
    {
        parent::__construct('wwf-sw6', $productService, $settingsManager, null);
    }
}