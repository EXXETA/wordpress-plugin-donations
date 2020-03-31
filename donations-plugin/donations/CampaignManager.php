<?php


namespace donations;

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
        self::$allCampaigns = [
            new CharityCampaign(CharityProductManager::$PROTECT_SPECIES_COIN, "Erweitere deinen Warenkorb mit einer Spende für die Artenschutzprojekte des WWF", "https://www.wwf.org/species"),
            new CharityCampaign(CharityProductManager::$PROTECT_OCEAN_COIN, "Erweitere deinen Warenkorb mit einer Spende für die Meeresprojekte des WWF", "https://www.wwf.org/ocean"),
            new CharityCampaign(CharityProductManager::$PROTECT_FOREST_COIN, "Erweitere deinen Warenkorb mit einer Spende für die Waldprojekte des WWF", "https://www.wwf.org/forest"),
            new CharityCampaign(CharityProductManager::$PROTECT_CHILDREN_YOUTH_COIN, "Erweitere deinen Warenkorb mit einer Spende für die Kinder- und Jugendschutzprojekte des WWF", "https://www.wwf.org/children"),
            new CharityCampaign(CharityProductManager::$PROTECT_CLIMATE_COIN, "Erweitere deinen Warenkorb mit einer Spende für die Klimaprojekte des WWF", "https://www.wwf.org/climate"),
            new CharityCampaign(CharityProductManager::$PROTECT_DIVERSITY_COIN, "Erweitere deinen Warenkorb mit einer Spende für Projekte des WWF zur Erhaltung der biologischen Vielfalt", "https://www.wwf.org/bio"),
        ];
    }
}