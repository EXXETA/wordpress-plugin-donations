<?php


namespace exxeta\wwf\banner;

/**
 * Interface DonationPluginInterface
 *
 * @package exxeta\wwf\banner
 */
interface DonationPluginInterface
{
    /**
     * @return CharityProductManagerInterface
     */
    public function getCharityProductManagerInstance(): CharityProductManagerInterface;

    /**
     * @return SettingsManagerInterface
     */
    public function getSettingsManagerInstance(): SettingsManagerInterface;

    /**
     * returns a custom css class name that should be added to the banner markup to enable external plugins
     * with custom styling capabilities.
     *
     * @return string|null
     */
    public function getCustomClass(): ?string;

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

    /**
     * method to get the plugin's name, e.g. for error log messages
     *
     * @return string
     */
    public function getPluginName(): string;
}