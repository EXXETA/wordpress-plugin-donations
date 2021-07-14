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
        return new ReportResultModel(new DateTime(), new DateTime());
    }
}