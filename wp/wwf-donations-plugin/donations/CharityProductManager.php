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

namespace donations;

use Automattic\WooCommerce\Admin\Overrides\Order;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Class CharityProductManager
 *
 * statically encapsulates most important methods of products offered by this plugin
 *
 * @package donations
 */
class CharityProductManager extends AbstractCharityProductManager
{
    // WoCommerce default taxonomy in wordpress for product categories
    // this should not be changed
    private static $WC_PRODUCT_CATEGORY_TAXONOMY = "product_cat";

    /**
     * @return array|false|mixed|\WP_Error|\WP_Term|null
     */
    public function getCharityProductCategory()
    {
        return get_term_by('slug', $this->getCategoryId(), static::getWooProductCategoryTaxonomy());
    }

    /**
     * @return string
     */
    public static function getWooProductCategoryTaxonomy(): string
    {
        return self::$WC_PRODUCT_CATEGORY_TAXONOMY;
    }

    /**
     * FIXME: this method is untested for a large amount of orders!
     * memory problem: use batching or paging
     * sql query count problem: use own SQL query or better WooCommerce api methods
     * think about caching these values as well.
     *
     * @param string $campaignSlug
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return ReportResultModel
     */
    public function getRevenueOfCampaignInTimeRange(string $campaignSlug,
                                                    \DateTime $startDate,
                                                    \DateTime $endDate): ReportResultModel
    {
        $reportResultModel = new ReportResultModel($startDate, $endDate);

        $sum = 0;
        $productId = $this->getProductIdBySlug($campaignSlug, Plugin::getDonationPlugin()->getSettingsManagerInstance());
        if (!$productId) {
            error_log(sprintf('no product id found in wooCommerce shop for campaign "%s"', $campaignSlug));
            return $reportResultModel;
        }
        $orders = wc_get_orders([
            'date_after' => $startDate->format('c'),
            'date_before' => $endDate->format('c'),
            'type' => 'shop_order',
            'limit' => -1,
            'status' => [
                'wc-processing',
                'wc-completed',
            ],
        ]);
        $totalOrderCounter = 0;
        foreach ($orders as $order) {
            /* @var $order Order */
            $totalOrderCounter++;

            foreach ($order->get_items() as $item) {
                /* @var $item \WC_Order_Item */
                if (in_array('product_id', $item->get_data_keys())
                    && in_array('total', $item->get_data_keys())) {
                    $orderItemProductId = $item->get_data()['product_id'];
                    if ($orderItemProductId === $productId) {
                        $sum += $item->get_data()['total'];
                    }
                }
            }
        }

        $reportResultModel->setAmount($sum);
        $reportResultModel->setOrderCountTotal($totalOrderCounter);

        return $reportResultModel;
    }
}

