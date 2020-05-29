# wwf-donations-plugin

**Plugin for Wordpress to collect donations in a WooCommerce shop 
for a non-profit organization. Automatically sending reports via mail.**
 
**NOTE:** Currently this plugin is in German language available only.

### Features
- Five donation products/campaigns
- Providing easy banner content (for cart page):
    - Shortcode: `[wwf_donations_banner]`
    - Gutenberg Block: `Spendemünzen`
- Campaign reports
    - Automatically generating reports
    - Mail delivery of report content
    - Reports are persistent and transparently accessible in custom backend section
    - Three interval modes: *weekly*, *monthly*, *quarterly*
    - Custom report live preview
    - Report generation is checked once a day
- No impact on privacy of customers of this plugin

# Installation procedure
1. Be sure to meet the listed requirements for the web server and the wordpress installation.
2. Extract provided archive of this plugin to `wp-content/plugins` directory.
3. Activate this plugin in Wordpress plugin page.
4. Configure plugin settings in `Settings > Spendenübersicht`. 

# Setup
- Install and activate this plugin in your wordpress installation.
- Add the banner to your *WooCommerce* cart page by using a block (in "Gutenberg" editor) 
or as an alternative you use a shortcode (documented below). Technically both ways lead to the same markup/result.

### Products
This plugin will create one new product category in a WooCommerce shop called "Spendemünzen" containing
six donation products (à 1 €) for different campaigns:
- Protecting species
- Protecting oceans
- Protecting forests
- Protecting climate
- Protecting diversity

You can view the WooCommerce product IDs in plugin report dashboard page in backend.

*NOTE:* Products are not removed during uninstallation. You have to remove them yourself for a complete cleanup. 

#### Product details
- virtual
- no stock management
- no taxation (`tax_status='none'`)
- not sold individually
- no reviews allowed
- `catalog_visibility='hidden'`

### Shortcode `wwf_donations_banner`
You can configure the target donation campaign by providing a `campaign` argument. If no campaign
is given, the *default* one is: `protect_species_coin`.

Valid values for `campaign` argument are:
- `protect_species_coin`
- `protect_ocean_coin`
- `protect_forest_coin`
- `protect_climate_coin`
- `protect_diversity_coin`

Example of shortcode usage: `[wwf_donations_banner campaign='protect_climate_coin']`

### Reports
You can configure three different report interval modes: `weekly`, `monthly`, `quarterly`.

Each report will be send by mail to an address you can view,
 but not change in plugin's backend section.
 
Technically the report generation is a summation of plugin's donation products in completed 
or processed orders grouped by a donation campaign in a certain time range.

All reports are persisted as a private custom post type integrated into the Wordpress system.

One time per day, a routine will check if time to generate a new report is reached. You can view the 
date and time of the last check in plugin's report dashboard.

**NOTE:** After plugin installation/(re-)activation there will be generated one first (probably empty) report.

**NOTE:** You are free to modify the WooCommerce products in your shop. This plugin won't overwrite these.

### Banner design/styling
This plugin is shipped with self-contained responsive CSS styles without dependencies to 
a specific theme or framework.

*NOTE*: Styles are included by wordpress only if the banner was placed into a page/post.

**Minimum screen width:** `320px`
**Maximum screen width:** `4k+`

Banner main class: `.cart-donation-banner`
CSS styles: `wwf-donations-plugin/styles/banner.css`

## Server and Wordpress requirements
- PHP 7.3+
- MySQL/MariaDB 5.7+/10.3+
- Required active Wordpress plugins:
    - `woocommerce`
- Wordpress crons (= scheduled events) are set up and are running at least once a day
- Wordpress should be able to send mails via `wp_mail`
- WooCommerce is (initially) set up
    - Currency: Euro

# Plugin Development

This repository contains a docker-compose configuration for a reproducible
environment during development. The directory `./wwf-donations-plugin` will be mounted into the
wordpress container.

## Requirements for development
- PHP Language Level 7.3+
- MySQL/MariaDB 5.7+/10.3+
- `docker` and `docker-compose`
- Composer for PHP
- Wordpress crons (= scheduled events) are activated (simply by calling [wp-cron.php](http://127.0.0.1:8000/wp-cron.php))
- `npm`

**NOTE:** For an easy setup procedure, simply execute `setup.sh` in this repository and 
run `docker-compose`-commands afterwards.

* You need [Composer](https://getcomposer.org) as package manager for PHP and `npm` for JavaScript.
 Note: You need at least the following **PHP extensions** enabled:
    - `openssl`
    - `dom`
    - `json`
    - `libxml`
    - `mbstring`
    - `xml`
    - `xmlwriter`
* cd into `wwf-donations-plugin` directory and execute `composer install` and `npm install`
* Build CSS artifacts via `npm run build` (during development you can also use `npm run stylewatch`)
* Build JS artifacts via `npm run build-js` (during development you can also use `npm run start`)
* Start the whole stack (database + wordpress instance) in containers and find out the container ID of the wordpress instance via `docker ps`

## Development

### Run shop on your local machine
* This project uses a custom docker wordpress image with predefined plugins, themes etc.
* Build the development container with `docker-compose build`
* Open root directory in cmd and run command `docker-compose up -d --remove-orphans`. Consider using `--force-recreate` in some cases.
 The initial setup procedure will take approx. 30 secs.
    * Note that after 10 seconds the wordpress setup routine starts inside the wordpress container,
    which is defined in `wp-entrypoint.sh`.
* Check if container is running `docker container ls` or `docker ps`.
* Open local shop via web browser [http://127.0.0.1:8000](http://127.0.0.1:8000). Append `/wp-admin` to URL for backend access.
* **Be sure** to run the WooCommerce plugin setup wizard once logged in at initial startup. Note that we **do not** use the Jetpack Plugin yet.

### WordPress development setup information
* Locale: `de_DE`
* Backend user: `admin:password`
* Site URL: [`http://127.0.0.1:8000`](http://127.0.0.1:8000)
* Backend URL: [`http://127.0.0.1:8000/wp-admin`](http://127.0.0.1:8000/wp-admin)
* Required plugins
    * Woocommerce
* Default theme
    * Shophistic Lite
* Currency: Euro

### Further information
* To get a shell inside the wordpress development container, simply use: `docker exec -ti <container_id> bash`.
Your working directory inside the container is `/var/www/html`.
* You can use the `wp` command as documented [here](https://wp-cli.org), 
e.g. to enable/disable the current plugin type `wp plugin toggle wwf-donations-plugin` 
* To run **unit tests**, cd into `wwf-donations-plugin` and execute `./vendor/phpunit/phpunit/phpunit test`
* For tests with a lot of orders, use [this plugin](https://github.com/75nineteen/order-simulator-woocommerce).
* Simple performance measurement: 1000 orders in a report time range need ~25 seconds during report generation

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

## Links
* [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
* [Quickstart: Compose and WordPress](https://docs.docker.com/compose/wordpress)
* [WP CLI](https://wp-cli.org)
* [Wordpress JS build setup for Gutenberg Blocks](https://developer.wordpress.org/block-editor/tutorials/javascript/js-build-setup/)

## Release
Execute `release.sh` in this repository to get a production-ready distributable .zip-archive 
of this plugin.

# License & Copyright

All images in `wwf-donations-plugin/images/` are explicitly excluded of the licensing policy mentioned below.

This plugin is licensed under [GPL v3.0](./LICENSE).