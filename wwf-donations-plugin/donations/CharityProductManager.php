<?php

namespace donations;

use exxeta\wwf\banner\AbstractCharityProductManager;

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
     * @return array|false|\WP_Term
     */
    public static function getCharityProductCategory()
    {
        return get_term_by('slug', static::getCategoryId(), static::getWooProductCategoryTaxonomy());
    }

    /**
     * @return string
     */
    public static function getWooProductCategoryTaxonomy(): string
    {
        return self::$WC_PRODUCT_CATEGORY_TAXONOMY;
    }
}

