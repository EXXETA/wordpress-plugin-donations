<?php

namespace exxeta\wwf\banner;


use DateTime;
use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\CharityProduct;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Interface CharityProductManager
 *
 * statically encapsulates most important methods of products offered by this plugin
 *
 * @package exxeta\wwf\banner
 */
interface CharityProductManagerInterface
{
    /**
     * @return string[]
     */
    public static function getAllCharityProductSlugs(): array;

    /**
     * @return CharityProduct[]
     */
    public static function getAllProducts(): array;

    /**
     * @return string
     */
    public static function getCategoryId(): string;

    /**
     * @return array|false|\WP_Term
     */
    public static function getCharityProductCategory();

    /**
     * method load product initially
     */
    public static function initProducts(): void;

    /**
     * method to get a specific product by its slug
     *
     * @param string $slug
     * @return CharityProduct|null
     */
    public static function getProductBySlug(string $slug): ?CharityProduct;

    /**
     * method to get a specific product id by its slug
     *
     * @param string $slug
     * @param string $settingManager
     * @return int|null
     */
    public static function getProductIdBySlug(string $slug, string $settingManager): ?int;

    /**
     * values correspond to charity coin product slug
     *
     * @return array|string[]
     */
    public static function getAllCampaignTypes(): array;

    /**
     * @return CharityCampaign[]
     */
    public static function getAllCampaigns(): array;

    /**
     * @param string $campaignSlug
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportResultModel
     */
    public static function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel;
}