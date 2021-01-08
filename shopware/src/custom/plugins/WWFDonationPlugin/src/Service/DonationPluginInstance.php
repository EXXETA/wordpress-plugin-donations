<?php


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\DonationPlugin;

class DonationPluginInstance extends DonationPlugin
{
    /**
     * DonationPluginInstance constructor.
     */
    public function __construct()
    {
        $charityProductManager = new CharityCampaignManager();
        $settingsManager = new CharitySettingsManager();

        parent::__construct('wwf-sw6', $charityProductManager, $settingsManager, null);
    }
}