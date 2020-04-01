<?php


namespace donations;

use Automattic\WooCommerce\Admin\Overrides\Order;

/**
 * Class CampaignManager
 * encapsulation of campaign related topics
 * @package donations
 */
class CampaignManager
{
    /**
     * @var CharityCampaign[]
     */
    private static $allCampaigns = [];

    /**
     * values correspond to charity coin product slug
     *
     * @return array|string[]
     */
    public static function getAllCampaignTypes()
    {
        return CharityProductManager::getAllCharityProductSlugs();
    }

    /**
     * @return CharityCampaign[]
     */
    public static function getAllCampaigns(): array
    {
        if (count(self::$allCampaigns) === 0) {
            // one-time init of products
            self::initCampaigns();
        }
        return self::$allCampaigns;
    }

    public static function getCampaignBySlug(string $slug): ?CharityCampaign
    {
        foreach (self::getAllCampaigns() as $singleCampaign) {
            if ($slug === $singleCampaign->getSlug()) {
                return $singleCampaign;
            }
        }
        return null;
    }

    private static function initCampaigns()
    {
        // FIXME add correct URLs and review description texts
        self::$allCampaigns = [
            new CharityCampaign(CharityProductManager::$PROTECT_SPECIES_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Artenschutzprojekte des WWF",
                "https://www.wwf.org/species"),
            new CharityCampaign(CharityProductManager::$PROTECT_OCEAN_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Meeresprojekte des WWF",
                "https://www.wwf.org/ocean"),
            new CharityCampaign(CharityProductManager::$PROTECT_FOREST_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Waldprojekte des WWF",
                "https://www.wwf.org/forest"),
            new CharityCampaign(CharityProductManager::$PROTECT_CHILDREN_YOUTH_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Kinder- und Jugendschutzprojekte des WWF",
                "https://www.wwf.org/children"),
            new CharityCampaign(CharityProductManager::$PROTECT_CLIMATE_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Klimaprojekte des WWF",
                "https://www.wwf.org/climate"),
            new CharityCampaign(CharityProductManager::$PROTECT_DIVERSITY_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für Projekte des WWF zur Erhaltung der biologischen Vielfalt",
                "https://www.wwf.org/bio"),
        ];
    }

    /**
     * NOTE: this method is untested for large amounts of orders!
     *
     * @param string $campaignSlug
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return float
     */
    public static function getRevenueOfCampaignInTimeRange(string $campaignSlug, \DateTime $startDate, \DateTime $endDate): float
    {
        $sum = 0;
        $productId = CharityProductManager::getProductIdBySlug($campaignSlug);
        if (!$productId) {
            error_log(sprintf('no product id found in wooCommerce shop for campaign "%s"', $campaignSlug));
            return 0;
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
        foreach ($orders as $order) {
            /* @var $order Order*/
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
        return $sum;
    }
}