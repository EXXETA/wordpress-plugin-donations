<?php
/*
Plugin Name: WP-Donations-Plugin
Plugin URI: https://github.com/EXXETA/wordpress-plugin-donations
Description: A plugin to add a donation button to the cart page and integrating it into an existing WooCommerce wordpress setup
Author: Heiko Scholz
Version: 0.1
License: TODO
*/

define('BASE_PATH', plugin_dir_path(__FILE__));
require BASE_PATH . 'vendor/autoload.php';

use donations\CharityProduct;
use donations\CharityProductManager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

register_activation_hook(__FILE__, 'wp_donations_plugin_activate');
register_deactivation_hook(__FILE__, 'wp_donations_plugin_deactivate');

/**
 * this is called by wordpress if the plugin is activated
 * This is the place to init all products at once and store their WooCommerce product IDs in wordpress options.
 */
function wp_donations_plugin_activate()
{
    // add plugin pages
    add_menu_page('Test Plugin Page', 'Donations Plugin', 'manage_options', 'wp-donations-plugin', 'init_menu_page');
    // init all known products and store their IDs

    $result = CharityProductManager::getCharityProductCategory();
    $termId = null;
    if (!$result instanceof WP_Term) {
        // create it
        $result = wp_insert_term("Spendemünzen", CharityProductManager::getWooProductCategoryTaxonomy(), [
            'description' => 'Kategorie für Charity Coins',
            'parent' => 0,
            'slug' => CharityProductManager::getCategoryId(),
        ]);
        $termId = $result['term_id'];
    } else {
        $termId = $result->term_id;
    }
    if (!$termId) {
        error_log("Could not create default coin category");
    }
    // add default products
    foreach (CharityProductManager::getAllProducts() as $singleProduct) {
        /* @var $singleProduct CharityProduct */
        if (empty(get_option($singleProduct->getProductIdOptionKey()))) {
            $product = new WC_Product_Simple();
            $product->set_name($singleProduct->getName());
            $product->set_slug($singleProduct->getSlug());
            $product->set_description($singleProduct->getDescription());
            // prices
            $product->set_regular_price($singleProduct->getPrice());
            $product->set_sale_price($singleProduct->getPrice());
            // disable stock management
            $product->set_manage_stock(false);
            $product->set_sold_individually(true);
            // disable reviews
            $product->set_reviews_allowed(false);
            // set category id
            $product->set_category_ids([$termId]);
            $product->set_virtual(true);
            $product->save();

            $product_id = $product->get_id();
            update_option($singleProduct->getProductIdOptionKey(), $product_id);
        }
    }
}

/**
 * this is called if the plugin is disabled
 */
function wp_donations_plugin_deactivate()
{
    // remove products
    foreach (CharityProductManager::getAllProducts() as $singleProduct) {
        /* @var $singleProduct \donations\CharityProduct */
        $product_id = get_option($singleProduct->getProductIdOptionKey());
        if (!empty($product_id)) {
            // delete product
            $product = wc_get_product($product_id);
            $product->delete();
        }
        // delete option
        delete_option($singleProduct->getProductIdOptionKey());
    }

    // remove woo commerce product category which is technically a wordpress term
    $result = CharityProductManager::getCharityProductCategory();
    if ($result instanceof WP_Term) {
        wp_delete_term($result->term_id, CharityProductManager::getWooProductCategoryTaxonomy());
    }
}

function init_menu_page()
{
    add_menu_page('Theme page title', 'Donations Plugin', 'manage_options', 'theme-options', 'wps_theme_func');
    add_submenu_page('theme-options', 'Settings', 'Settings menu label', 'manage_options', 'theme-op-settings', 'init_settings');
    add_submenu_page('theme-options', 'Overview', 'Overview menu label', 'manage_options', 'theme-op-faq', 'init_overview');
}


function wps_theme_func()
{
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
        <h2>Donations Plugin</h2></div>';
}

function init_settings()
{
    echo '
        <div class="wrap">
            <h2>Welcome To My Plugin</h2>
            <label for="organizations">Wähle eine Spendenaktion</label>
            <select id="organizations">
                <option value="Artenschutz">Artenschutz</option>
                <option value="Meeresschutz (Meeresschutzeuro)">Meeresschutz (Meeresschutzeuro)</option>
                <option value="Waldschutz (Waldschutzeuro)">Waldschutz (Waldschutzeuro)</option>
                <option value="Artenschutz (Artenschutzeuro)">Artenschutz (Artenschutzeuro)</option>
                <option value="Kinder- und Jugendarbeit (Kinder- und Jugendarbeiteuro)">Kinder- und Jugendarbeit (Kinder- und Jugendarbeiteuro)</option>
                <option value="Klimaschutz (Klimaschutzeuro)"Klimaschutz (Klimaschutzeuro)</option>
                <option value="Bewahrung der biologischen Vielfalt (Biologischer Vielfaltseuro)">Bewahrung der biologischen Vielfalt (Biologischer Vielfaltseuro)</option>
            </select>
        </div>';
}

function init_overview()
{
    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
        <h2>FAQ</h2></div>';
}