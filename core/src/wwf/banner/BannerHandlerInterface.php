<?php


namespace exxeta\wwf\banner;

use exxeta\wwf\banner\model\CharityProduct;

/**
 * Interface BannerHandlerInterface
 *
 * This is a central data structure for the regular (big) banner and the mini banner.
 *
 * @package exxeta\wwf\banner
 */
interface BannerHandlerInterface
{
    /**
     * Method to get the (full) URL to the coin logo associated to the campaign.
     * Called in {@link Banner} and in {@link MiniBanner} class.
     *
     * @param CharityProduct $charityProduct
     * @return string
     */
    public function getLogoImageUrl(CharityProduct $charityProduct): string;

    /**
     * Method to get the (full) URL to the campaign banner image.
     * Called in {@link Banner} class only.
     *
     * @return string
     */
    public function getCartImageUrl(): string;

    /**
     * Method to get the shop-specific product id associated with a charity product.
     *
     * @param CharityProduct $charityProduct
     * @return int
     */
    public function getProductId(CharityProduct $charityProduct): int;

    /**
     * Method to get the base url (of this plugin)
     *
     * TODO replace/remove this method?
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Method to get the full URL to the cart page of the current shop.
     *
     * @return string
     */
    public function getCartUrl(): string;

    /**
     * Method to get the target page url where the "more info" link of the mini banner will link to.
     * Should be a full URL.
     *
     * @param $pageId
     * @return string
     */
    public function getMiniBannerTargetPageUrl($pageId): string;

    /**
     * Method to get the page id of the cart in the current shop system. Very shop-specific.
     *
     * @return int
     */
    public function getCartPageId(): int;

    /**
     * this hook method is used to add the concrete banner content
     *
     * @param string $output
     * @param CharityProduct $charityProduct
     */
    public function applyMiniBannerCartRowHook(string &$output, CharityProduct $charityProduct): void;

    /**
     * this hook method can be used to customize the inner form elements (e.g. to add hidden inputs or csrf tokens..)
     *
     * @param $output string is passed by reference!
     * @param $charityProduct CharityProduct
     */
    public function applyCartFormHook(&$output, CharityProduct $charityProduct): void;

    /**
     * this will correspond to the quantity input name attribute of the banner form
     *
     * @return string
     */
    public function getFormQuantityInputName(): string;
}