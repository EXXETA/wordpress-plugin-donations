<?php


namespace WWFDonationPlugin\Service;


use DateTime;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Class SimpleCharityProductManager
 *
 * this class is just a subclass of the core library class to use api methods.
 * If you just need to call core api methods and do not need shopware specific code, e.g. from {@link ProductService},
 * you can use this class.
 *
 * Initially this was done to remove a circular reference in the Symfony DI container.
 *
 * @package WWFDonationPlugin\Service
 */
class SimpleCharityProductManager extends AbstractCharityProductManager
{
    public function getCharityProductCategory()
    {
        // NOTE: this is not implemented here
    }

    public function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel
    {
        // NOTE: this is not implemented here. Look at ProductService!
    }
}