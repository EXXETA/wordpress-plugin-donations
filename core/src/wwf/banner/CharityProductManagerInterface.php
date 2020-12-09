<?php

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
    public function getCharityProductCategory(): mixed;

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
     * @param string $campaignSlug
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportResultModel
     */
    public function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel;
}