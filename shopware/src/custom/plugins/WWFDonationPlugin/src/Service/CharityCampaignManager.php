<?php

namespace WWFDonationPlugin\Service;


use DateTime;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Class CharityCampaignManager
 * @package WWFDonationPlugin\Service
 */
class CharityCampaignManager extends AbstractCharityProductManager
{

    public function getCharityProductCategory()
    {
        // TODO: Implement getCharityProductCategory() method.
    }

    public function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel
    {
        // TODO: Implement getRevenueOfCampaignInTimeRange() method.
    }
}