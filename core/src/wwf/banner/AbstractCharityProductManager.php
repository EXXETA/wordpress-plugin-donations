<?php

namespace exxeta\wwf\banner;

use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\CharityProduct;

/**
 * Class AbstractCharityProductManager
 *
 * A generic implementation of a CharityProductManagerInterface
 *
 * @package exxeta\wwf\banner
 */
abstract class AbstractCharityProductManager implements CharityProductManagerInterface
{
    // available product category
    private static $CHARITY_COINS_CATEGORY = "charity_coins";

    // field for all charity products
    /**
     * @var CharityProduct[]
     */
    private static $allProducts = [];

    /**
     * @var CharityCampaign[]
     */
    private static $allCampaigns = [];

    // available coin ids - no duplicates!
    public static $PROTECT_SPECIES_COIN = 'protect_species_coin';
    public static $PROTECT_OCEAN_COIN = 'protect_ocean_coin';
    public static $PROTECT_FOREST_COIN = 'protect_forest_coin';
    public static $PROTECT_CLIMATE_COIN = 'protect_climate_coin';
    public static $PROTECT_DIVERSITY_COIN = 'protect_diversity_coin';

    private static $genericFootNotesTextDE = "<br/>* Der gesamte Erlös der Umweltschutztaler kommt ausgewählten Projekten des WWF zugute. Bitte beachten Sie, dass die Ausstellung einer Spendenquittung nicht möglich ist.<br/>"
    . "<br/>** Der Umweltschutztaler ist kein physisches Produkt, sondern die Möglichkeit der digitalen Spende für den WWF. Eine Auslieferung und Rückerstattung des Umweltschutztalers ist daher nicht möglich.";

    /**
     * AbstractCharityProductManager constructor.
     */
    public function __construct()
    {
        $this->initCampaigns();
        $this->initProducts();
    }

