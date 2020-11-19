<?php


namespace exxeta\wwf\banner;

/**
 * Class DonationPlugin
 *
 * Generic implementation of a donation plugin
 *
 * @package exxeta\wwf\banner
 */
class DonationPlugin implements DonationPluginInterface
{
    private $charityProductManager;
    private $campaignManager;
    private $settingsManager;

    /**
     * DonationPlugin constructor.
     *
     * @param $charityProductManager
     * @param $campaignManager
     * @param $settingsManager
     */
    public function __construct($charityProductManager, $campaignManager, $settingsManager)
    {
        $this->charityProductManager = $charityProductManager;
        $this->campaignManager = $campaignManager;
        $this->settingsManager = $settingsManager;
    }

    public function getCharityProductManager(): string
    {
        return $this->charityProductManager;
    }

    public function getCampaignManager(): string
    {
        return $this->campaignManager;
    }

    public function getSettingsManager(): string
    {
        return $this->settingsManager;
    }

    /**
     * You should not change this
     */
    public final function includeContentTemplate(): void
    {
        include(__DIR__ . '/template/content.php');
    }

    /**
     * You should not change this
     */
    public final function includeReportTemplate(): void
    {
        include(__DIR__ . '/template/report.php');
    }
}