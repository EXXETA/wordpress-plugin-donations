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


use DateTime;
use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\CharityProduct;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Interface CharityProductManager
 *
 * encapsulates most important methods of products offered by this plugin
 *
 * @package exxeta\wwf\banner
 */
interface CharityProductManagerInterface
{
    /**
     * @return string[]
     */
    public function getAllCharityProductSlugs(): array;

    /**
     * @return CharityProduct[]
     */
    public function getAllProducts(): array;

    /**
     * @return string
     */
    public function getCategoryId(): string;

    /**
     * @return mixed array|false|object
     */
    public function getCharityProductCategory();

    /**
     * method load product initially
     */
    public function initProducts(): void;

    /**
     * method load campaigns initially
     */
    public function initCampaigns(): void;

    /**
     * method to get a specific product by its slug
     *
     * @param string $slug
     * @return CharityProduct|null
     */
    public function getProductBySlug(string $slug): ?CharityProduct;

    /**
     * method to get a specific product id by its slug
     *
     * @param string $slug
     * @param SettingsManagerInterface $settingManager
     * @return int|null
     */
    public function getProductIdBySlug(string $slug, SettingsManagerInterface $settingManager): ?int;

    /**
     * Method to get a campaign object by its slug
     *
     * @param string $slug
     * @return CharityCampaign|null
     */
    public function getCampaignBySlug(string $slug): ?CharityCampaign;

    /**
     * values correspond to charity coin product slug
     *
     * @return array|string[]
     */
    public function getAllCampaignTypes(): array;

    /**
     * @return CharityCampaign[]
     */
    public function getAllCampaigns(): array;

    /**
     * @return array
     */
    public function getAllCampaignBannerFileNames(): array;

    /**
     * @return array
     */
    public function getIconAssetFileNames(): array;

    /**
     * @param string $campaignSlug
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportResultModel
     */
    public function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel;
}