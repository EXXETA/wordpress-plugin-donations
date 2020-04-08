# wp-donations-plugin

**Plugin for Wordpress to collect donations in a WooCommerce shop 
for a non-profit organization. Automatically sending reports via mail.**
 
**NOTE:** Currently this plugin is in German language available only.

### Features
- Six donation products/campaigns
- Providing easy banner content (for cart page):
    - Shortcode: `[wp_donations_banner]`
    - Gutenberg Block: `Spendemünzen`
- Campaign reports
    - Automatically generating reports
    - mail delivery of report content
    - Reports are persistent and transparently accessible in custom backend section
    - Three interval modes: *weekly*, *monthly*, *quarterly*
    - Custom report live preview
- No privacy impact of this plugin

# Installation
1. Be sure to meet the listed requirements for the web server and the wordpress installation.
2. Extract provided archive of this plugin to `wp-content/plugins` directory.
3. Activate this plugin in Wordpress plugin page.
4. Configure plugin settings in `Settings > Donations`. 

# Setup
- Install and activate this plugin in your wordpress installation.
- Add the banner to your *WooCommerce* cart page by using a block (in "Gutenberg" editor) 
or use a shortcode (documented below). Technically both ways lead to the same result.

### Products
This plugin will create one new product category in a WooCommerce shop called "Spendemünzen" containing
six donation products (à 1 €) for different campaigns:
- Protecting species
- Protecting oceans
- Protecting forests
- Protecting children and youth
- Protecting climate
- Protecting diversity

You can view the WooCommerce product IDs in plugin report dashboard page in backend.

#### Product details
- virtual
- no stock management
- no taxation (`tax_status='none'`)
- not sold individually
- no reviews allowed
- `catalog_visibility='hidden'`

### Shortcode `wp_donations_banner`
You can configure the target donation campaign by providing a `campaign` argument. If no campaign
is given, the *default* one is: `protect_species_coin`.

Valid values for `campaign` argument are:
- `protect_species_coin`
- `protect_ocean_coin`
- `protect_forest_coin`
- `protect_children_youth_coin`
- `protect_climate_coin`
- `protect_diversity_coin`

Example of shortcode usage: `[wp_donations_banner campaign='protect_climate_coin']`

### Reports
You can configure three different report interval modes: `weekly`, `monthly`, `quarterly`.

Each report will be send by mail to an address you can view,
 but not change in plugin's backend section.
 
Technically the report generation is a summation of plugin's donation products in completed 
or processed orders grouped by a donation campaign in a certain time range.

All reports are persisted as a private custom post type integrated into the Wordpress system.

One time per day, a routine will check if time to generate a new report is reached. You can view the 
date and time of the last check in plugin's report dashboard.

**NOTE:** After plugin installation there will be generated one first (probably empty) report.

### Banner styling
This plugin is shipped with self-contained responsive CSS styles without dependencies to 
a specific theme or framework.

Banner main class: `.cart-donation-banner`

## Server and Wordpress requirements
- PHP 7.3+
- MySQL/MariaDB 5.7+/10.3+
- Required active Wordpress plugins:
    - `woocommerce`
    - `woocommerce-services`
- Wordpress crons (= scheduled events) are set up
- Wordpress should be able to send mails via `wp_mail`
- WooCommerce is (initially) set up
    - Currency: Euro
- Minimum screen width: `320px`

# Plugin Development

This repository contains a docker-compose configuration for a reproducible
environment during development. The directory `./donations-plugin` is mounted into the
wordpress container.

## Requirements for development
- PHP Language Level 7.3+
- MySQL/MariaDB 5.7+/10.3+
- `docker` and `docker-compose`
- Composer for PHP
- Wordpress crons (= scheduled events) are activated (simply by calling [wp-cron.php](http://127.0.0.1:8000/wp-cron.php))
- npm

**NOTE:** For an easy setup procedure, simply execute `setup.sh` in this repository and 
run `docker-compose` afterwards.

* You need [Composer](https://getcomposer.org) as package manager for PHP and `npm` for JavaScript.
 Note: You need the `openssl` extension of PHP.
* cd into `donations-plugin` directory and execute `composer install` and `npm install`
* Build JS artifacts via `npm run build` (during development you can also use `npm run start`)
* Start the whole stack (database + wordpress instance) in containers and find out the container ID of the wordpress instance via `docker ps`
* Get a shell inside the wordpress development container it via: `docker exec -ti <container_id> bash`.
Your working directory inside the container is `/var/www/html`.
* Use the `wp` command as documented [here](https://wp-cli.org), 
e.g. to enable/disable the current plugin type `wp plugin toggle donations-plugin` 
* To run **unit tests**, cd into `donations-plugin` and execute `./vendor/phpunit/phpunit/phpunit test`

## Development

### Run shop on your local machine
* This project uses a custom docker wordpress image with predefined plugins, themes etc.
* Build the development container with `docker-compose build`
* Open root directory in cmd and run command `docker-compose up -d --remove-orphans`. Consider using `--force-recreate` in some cases.
* Note that after 10 seconds the wordpress setup routine starts inside the wordpress container,
    which is defined in `wp-entrypoint.sh`.
* Check if container is running `docker container ls` or `docker ps`.
* Start shop via web browser [http://127.0.0.1:8000](http://127.0.0.1:8000). Append `/wp-admin` to URL for backend access.
* Be sure to run the WooCommerce plugin setup wizard once logged in. Note that we do not use the Jetpack Plugin yet.

### WordPress development setup information
* Locale: `de_DE`
* Backend user: `admin:password`
* Site URL `http://127.0.0.1:8000`
* Required plugins
    * Woocommerce
    * Woocommerce-Services
* Used Theme
    * Shophistic Lite

### Shutdown and cleanup
**Warning:** This will remove the complete database and all existing data!

* Open root directory in cmd and run command `docker-compose down --volumes`.
 
### Update wordpress container
- Stop all running containers via `docker-compose down --volumes`
- `docker-compose pull`
- `docker-compose build`
- Start with `docker-compose up`

## TODO
- Multi-Language Support/I18N
- add screenshots
- Add copyright header to source files
- Test with other themes
- Test without WooCommerce
- Versioning
- Remove/Handle TODOs in Code

## Links
* [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
* [Quickstart: Compose and WordPress](https://docs.docker.com/compose/wordpress)
* [WP CLI](https://wp-cli.org)
* [Wordpress JS build setup for Gutenberg Blocks](https://developer.wordpress.org/block-editor/tutorials/javascript/js-build-setup/)

## Release
Execute `release.sh` in this repository to get a production-ready distributable .zip-archive 
of this plugin.

# License & Copyright

All images in `donations-plugin/images/` are explicitly excluded of the licensing mentioned below.

[Cart Icon](https://fontawesome.com/icons/cart-plus?style=solid) (used in banner's "Add to cart"-button): [License](https://fontawesome.com/license), changed fill color to #fff

This plugin is licensed under [GPL v3.0](./LICENSE).