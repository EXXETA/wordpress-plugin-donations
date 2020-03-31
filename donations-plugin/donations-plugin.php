<?php
/**
 * Plugin Name: WP-Donations-Plugin
 * Plugin URI: https://github.com/EXXETA/wordpress-plugin-donations
 * Description: A plugin to add a donation button to the cart page and integrating it into an existing WooCommerce wordpress setup
 * Author: Marius Schuppert
 * Version: 0.1
 * License: GNU General Public License version 3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('BASE_PATH', plugin_dir_path(__FILE__));
require_once BASE_PATH . 'vendor/autoload.php';

use donations\Plugin;

$plugin = new Plugin(__FILE__);
$plugin->registerPluginHooks();

/* FIXME review and integrate code below */
//function init_menu_page()
//{
//    add_menu_page('Theme page title', 'Donations Plugin', 'manage_options', 'theme-options', 'wps_theme_func');
//    add_submenu_page('theme-options', 'Settings', 'Settings menu label', 'manage_options', 'theme-op-settings', 'init_settings');
//    add_submenu_page('theme-options', 'Overview', 'Overview menu label', 'manage_options', 'theme-op-faq', 'init_overview');
//}
//
//function wps_theme_func()
//{
//    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
//        <h2>Donations Plugin</h2></div>';
//}
//
//function init_settings()
//{
//    echo '
//        <div class="wrap">
//            <h2>Welcome To My Plugin</h2>
//            <label for="organizations">Wähle eine Spendenaktion</label>
//            <select id="organizations">
//                <option value="Artenschutz">Artenschutz</option>
//                <option value="Meeresschutz (Meeresschutzeuro)">Meeresschutz (Meeresschutzeuro)</option>
//                <option value="Waldschutz (Waldschutzeuro)">Waldschutz (Waldschutzeuro)</option>
//                <option value="Artenschutz (Artenschutzeuro)">Artenschutz (Artenschutzeuro)</option>
//                <option value="Kinder- und Jugendarbeit (Kinder- und Jugendarbeiteuro)">Kinder- und Jugendarbeit (Kinder- und Jugendarbeiteuro)</option>
//                <option value="Klimaschutz (Klimaschutzeuro)">Klimaschutz (Klimaschutzeuro)</option>
//                <option value="Bewahrung der biologischen Vielfalt (Biologischer Vielfaltseuro)">Bewahrung der biologischen Vielfalt (Biologischer Vielfaltseuro)</option>
//            </select>
//        </div>';
//}
//
//function init_overview()
//{
//    echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
//        <h2>FAQ</h2></div>';
//}