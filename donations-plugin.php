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
register_activation_hook( __FILE__, 'wp_donations_plugin_setup' );

function wp_donations_plugin_setup()
{
    add_menu_page('Test Plugin Page', 'Test Plugin', 'manage_options', 'wp-donations-plugin', 'test_init');
    add_submenu_page('wp-donations-plugin', 'Settings', 'Whatever You Want', 'manage_options', 'my-menu' );
    add_submenu_page('wp-donations-plugin', 'Donations Overview', 'Whatever You Want2', 'manage_options', 'my-menu2' );
    $product = new WC_Product_Simple();
    $product->set_name( 'Artenschutz Euro' );
    $product->set_slug( 'artenschutzeuro' );
    $product->set_description( 'Spenden Euro fuer wohltaetige Zwecke' );
    $product->set_regular_price( '1.00' );
    $product->save();


}

function test_init(){
        echo '
        
        <div class="wrap">
            <h2>Welcome To My Plugin</h2>
            <label for="spendenaktion">Wähle eine Spendenaktion</label>
            <select id="organizations">
                <option value="Artenschutz">Volvo</option>
                <option value="Meeresschutz (Meeresschutzeuro)">Saab</option>
                <option value="Waldschutz (Waldschutzeuro)"></option>
                <option value="Artenschutz (Artenschutzeuro)">Saab</option>
                <option value="Kinder- und Jugendarbeit (Kinder- und Jugendarbeiteuro)">Saab</option>
                <option value="Klimaschutz (Klimaschutzeuro)">Mercedes</option>
                <option value="Bewahrung der biologischen Vielfalt (Biologischer Vielfaltseuro)">Audi</option>
            </select>
        </div>
        ';
}


?>