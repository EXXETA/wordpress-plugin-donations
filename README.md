# WWF Donation Plugins

**Online shop plugins to collect donations for the non-profit organization [WWF Germany](https://www.wwf.de/).
Automatically sending mail reports.**

**NOTE:** Currently all these plugins are in German language available only.

Do you have questions, want to request a feature or do you found a
bug? [-> Create issues!](https://github.com/EXXETA/wordpress-plugin-donations/issues)

### General Features

- **Five donation campaigns** are officially supported
- Providing banner content in two variants (small and normal) for easy integration into your shop.
- **Campaign reporting**
    - Automatic generation of reports in the following time ranges: *weekly*, *monthly*, *quarterly*
    - Automatic mail delivery to WWF Germany of report content
    - Reports are persisted and transparently accessible in custom backend section
    - Custom "live" report preview
- No impact on privacy of customers by using this plugin

## General Requirements and Assumptions

- PHP 7.3+
- MySQL/MariaDB 5.7+/10.3+
- Currency: Euro
- **General tested minimum screen width:** `320px`
- **Maximum screen width:** `4k+`
- Main banner main class: `.cart-donation-banner`
- Small banner main class: `.cart-donation-mini-banner`
- SCSS styles: `assets/styles/banner.scss`

# Plugin's functionality

### Campaigns/Products

This plugin supports five different donation campaigns:

- Protecting **species**
- Protecting **oceans**
- Protecting **forests**
- Protecting **climate**
- Protecting **diversity**

### Reports

In general, you can configure three different report interval modes: `weekly`, `monthly`, `quarterly`.

Each report will be send by mail to an address you can view, but not change in plugin's backend section.

Technically the report generation is a summation of plugin's donation products in completed or processed orders grouped
by a donation campaign in a certain time range.

All reports are persisted as a private custom post type integrated into the WordPress system.

One time per day, a routine will check if the time to generate a new report has been reached. You can view the date and
time of the last check in plugin's report dashboard.

### Plugin's settings

This plugin enables you to modify certain aspects of the donations campaign integration:

- **Reporting interval:** {weekly, monthly, quarterly}
- **Report preview default days in past:** This can be overwritten in the preview section.
- (readonly) **Recipient's mail address:** You can't change this value and you must not.
- **Show Mini-Banner in Mini-Cart:**\* yes/no flag.
- **Campaign of mini-banner:**\* Default is "protect diversity"
- **Target page of "more information" link in Mini-Banner:**\* Default is the "Cart page".

\* If you change these settings, in order to see the effects, you need to clear your browser's cookies and the local
storage one time. E.g. by doing the following (in Chromium Browser):
`Dev Tools -> Application -> Local Storage -> Delete the entry which is prefixed by 'wc_cart_hash...' and reload the page`
.

# Development

### Core Packages

- [PHP Banner core library](./core/README.md)
- [Common image and style asset library](./assets/README.md)

### Shop-Plugins

- [Wordpress + WooCommerce plugin](./wp/README.md)

## Setup - Installation - Release

There is a [`setup.sh`](./setup.sh) to create a development environment initially.

In addition there is a `release.sh` to build the plugins.

Execute `release.sh` in this repository to get a production-ready distributable .zip-archive of this plugin. Change
version number in:

- `wp/wwf-donations-plugin/wwf-donations-plugin.php`
- `wp/wwf-donations-plugin/package.json` + `npm i`
- `wp/wwf-donations-plugin/composer.json` + `composer u`
- `core/composer.json` + `composer u`
- Add changelog messages to the bottom of `wp/wwf-donations-plugin/readme.txt`

## Requirements for development

- PHP Language Level 7.3+
- MySQL/MariaDB 5.7+/10.3+
- `docker` and `docker-compose`
- Composer for PHP
- `npm`

# Plugin Development

## TODO

- Multi-Language Support/I18N
- Add copyright header to source files
- Test with other themes
- Test without WooCommerce

# License & Copyright

All images in `wp/wwf-donations-plugin/images/` are explicitly excluded of the licensing policy mentioned below.

This plugin is licensed under [GPL v3.0](./LICENSE).