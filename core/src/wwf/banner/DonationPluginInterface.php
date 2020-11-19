<?php


namespace exxeta\wwf\banner;

/**
 * Interface DonationPluginInterface
 * @package exxeta\wwf\banner
 */
interface DonationPluginInterface
{
    /**
     * returns the string of the Fully-Qualified-Class-Name (FQCN) of the plugin's CharityProductManagerInterface
     *
     * @return string
     */
    public function getCharityProductManager(): string;

    /**
     * returns the string of the Fully-Qualified-Class-Name (FQCN) of the plugin's CampaignManagerInterface
     *
     * @return string
     */
    public function getCampaignManager(): string;

    /**
     * returns the string of the Fully-Qualified-Class-Name (FQCN) of the plugin's SettingsManagerInterface
     *
     * @return string
     */
    public function getSettingsManager(): string;

    /**
     * inclusion of report content template takes place here
     *
     * @param array $args
     */
    public function includeContentTemplate(array $args): void;

    /**
     * inclusion of report template takes place here
     *
     * @param array $args
     */
    public function includeReportTemplate(array $args): void;
}