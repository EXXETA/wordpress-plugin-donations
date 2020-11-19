<?php


namespace donations;

use Automattic\WooCommerce\Admin\Overrides\Order;
use exxeta\wwf\banner\AbstractCampaignManager;
use exxeta\wwf\banner\model\ReportResultModel;

/**
 * Class CampaignManager
 * encapsulation of campaign related topics
 * @package donations
 */
class CampaignManager extends AbstractCampaignManager
{
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
    public static function getRevenueOfCampaignInTimeRange(string $campaignSlug,
                                                           \DateTime $startDate,
                                                           \DateTime $endDate): ReportResultModel
    {
        $reportResultModel = new ReportResultModel($startDate, $endDate);

        $sum = 0;
        $productId = CharityProductManager::getProductIdBySlug($campaignSlug);
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