    public function initCampaigns(): void
    {
        static::$allCampaigns = [
            new CharityCampaign(AbstractCharityProductManager::$PROTECT_SPECIES_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Artenschutzprojekte des WWF",
                "Wir befinden uns heute im größten Artensterben seit dem Ende der Dinosaurierzeit vor 65 Millionen Jahren. Dass Arten aussterben ist ein natürlicher Prozess, der jedoch heute unter dem Einfluss des Menschen beträchtlich beschleunigt ist. Wir sägen an dem Ast, auf dem wir sitzen. Nahrung, Medizin, Rohstoffe, sauberes Wasser und Luft sind nur einige der wichtigen Dinge, die die Natur uns zur Verfügung stellt. Es ist längst Zeit, zu handeln. Und es könnte bald zu spät sein. Die Mission des WWF ist wichtiger denn je: &quot;Bewahrung der biologischen Vielfalt – ein lebendiger Planet für uns und unsere Kinder&quot;. Für weitere Informationen bitte <a href='https://www.wwf.de/themen-projekte/artenschutz-und-biologische-vielfalt/' target='_blank'>&quot;hier&quot;</a> klicken" . '<br/>' . static::$genericFootNotesTextDE,
                "Gutes tun war noch nie so einfach", "1 € in den Warenkorb", "Artenschutz", "protect-species"),
            new CharityCampaign(AbstractCharityProductManager::$PROTECT_OCEAN_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Meeresprojekte des WWF",
                "Unser Planet ist blau – die Meere bedecken rund 70 Prozent der Erdoberfläche und sind von entscheidender Bedeutung für uns Menschen.<br/>Doch Überfischung, Verschmutzung oder der Abbau von Ressourcen haben Meeresökosysteme bereits großflächig zerstört und Bestände vieler Meerestierarten auf den niedrigsten Stand seit Menschengedenken schrumpfen lassen. Darüber hinaus sind die Meere zunehmend den dramatischen Folgen der Klimakrise ausgesetzt. Längst haben die zahlreichen Belastungen der Ozeane Ausmaße angenommen, die nicht nur die biologische Vielfalt der Erde, sondern auch unsere zukünftige Ernährung bedrohen.<br/>Deshalb setzt sich der <strong>WWF</strong> weltweit für einen respektvollen und nachhaltigen Umgang mit den Ökosystemen und natürlichen Ressourcen unserer Meere ein." . '<br/>' . static::$genericFootNotesTextDE,
                "Gutes tun war noch nie so einfach", "1 € in den Warenkorb", "Meeresschutz", "protect-oceans"),
            new CharityCampaign(AbstractCharityProductManager::$PROTECT_FOREST_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Waldprojekte des WWF",
                "Wälder bedecken knapp vier Mrd. Hektar und damit rund 30 Prozent der Landoberfläche der Erde. Vor 10.000 Jahren war es noch doppelt so viel. Obwohl auf der Nordhalbkugel sogar ein Nettozuwachs an Waldfläche verzeichnet wird, verschwinden global gesehen im Durchschnitt jedes Jahr mindestens 14 Millionen Hektar Wald, besonders in den Tropen. Das entspricht einer Fläche, die größer ist als Österreich und die Schweiz zusammengenommen. Besonders dramatisch ist der Rückgang der wertvollen Urwälder. Weltweit gibt es noch zwei bis drei Prozent Naturwälder. Wie der <strong>WWF</strong> schützen will, erfahrt ihr <a href='https://www.wwf.de/themen-projekte/waelder/' target='_blank'>hier</a>." . '<br/>' . static::$genericFootNotesTextDE,
                "Gutes tun war noch nie so einfach", "1 € in den Warenkorb", "Waldschutz", "protect-forests"),
            new CharityCampaign(AbstractCharityProductManager::$PROTECT_CLIMATE_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für die Klimaprojekte des WWF",
                "Dürren, Überflutungen, Stürme: Immer häufiger und heftiger führt der Klimawandel zu Tod und Verwüstung. Naturparadiese wie der Amazonas drohen weitreichend zerstört und der Hälfte ihrer Tier- und Pflanzenarten beraubt zu werden. Ikonische Tiere wie Große Pandas können genau wie zehntausende Pflanzen, Insekten und kleinere Lebewesen regional verschwinden. Deshalb hat sich Weltgemeinschaft 2015 mit dem Pariser Abkommen darauf geeinigt, die globale Erderhitzung auf deutlich unter 2 Grad Celsius, möglichst 1,5 Grad, zu beschränken.<br/>Trotzdem sinken die Treibhausgasemissionen nicht schnell genug und der Mensch holzt die im Kampf gegen die Klimakrise so wichtigen Regenwälder munter weiter ab. Aber nun ist es Zeit umzudenken! Politik, Wirtschaft und Gesellschaft müssen sofort handeln und so schnell und effizient wie möglich gegen die drohende Klimakatastrophe vorgehen. Wie das aus Sicht des <strong>WWF</strong> gelingen kann, bitte <a href='https://www.wwf.de/themen-projekte/klima-energie/' target='_blank'>hier weiter lesen</a>.<br/>" . static::$genericFootNotesTextDE,
                "Gutes tun war noch nie so einfach", "1 € in den Warenkorb", "Klimaschutz", "protect-climate"),
            new CharityCampaign(AbstractCharityProductManager::$PROTECT_DIVERSITY_COIN,
                "Erweitere deinen Warenkorb mit einer Spende für Projekte des WWF zur Erhaltung der biologischen Vielfalt",
                "Der <strong>WWF</strong> ist die größte und einflussreichste Umweltorganisation in Deutschland. Wir wollen die weltweite Zerstörung der Natur und Umwelt stoppen und eine Zukunft gestalten, in der Mensch und Natur in Einklang miteinander leben. Unsere nationalen und internationalen Projekte tragen dazu bei, unsere Ziele zu erreichen und zu beweisen, dass Bewahrung und verantwortungsvolle Nutzung der natürlichen Lebensgrundlagen mit nachhaltiger wirtschaftlicher Entwicklung vereinbar sind. <a href='https://www.wwf.de/' target='_blank'>https://www.wwf.de/</a><br/>" . static::$genericFootNotesTextDE,
                "Gutes tun war noch nie so einfach", "1 € in den Warenkorb", "Biologische Vielfalt", "protect-diversity"),
        ];
    }

