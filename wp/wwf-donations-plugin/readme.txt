=== Wordpress Donations Plugin For WooCommerce ===
Contributors: Exxeta AG, Marius Schuppert
Donate link: https://github.com/EXXETA/wwf-plugin-donations
Tags: woocommerce, donation, charity, wwf
Requires at least: 5.3
Stable tag: 5.7
Tested up to: 5.8
Requires PHP: 7.4
License: GNU General Public License version 3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

**Plugin for WordPress to collect donations in a WooCommerce shop
for the non-profit organization [WWF](https://www.wwf.de/). Automatically sending mail reports.**


== Description ==

**Plugin for WordPress to collect donations in a WooCommerce shop
for the non-profit organization [WWF](https://www.wwf.de/). Automatically sending mail reports.**

**NOTE:** Currently this plugin is in German language available only.

### Features
- **Five donation campaigns** are officially supported
- Providing banner content for easy integration:
    - Shortcodes: `[wwf_donations_banner]` and `[wwf_donations_banner_small]`. See below for more information.
    - Equivalent Gutenberg editor blocks: `WWF-Spendenbanner`, `WWF-Spendenbanner (mini)`
- **Campaign reporting**
    - Automatic generation of reports in the following time ranges: *weekly*, *monthly*, *quarterly*
    - Automatic mail delivery to WWF Germany of report content
    - Reports are persisted and transparently accessible in custom backend section
    - Custom "live" report preview
- Easy integration into WooCommerce "Mini cart" widget
- No impact on privacy of customers of this plugin

== Installation ==

## Server and WordPress requirements
- PHP 7.4+
- MySQL/MariaDB 5.7+/10.3+
- Required active WordPress plugins:
    - `woocommerce`
- WordPress crons (= scheduled events) are set up and are running at least once a day.
- **Important:** WordPress should be able to send mails via `wp_mail`. If you are not sure about this,
e.g. check [this article](https://wphelp.de/wordpress-email) and test successful mail delivery.
- WooCommerce is (initially) set up
    - Currency: Euro


1. Be sure to meet the listed requirements for the web server and the WordPress installation, *before* you proceed.
2. Extract provided archive of this plugin to `wp-content/plugins` directory.
3. Install and activate this plugin in WordPress plugin management section.
4. Configure plugin settings in `Einstellungen > Spendenübersicht`.
5. Add the banner to your *WooCommerce* cart page by using a block (in "Gutenberg" editor)
   or as an alternative you can use a WordPress shortcode (documented below).
   Technically both ways lead to the same markup/result.
6. Read this documentation carefully to understand how this plugin is supposed to work.
7. You'll get regular donation reports and you *must* transfer the donated money to the [WWF Germany](https://www.wwf.de/).
More information (including bank account) is given in the plugin's dashboard.

**NOTE:** After plugin installation/(re-)activation there will be generated one first (probably empty) report.

== Frequently Asked Questions ==

= I want to have more information about this plugin =

[Read docs here](https://github.com/EXXETA/wwf-plugin-donations).

= I found a bug or I have a question =

[Create an issue here](https://github.com/EXXETA/wwf-plugin-donations/issues).

== Screenshots ==

Have a look [here](https://github.com/EXXETA/wwf-plugin-donations/tree/master/screenshots).

== Changelog ==

= 1.1.5 =
* Fix critical bug because of wrong method signature.
* Remove jQuery dependency and inline scripts (not yet in admin theme)
* Update dependencies.

= 1.1.4 =
* Update dependencies.

= 1.1.3 =
* add option to force style inclusion instead of detecting it dynamically.

= 1.1.0 =
* Mini Banner Version + Shortcode + Settings added

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
* Mini Banner Version + Shortcode + Settings added

= 1.0 =
Initial release