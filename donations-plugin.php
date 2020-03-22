<?php
/*
Plugin Name: WP-Donations-Plugin
Description: A Plugin to add a Donation Button to the Cart Page
Author: Heiko Scholz
Version: 0.1
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
register_activation_hook(__FILE__, 'wp_donations_plugin_activate');
register_deactivation_hook(__FILE__, 'wp_donations_plugin_deactivate');

function wp_donations_plugin_activate()
{
    if(empty(get_option("artenschutzeuro_id"))){
        add_menu_page('Test Plugin Page', 'Donations Plugin', 'manage_options', 'wp-donations-plugin', 'init_menu_page');
        $product = new WC_Product_Simple();
        $product->set_name('Artenschutz Euro');
        $product->set_slug('artenschutzeuro');
        $product->set_description('Spenden Euro für wohltätige Zwecke');
        $product->set_regular_price('1.00');
        $product->save();
        $product_id =$product->get_id();
        update_option("artenschutzeuro_id",$product_id);

    }


}

function wp_donations_plugin_deactivate()
{
    $product_id = get_option("artenschutzeuro_id");
    if (!empty($product_id)) {
        $product=wc_get_product($product_id);
        $product->delete();
    }
    delete_option("artenschutzeuro_id");

}

function init_menu_page()
{
    add_menu_page('Theme page title', 'Donationss Plugin', 'manage_options', 'theme-options', 'wps_theme_func');
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


?>