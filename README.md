# wp-donations-plugin

**Wordpress plugin to collect donations for a non-profit organization and
 automatically sending reports via mail.**

### Features
- Shortcode
- Gutenberg Block
- Reports with three interval modes
- Mail cannot be changed
- Reports are visible as readonly custom post type
- Live-Preview of reports/custom reports
- No privacy impact

# Setup

## Server and Wordpress requirements
- PHP Language Level 7.3+
- MySQL/MariaDB 5.7+/10.3+
- Wordpress crons (= scheduled events) are set up
- Mandatory active Wordpress plugins:
    - `woocommerce`
    - `woocommerce-services`
- Wordpress should be able to send mails via `wp_mail`


## Installation
1. Be sure to meet the listed requirements for the web server and the wordpress installation.
2. Extract provided archive of this plugin to `wp-content/plugins` directory.
3. Activate this plugin in Wordpress plugin page.
4. Configure plugin settings in `Settings > Donations`. 

# Plugin Development

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
* To run the unit tests, cd into `donations-plugin` and execute `./vendor/phpunit/phpunit/phpunit test`

## Development

### Run shop on your local machine
* Install *docker* and *docker-compose* locally [Docker get started](https://www.docker.com/get-started)
* This project uses a custom wordpress image with predefined plugins, themes etc.
* Build the development wordpress container with `docker-compose build`
* Open root directory in cmd and run command `docker-compose up -d --remove-orphans`. Consider using `--force-recreate` in some cases.
* Note that after 10 seconds the wordpress setup routine starts inside the wordpress container.
* Check if container is running `docker container ls` or `docker ps`
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
**Warning:** This will remove the complete database and existing data!

* Open root directory in cmd and run command `docker-compose down --volumes`.
 
### Update wordpress
- Stop all running containers via `docker-compose down --volumes`
- `docker-compose pull`
- `docker-compose build`
- Start with `docker-compose up`
 
## System requirements for development
- PHP Language Level 7.3+
- MySQL/MariaDB 5.7+/10.3+
- Composer for PHP
- Wordpress crons (= scheduled events) are activated (by calling `wp-cron.php`)
- npm

## TODO
- Multi-Language Support/I18N
- Document manual DB backup/restore processes
- Copyright Header
- Test with other themes
- Test without WooCommerce
- Versioning
- Remove/Handle TODOs in Code
- Cart Icon: https://fontawesome.com/icons/cart-plus?style=solid [License](https://fontawesome.com/license), Changed fill color to #fff
- Add product images

## Links
* [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
* [Quickstart: Compose and WordPress](https://docs.docker.com/compose/wordpress)
* [WP CLI](https://wp-cli.org)
* [Wordpress JS build setup for Gutenberg Blocks](https://developer.wordpress.org/block-editor/tutorials/javascript/js-build-setup/)

## Release
Execute `release.sh` in this repository to get a production-ready distributable .zip-archive 
of this plugin.

# License
Licensed under [GPL v3.0](./LICENSE).