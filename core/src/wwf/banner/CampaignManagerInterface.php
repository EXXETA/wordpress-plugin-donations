<?php

namespace exxeta\wwf\banner;

use DateTime;
use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Interface CampaignManagerInterface
 *
 * encapsulation of campaign related topics
 *
 * @package exxeta\wwf\banner
 */
interface CampaignManagerInterface
{
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
     * method to load campaigns initially
     */
    public static function initCampaigns(): void;

    /**
     * method to get a campaign object by its slug
     *
     * @param string $slug
     * @return CharityCampaign|null
     */
    public static function getCampaignBySlug(string $slug): ?CharityCampaign;

    /**
     * @param string $campaignSlug
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportResultModel
     */
    public static function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel;
}