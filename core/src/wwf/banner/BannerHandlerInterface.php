<?php
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


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
     * @return string
     */
    public function getProductId(CharityProduct $charityProduct): string;

    /**
     * Method to get the base url (of this plugin)
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
     * method to provide a form method, either GET or POST or other methods browsers are supporting
     *
     * @return string
     */
    public function getFormMethod(): string;

    /**
     * option to add additional shop-specific form attributes besides action and method, e.g. 'enctype'.
     * Leave it blank if this is not needed.
     *
     * @return string
     */
    public function getFormAttributes(): string;

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