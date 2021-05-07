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
     * @var CharitySettingsManager
     */
    private $settingsManager;

    /**
     * DonationPluginInstance constructor.
     * @param CharitySettingsManager $settingsManager
     */
    public function __construct(CharitySettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
        $charityProductManager = new CharityCampaignManager();

        parent::__construct('wwf-sw6', $charityProductManager, $settingsManager, null);
    }
}