    public function initProducts(): void
    {
        static::$allProducts = [
            new CharityProduct(static::$PROTECT_SPECIES_COIN, "Deine WWF-Spende (Artenschutz)", "Ein Euro für den Artenschutz", 1, "product_protect_species.png"),
            new CharityProduct(static::$PROTECT_OCEAN_COIN, "Deine WWF-Spende (Meeresschutz)", "Ein Euro für den Meeresschutz", 1, "product_protect_oceans.png"),
            new CharityProduct(static::$PROTECT_FOREST_COIN, "Deine WWF-Spende (Waldschutz)", "Ein Euro für den Waldschutz", 1, "product_protect_forest.png"),
            new CharityProduct(static::$PROTECT_CLIMATE_COIN, "Deine WWF-Spende (Klimaschutz)", "Ein Euro für den Erhalt des Klimas", 1, "product_protect_climate.png"),
            new CharityProduct(static::$PROTECT_DIVERSITY_COIN, "Deine WWF-Spende (Biologische Artenvielfalt)", "Ein Euro für die Erhaltung der biologischen Vielfalt", 1, "product_protect_diversity.png"),
        ];
    }

    /**
     * @return CharityCampaign[]
     */
    public function getAllCampaigns(): array
    {
        return static::$allCampaigns;
    }

    /**
     * method to get a campaign record by its slug
     *
     * @param string $slug
     * @return CharityCampaign|null
     */
    public function getCampaignBySlug(string $slug): ?CharityCampaign
    {
        foreach ($this->getAllCampaigns() as $singleCampaign) {
            /* @var $singleCampaign CharityCampaign */
            if ($slug === $singleCampaign->getSlug()) {
                return $singleCampaign;
            }
        }
        return null;
    }

    /**
     * @return string[]
     */
    public function getAllCharityProductSlugs(): array
    {
        return [
            static::$PROTECT_SPECIES_COIN,
            static::$PROTECT_OCEAN_COIN,
            static::$PROTECT_FOREST_COIN,
            static::$PROTECT_CLIMATE_COIN,
            static::$PROTECT_DIVERSITY_COIN,
        ];
    }

    /**
     * @return CharityProduct[]
     */
    public function getAllProducts(): array
    {
        return static::$allProducts;
    }

    /**
     * @return string
     */
    public function getCategoryId(): string
    {
        return static::$CHARITY_COINS_CATEGORY;
    }

    /**
     * @param string $slug
     * @return CharityProduct|null
     */
    public function getProductBySlug(string $slug): ?CharityProduct
    {
        foreach ($this->getAllProducts() as $singleProduct) {
            /* @var CharityProduct $singleProduct */
            if ($slug === $singleProduct->getSlug()) {
                return $singleProduct;
            }
        }
        return null;
    }

    /**
     * @param string $slug
     * @param string $settingManager
     * @return int|null
     */
    public function getProductIdBySlug(string $slug, string $settingManager): ?int
    {
        foreach ($this->getAllProducts() as $singleProduct) {
            /* @var $singleProduct CharityProduct */
            if ($singleProduct->getSlug() === $slug) {
                $productId = call_user_func($settingManager . '::' . 'getSetting', $singleProduct->getProductIdSettingKey(), null);
                if ($productId > 0) {
                    return $productId;
                } else {
                    return null;
                }
            }
        }
        return null;
    }

    /**
     * values correspond to charity coin product slug
     *
     * @return array|string[]
     */
    public function getAllCampaignTypes(): array
    {
        return $this->getAllCharityProductSlugs();
    }
}