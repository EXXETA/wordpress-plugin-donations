<?php
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


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
    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var CharityProductManagerInterface
     */
    private $charityProductManager;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * a custom css class added to the (mini-)banner markup
     * @var string|null
     */
    private $customClass = null;

    /**
     * DonationPlugin constructor.
     *
     * @param string $pluginName
     * @param CharityProductManagerInterface $charityProductManager
     * @param SettingsManagerInterface $settingsManager
     * @param string|null $customClass optional css top-level class
     */
    public function __construct(string $pluginName, CharityProductManagerInterface $charityProductManager,
                                SettingsManagerInterface $settingsManager, ?string $customClass)
    {
        $this->pluginName = $pluginName;
        $this->charityProductManager = $charityProductManager;
        $this->settingsManager = $settingsManager;
        if ($customClass) {
            $this->customClass = $customClass;
        }
    }

    public function getCharityProductManagerInstance(): CharityProductManagerInterface
    {
        return $this->charityProductManager;
    }

    public function getSettingsManagerInstance(): SettingsManagerInterface
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

    public final function includePlainTemplate(array $args): void
    {
        include(__DIR__ . '/template/plain.php');
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }
}