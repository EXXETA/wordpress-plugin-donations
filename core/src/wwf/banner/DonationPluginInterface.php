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
     * render plain mail content
     *
     * @param array $args
     */
    public function includePlainTemplate(array $args): void;

    /**
     * method to get the plugin's name, e.g. for error log messages
     *
     * @return string
     */
    public function getPluginName(): string;
}