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
                "Hey Honey!<br>Spende den Artenschutzeuro für Projekte des WWF*",
                "Die Zahlen des Artenschutzberichtes des Weltbiodiversitätsrats IPBES sind alarmierend: Rund eine Million Arten könnten innerhalb 
						der nächsten Jahrzehnte verschwinden, wenn sich der Zustand unserer Ökosysteme weiterhin verschlechtert. Auch die Weltnaturschutzunion IUCN konstatiert den Arten mit ihrer  
                        <a href='https://www.wwf.de/themen-projekte/weitere-artenschutzthemen/rote-liste-gefaehrdeter-arten/' target='_blank'>&quot;Internationalen Roten Liste&quot;</a> Schlimmes: Ein Viertel der Säugetierarten, jede achte Vogelart, mehr als 30 Prozent der Haie und Rochen sowie 40 Prozent der Amphibienarten sind bedroht. 
						<p><br>&quot;Im Yoga geht es darum, Einheit in der Vielfalt zu finden. Und wenn wir Tierformen einnehmen, dann spüren wir eigentlich, wie es diesen Tieren geht. Wir entwickeln Empathie, wir haben Mitgefühl und wollen, dass es nicht nur uns, sondern auch allen anderen Mitbewohnern auf diesem Planeten gut geht.“ - Patrick Broome&quot;
						<p><br>Gemeinsam mit dir möchten wir einen Beitrag zum Artenschutz leisten. Hilf mit deiner Spende die biologische Vielfalt auf unserem Planten zu erhalten! 
						<p><br>*Der gesamte Erlös der Spende kommt ausgewählten Artenschutzprojekten des WWF zugute. Bitte beachte, dass die Ausstellung einer Spendenquittung nicht möglich ist.
						<p><br>**Bitte beachte: Der WWF Euro ist kein physisches Produkt, sondern die Möglichkeit der digitalen Spende für den WWF. Eine Auslieferung und Rückerstattung des WWF Euros ist daher nicht möglich",
                "Yoga für Artenschutz und biologische Vielfalt","1 € In den Warenkorb","Artenschutz HeyHoney DE", "protect-species"),
            new CharityCampaign(CharityProductManager::$PROTECT_SPECIES_COIN,
                "Hey Honey!<br>donate the “Artenschutzeuro” to support the WWF*",
                "The statistics published in the Conservation Report created by the Intergovernmental Science-Policy Platform on Biodiversity and 
						Ecosystem Services (IPBES) are truly shocking. It is expected that about one million animal species will become extinct within the next decades, if the condition of our ecosystems will continue to worsen. Also the International Union for Conservation of Nature IUCN published drastics facts regarding endangered species on their  
                        <a href='https://www.wwf.de/themen-projekte/weitere-artenschutzthemen/rote-liste-gefaehrdeter-arten/' target='_blank'>&quot;Red List&quot;</a> 
						report: one quarter of all mammals, every 8th bird species, more than 30 percents of all sharks and skates as well as 40 percent of all Amphibian species are threatened to die out.
						<p><br>&quot;Yoga is about finding unity in diversity.  When we take on the shapes of  animals, we create a connection with them and actually feel how they are feeling. We develop empathy, we have compassion and we long for the well-being of all lifes on this planet.“ - Patrick Broome&quot;
						<p><br>Let’s make a contribution for the protection of endangered species together. Support the biological diversity of our planet with your donation! 
						<p><br>*The entire revenue of your donation will be used for selected projects initiated from the WWF to support the protection of endangered animal species. Please note that it is not possible to receive a donation receipt.
						<p><br>**Please note: The WWF Euro is not a physical product, but a digital donation. Therefore, the delivery and refund of the WWF is not possible",
                "Yoga for the protection of endangered animal species and biological diversity","1 € into the shopping cart","Artenschutz HeyHoney EN", "protect-species"),
            new CharityCampaign(CharityProductManager::$PROTECT_SPECIES_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Artenschutzprojekte des WWF",
                "Wir befinden uns heute im größten Artensterben seit dem Ende der Dinosaurierzeit vor 65 Millionen Jahren. Dass Arten aussterben ist ein natürlicher Prozess, der jedoch heute unter dem Einfluss des Menschen beträchtlich beschleunigt ist. Wir sägen an dem Ast, auf dem wir sitzen. Nahrung, Medizin, Rohstoffe, sauberes Wasser und Luft sind nur einige der wichtigen Dinge, die die Natur uns zur Verfügung stellt. Es ist längst Zeit, zu handeln. Und es könnte bald zu spät sein. Die Mission des WWF ist wichtiger denn je: &quot;Bewahrung der biologischen Vielfalt – ein lebendiger Planet für uns und unsere Kinder&quot;. Für weitere Informationen bitte <a href='https://www.wwf.de/themen-projekte/artenschutz-und-biologische-vielfalt/' target='_blank'>&quot;hier&quot;</a> klicken", 
				"Gutes zu tun war noch nie so einfach", "1 € In den Warenkorb", "Artenschutz HeyHoney DE", "Artenschutz", "protect-species"),
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
                "Gutes zu tun war noch nie so einfach", "1 € In den Warenkorb", "Meeresschutz", "protect-oceans"),
            new CharityCampaign(CharityProductManager::$PROTECT_FOREST_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Waldprojekte des WWF",
                "Wälder bedecken knapp vier Mrd. Hektar und damit rund 30 Prozent der Landoberfläche der Erde. Vor 10.000 Jahren war 
                        es noch doppelt so viel. Obwohl auf der Nordhalbkugel sogar ein Nettozuwachs an Waldfläche verzeichnet wird, verschwinden 
                        global gesehen im Durchschnitt jedes Jahr mindestens 14 Millionen Hektar Wald, besonders in den Tropen. Das entspricht einer 
                        Fläche, die größer ist als Österreich und die Schweiz zusammengenommen. Besonders dramatisch ist der Rückgang der wertvollen 
                        Urwälder. Weltweit gibt es noch zwei bis drei Prozent Naturwälder. Wie der <strong>WWF</strong> schützen will, erfahrt ihr 
                        <a href='https://www.wwf.de/themen-projekte/waelder/' target='_blank'>hier</a>.",
                "Gutes zu tun war noch nie so einfach", "1 € In den Warenkorb", "Waldschutz", "protect-forests"),
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
                "Gutes zu tun war noch nie so einfach", "1 € In den Warenkorb", "Klimaschutz", "protect-climate"),
            new CharityCampaign(CharityProductManager::$PROTECT_DIVERSITY_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für Projekte des WWF zur Erhaltung der biologischen Vielfalt",
                "Der <strong>WWF</strong> ist die größte und einflussreichste Umweltorganisation in Deutschland. Wir wollen die weltweite Zerstörung der 
                        Natur und Umwelt stoppen und eine Zukunft gestalten, in der Mensch und Natur in Einklang miteinander leben. Unsere 
                        nationalen und internationalen Projekte tragen dazu bei, unsere Ziele zu erreichen und zu beweisen, dass Bewahrung 
                        und verantwortungsvolle Nutzung der natürlichen Lebensgrundlagen mit nachhaltiger wirtschaftlicher Entwicklung vereinbar 
                        sind. <a href='https://www.wwf.de/' target='_blank'>https://www.wwf.de/</a>",
                "Gutes zu tun war noch nie so einfach", "1 € In den Warenkorb", "Biologische Vielfalt", "protect-diversity"),
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
                        $isAffected = true;
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