<?php declare(strict_types=1);

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
        $reportResultModel = new ReportResultModel($startDate, $endDate);


        return $reportResultModel;
    }
}