# WWFDonationPlugin for Shopware 6

This plugin is developed using [Dockware](https://dockware.io/).

## First time setup

- Run the dockware container with `docker-compose up`
- Copy over files `docker cp shopware:/var/www/html/. ./src`

Run: `composer install` both in `./shopware/src` and in `./shopware/src/custom/plugins/WWFDonationPlugin`.

For development run `docker-compose up` and sync changes of plugin's code via SFTP into the container. 

## TODO