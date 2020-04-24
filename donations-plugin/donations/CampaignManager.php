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
                "Wir befinden uns heute im größten Artensterben seit dem Ende der Dinosaurierzeit vor 65 Millionen Jahren.
                        Dass Arten aussterben ist ein natürlicher Prozess, der jedoch heute unter dem Einfluss des Menschen beträchtlich 
                        beschleunigt ist. Wir sägen an dem Ast, auf dem wir sitzen. Nahrung, Medizin, Rohstoffe, sauberes Wasser und Luft 
                        sind nur einige der wichtigen Dinge, die die Natur uns zur Verfügung stellt. Es ist längst Zeit, zu handeln. Und 
                        es könnte bald zu spät sein. Die Mission des <strong>WWF</strong> ist wichtiger denn je: &ldquor;Bewahrung der biologischen Vielfalt – 
                        ein lebendiger Planet für uns und unsere Kinder&ldquo;. Für weitere Informationen bitte 
                        <a href='https://www.wwf.de/themen-projekte/artenschutz-und-biologische-vielfalt/' target='_blank'>hier weiter lesen</a>.",
                "Artenschutz", "protect-species"),
            new CharityCampaign(CharityProductManager::$PROTECT_OCEAN_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Meeresprojekte des WWF",
                "Unser Planet ist blau – die Meere bedecken rund 70 Prozent der Erdoberfläche und sind von entscheidender Bedeutung 
                        für uns Menschen.<br/>
                        Doch Überfischung, Verschmutzung oder der Abbau von Ressourcen haben Meeresökosysteme bereits großflächig zerstört und 
                        Bestände vieler Meerestierarten auf den niedrigsten Stand seit Menschengedenken schrumpfen lassen. Darüber hinaus sind die 
                        Meere zunehmend den dramatischen Folgen der Klimakrise ausgesetzt. Längst haben die zahlreichen Belastungen der Ozeane Ausmaße 
                        angenommen, die nicht nur die biologische Vielfalt der Erde, sondern auch unsere zukünftige Ernährung bedrohen.<br/>
                        Deshalb setzt sich der <strong>WWF</strong> weltweit für einen respektvollen und nachhaltigen Umgang mit den Ökosystemen und natürlichen 
                        Ressourcen unserer Meere ein.",
                "Meeresschutz", "protect-oceans"),
            new CharityCampaign(CharityProductManager::$PROTECT_FOREST_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Waldprojekte des WWF",
                "Wälder bedecken knapp vier Mrd. Hektar und damit rund 30 Prozent der Landoberfläche der Erde. Vor 10.000 Jahren war 
                        es noch doppelt so viel. Obwohl auf der Nordhalbkugel sogar ein Nettozuwachs an Waldfläche verzeichnet wird, verschwinden 
                        global gesehen im Durchschnitt jedes Jahr mindestens 14 Millionen Hektar Wald, besonders in den Tropen. Das entspricht einer 
                        Fläche, die größer ist als Österreich und die Schweiz zusammengenommen. Besonders dramatisch ist der Rückgang der wertvollen 
                        Urwälder. Weltweit gibt es noch zwei bis drei Prozent Naturwälder. Wie der <strong>WWF</strong> schützen will, erfahrt ihr 
                        <a href='https://www.wwf.de/themen-projekte/waelder/' target='_blank'>hier</a>.",
                "Waldschutz", "protect-forests"),
            new CharityCampaign(CharityProductManager::$PROTECT_CLIMATE_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Klimaprojekte des WWF",
                "Dürren, Überflutungen, Stürme: Immer häufiger und heftiger führt der Klimawandel zu Tod und Verwüstung.
                        Naturparadiese wie der Amazonas drohen weitreichend zerstört und der Hälfte ihrer Tier- und Pflanzenarten 
                        beraubt zu werden. Ikonische Tiere wie Große Pandas können genau wie zehntausende Pflanzen, Insekten und 
                        kleinere Lebewesen regional verschwinden. Deshalb hat sich Weltgemeinschaft 2015 mit dem Pariser Abkommen 
                        darauf geeinigt, die globale Erderhitzung auf deutlich unter 2 Grad Celsius, möglichst 1,5 Grad, zu 
                        beschränken.<br/>
                        Trotzdem sinken die Treibhausgasemissionen nicht schnell genug und der Mensch holzt die im Kampf gegen 
                        die Klimakrise so wichtigen Regenwälder munter weiter ab. Aber nun ist es Zeit umzudenken! Politik, 
                        Wirtschaft und Gesellschaft müssen sofort handeln und so schnell und effizient wie möglich gegen die 
                        drohende Klimakatastrophe vorgehen. Wie das aus Sicht des <strong>WWF</strong> gelingen kann, bitte 
                        <a href='https://www.wwf.de/themen-projekte/klima-energie/' target='_blank'>hier weiter lesen</a>.",
                "Klimaschutz", "protect-climate"),
            new CharityCampaign(CharityProductManager::$PROTECT_DIVERSITY_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für Projekte des WWF zur Erhaltung der biologischen Vielfalt",
                "Der <strong>WWF</strong> ist die größte und einflussreichste Umweltorganisation in Deutschland. Wir wollen die weltweite Zerstörung der 
                        Natur und Umwelt stoppen und eine Zukunft gestalten, in der Mensch und Natur in Einklang miteinander leben. Unsere 
                        nationalen und internationalen Projekte tragen dazu bei, unsere Ziele zu erreichen und zu beweisen, dass Bewahrung 
                        und verantwortungsvolle Nutzung der natürlichen Lebensgrundlagen mit nachhaltiger wirtschaftlicher Entwicklung vereinbar 
                        sind. <a href='https://www.wwf.de/' target='_blank'>https://www.wwf.de/</a>",
                "Biologische Vielfalt", "protect-diversity"),
        ];
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
     * @return float
     */
    public static function getRevenueOfCampaignInTimeRange(string $campaignSlug,
                                                           \DateTime $startDate,
                                                           \DateTime $endDate): float
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
            /* @var $order Order */
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