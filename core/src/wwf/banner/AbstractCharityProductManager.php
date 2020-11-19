<?php

namespace exxeta\wwf\banner;

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
    private static $allProducts = [];

    // available coin ids - no duplicates!
    public static $PROTECT_SPECIES_COIN = 'protect_species_coin';
    public static $PROTECT_OCEAN_COIN = 'protect_ocean_coin';
    public static $PROTECT_FOREST_COIN = 'protect_forest_coin';
    public static $PROTECT_CLIMATE_COIN = 'protect_climate_coin';
    public static $PROTECT_DIVERSITY_COIN = 'protect_diversity_coin';

    /**
     * @return string[]
     */
    public static function getAllCharityProductSlugs(): array
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
    public static function getAllProducts(): array
    {
        if (count(static::$allProducts) === 0) {
            // one-time init of products
            static::initProducts();
        }
        return static::$allProducts;
    }

    /**
     * @return string
     */
    public static function getCategoryId(): string
    {
        return static::$CHARITY_COINS_CATEGORY;
    }

    public static function initProducts(): void
    {
        static::$allProducts = [
            new CharityProduct(static::$PROTECT_SPECIES_COIN, "Deine WWF-Spende (Artenschutz)", "Ein Euro für den Artenschutz", 1, "product_protect_species.png"),
            new CharityProduct(static::$PROTECT_OCEAN_COIN, "Deine WWF-Spende (Meeresschutz)", "Ein Euro für den Meeresschutz", 1, "product_protect_oceans.png"),
            new CharityProduct(static::$PROTECT_FOREST_COIN, "Deine WWF-Spende (Waldschutz)", "Ein Euro für den Waldschutz", 1, "product_protect_forest.png"),
            new CharityProduct(static::$PROTECT_CLIMATE_COIN, "Deine WWF-Spende (Klimaschutz)", "Ein Euro für den Erhalt des Klimas", 1, "product_protect_climate.png"),
            new CharityProduct(static::$PROTECT_DIVERSITY_COIN, "Deine WWF-Spende (Biologische Artenvielfalt)", "Ein Euro für die Erhaltung der biologischen Vielfalt", 1, "product_protect_diversity.png"),
        ];
    }

    public static function getProductBySlug(string $slug): ?CharityProduct
    {
        foreach (static::getAllProducts() as $singleProduct) {
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
    public static function getProductIdBySlug(string $slug, string $settingManager): ?int
    {
        foreach (static::getAllProducts() as $singleProduct) {
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
}