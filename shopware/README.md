# WWFDonationPlugin for Shopware 5 + 6

This plugin is developed using [Dockware](https://dockware.io/).

## First time setup

- Run the dockware container with `docker-compose up`
- Copy over files `docker cp shopware:/var/www/html/. ./src`

Run: `composer install` both in `./shopware/sw6/src` and in `./shopware/sw6/src/custom/plugins/WWFDonationPlugin`.

For development run `docker-compose up` and sync changes of plugin's code via SFTP into the container.

## TODO

- Never change product number (prefix)!


- Installation process order: Media import -> Product import -> CMS block

## Possible problems

- Wrong CSRF usage if CSRF of shopware is in ajax mode, e.g. if shop is running behind a (page) cache proxy