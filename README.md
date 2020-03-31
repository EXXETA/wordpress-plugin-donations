# wp-donations-plugin

**Wordpress plugin to collect donations for a non-profit organization.**

# Setup

# Development

### Run shop on your local machine
* Install *docker* and *docker-compose* locally [Docker get started](https://www.docker.com/get-started)
* This project uses a custom wordpress image with predefined plugins, themes etc.
* Build the development wordpress container with `docker-compose build`
* Open root directory in cmd and run command `docker-compose up -d --remove-orphans`. Consider using `--force-recreate` in some cases.
* Note, that after 10 seconds the wordpress setup routine starts inside the wordpress container.
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

# Plugin Development
* You need [Composer](https://getcomposer.org) as package manager for PHP and `npm` for JavaScript
* cd into `donations-plugin` directory and execute `composer install` and `npm install`
* Build JS artifacts via `npm run build` (during development you can also use `npm run start`)
* Start the whole stack (database + wordpress instance) in containers and find out the container ID of the wordpress instance via `docker ps`
* Get a shell inside the wordpress development container it via: `docker exec -ti <container_id> bash`.
Your working directory inside the container is `/var/www/html`.
* Use the `wp` command as documented [here](https://wp-cli.org), 
e.g. to enable/disable the current plugin type `wp plugin toggle donations-plugin` 

## Shutdown and cleanup
**Warning:** This will remove the complete database and existing data!

* Open root directory in cmd and run command `docker-compose down --volumes`.
 
## System requirements
- PHP Language Level 7.3+
- MySQL/MariaDB 5.7+/10.3+
- Composer for PHP
- npm for JS

## TODO
- Multi-Language Support/I18N
- Document manual DB backup/restore processes
- Licensing (Pre-Release) issues + Copyright Header hinzufügen
- Release-Script to build a plugin to release
- Test with other themes
- Test without WooCommerce
- Versioning
- Remove/Handle TODOs in Code
- Cart Icon: https://fontawesome.com/icons/cart-plus?style=solid [License](https://fontawesome.com/license), Changed fill color to #fff

## Links
* [Quickstart: Compose and WordPress](https://docs.docker.com/compose/wordpress)
* [Docker cheat sheet](https://www.docker.com/sites/default/files/d8/2019-09/docker-cheat-sheet.pdf)
* [Docker cheat sheet remove all](https://linuxize.com/post/how-to-remove-docker-images-containers-volumes-and-networks/)
* [WP CLI](https://wp-cli.org)
* [Wordpress JS build setup](https://developer.wordpress.org/block-editor/tutorials/javascript/js-build-setup/)