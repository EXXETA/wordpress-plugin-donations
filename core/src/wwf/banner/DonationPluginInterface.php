<?php


namespace exxeta\wwf\banner;


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
     */
    public function includeContentTemplate(): void;

    /**
     * inclusion of report template takes place here
     */
    public function includeReportTemplate(): void;
}