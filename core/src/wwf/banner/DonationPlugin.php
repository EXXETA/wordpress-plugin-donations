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
     * a custom css class added to the (mini-)banner markup
     * @var string|null
     */
    private $customClass = null;

    /**
     * DonationPlugin constructor.
     *
     * @param $charityProductManager
     * @param $campaignManager
     * @param $settingsManager
     */
    public function __construct($charityProductManager, $campaignManager, $settingsManager, ?string $customClass)
    {
        $this->charityProductManager = $charityProductManager;
        $this->campaignManager = $campaignManager;
        $this->settingsManager = $settingsManager;
        if ($customClass) {
            $this->customClass = $customClass;
        }
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
     * @return string|null
     */
    public function getCustomClass(): ?string
    {
        return $this->customClass;
    }

    /**
     * You should not change this
     *
     * @param array $args
     */
    public final function includeContentTemplate(array $args): void
    {
        include(__DIR__ . '/template/content.php');
    }

    /**
     * You should not change this
     *
     * @param array $args
     */
    public final function includeReportTemplate(array $args): void
    {
        include(__DIR__ . '/template/report.php');
    }
}