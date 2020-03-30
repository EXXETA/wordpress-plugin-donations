<?php

namespace donations;

/**
 * Class CharityProductManager
 *
 * statically encapsulates most important methods of products offered by this plugin
 *
 * @package donations
 */
class CharityProductManager
{
    // available coin ids - no duplicates!
    private static $PROTECT_SPECIES_COIN = 'protect_species_coin';
    private static $PROTECT_OCEAN_COIN = 'protect_ocean_coin';
    private static $PROTECT_FOREST_COIN = 'protect_forest_coin';
    private static $PROTECT_CHILDREN_YOUTH_COIN = 'protect_children_youth_coin';
    private static $PROTECT_CLIMATE_COIN = 'protect_climate_coin';
    private static $PROTECT_DIVERSITY_COIN = 'protect_diversity_coin';
    // available product category
    private static $CHARITY_COINS_CATEGORY = "charity_coins";
    // WoCommerce default taxonomy in wordpress for product categories
    // this should not be changed
    private static $WC_PRODUCT_CATEGORY_TAXONOMY = "product_cat";
    // field for all charity products
    private static $allProducts = [];

    /**
     * @return string[]
     */
    public static function getAllProductIds(): array
    {
        return [
            self::$PROTECT_SPECIES_COIN,
            self::$PROTECT_OCEAN_COIN,
            self::$PROTECT_FOREST_COIN,
            self::$PROTECT_CHILDREN_YOUTH_COIN,
            self::$PROTECT_CLIMATE_COIN,
            self::$PROTECT_DIVERSITY_COIN,
        ];
    }

    /**
     * @return CharityProduct[]
     */
    public static function getAllProducts(): array
    {
        if (count(self::$allProducts) === 0) {
            // one-time init of products
            self::initProducts();
        }
        return self::$allProducts;
    }

    /**
     * @return string
     */
    public static function getCategoryId(): string
    {
        return self::$CHARITY_COINS_CATEGORY;
    }

    /**
     * @return string
     */
    public static function getWooProductCategoryTaxonomy(): string
    {
        return self::$WC_PRODUCT_CATEGORY_TAXONOMY;
    }

    /**
     * @return array|false|\WP_Term
     */
    public static function getCharityProductCategory()
    {
        return get_term_by('slug', CharityProductManager::getCategoryId(), CharityProductManager::getWooProductCategoryTaxonomy());
    }

    private static function initProducts()
    {
        self::$allProducts = [
            new CharityProduct(self::$PROTECT_SPECIES_COIN, "Artenschutzeuro", "Ein Euro für den Artenschutz", 1),
            new CharityProduct(self::$PROTECT_OCEAN_COIN, "Meeresschutzeuro", "Ein Euro für den Meeresschutz", 1),
            new CharityProduct(self::$PROTECT_FOREST_COIN, "Waldschutzeuro", "Ein Euro für den Waldschutz", 1),
            new CharityProduct(self::$PROTECT_CHILDREN_YOUTH_COIN, "Kinder- und Jugendschutzeuro", "Ein Euro für den Kinder- und Jugendschutz", 1),
            new CharityProduct(self::$PROTECT_CLIMATE_COIN, "Klimaerhaltungseuro", "Ein Euro für den Erhalt des Klimas", 1),
            new CharityProduct(self::$PROTECT_DIVERSITY_COIN, "Biologischer Vielfaltseuro", "Ein Euro für die Erhaltung der biologischen Vielfalt", 1),
        ];
    }
}

