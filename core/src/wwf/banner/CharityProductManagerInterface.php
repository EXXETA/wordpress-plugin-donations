<?php

namespace exxeta\wwf\banner;


use exxeta\wwf\banner\model\CharityProduct;

/**
 * Interface CharityProductManager
 *
 * statically encapsulates most important methods of products offered by this plugin
 *
 * @package exxeta\wwf\banner
 */
interface CharityProductManagerInterface
{
    /**
     * @return string[]
     */
    public static function getAllCharityProductSlugs(): array;

    /**
     * @return CharityProduct[]
     */
    public static function getAllProducts(): array;

    /**
     * @return string
     */
    public static function getCategoryId(): string;

    /**
     * @return array|false|\WP_Term
     */
    public static function getCharityProductCategory();

    /**
     * method load product initially
     */
    public static function initProducts(): void;

    /**
     * method to get a specific product by its slug
     *
     * @param string $slug
     * @return CharityProduct|null
     */
    public static function getProductBySlug(string $slug): ?CharityProduct;

    /**
     * method to get a specific product id by its slug
     *
     * @param string $slug
     * @return int|null
     */
    public static function getProductIdBySlug(string $slug): ?int;
}