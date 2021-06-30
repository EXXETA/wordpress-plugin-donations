# WWFDonationPlugin for Shopware 5 + 6

This plugin is developed using [Dockware](https://dockware.io/).

## First time setup

- Run the dockware container with `docker-compose up`
- Copy over files `docker cp shopware:/var/www/html/. ./src`

Run: `composer install` both in `./shopware/sw6/src` and in `./shopware/sw6/src/custom/plugins/WWFDonationPlugin`.

For development run `docker-compose up` and sync changes of plugin's code via SFTP into the container.

## TODO

SW5: Add products/articles to a category manually after installation!

- Never change product number (prefix)!
- FIX problem with article images in sw5 frontend!

- Installation process order: Media import -> Product import -> CMS block

## Possible problems

- Wrong CSRF usage if CSRF of shopware is in ajax mode, e.g. if shop is running behind a (page) cache proxy

- You should not change the name of the supplier (SW5) or the manufacturer (SW6)
- The plugin will create a 0 % tax record - if it does not already exist

## General Notes

- The Shopware plugins are developed and tested with the default themes only. Therefore, it may be necessary to adjust
  styles and themes for your customized Shopware setup.

## Shopware 5 Setup

1. Install and activate the plugin in a Shopware 5 shop instance.
2. There are 5 new products. You MUST add them to an active article category of your shop.
3. View the plugin's configuration options. There you can enable the cart integration of the WWF banner.
    1. Or insert this code into your Smarty templates:

```phpt
<!-- include banner styles -->
<link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/banner.css'}"/>
<!-- [...] -->
<!-- this smarty function includes the banner markup -->
{wwfbanner campaign="protect_species_coin" isAjax=false isMini=false miniBannerTargetPage="https://www.wwf.de"}
```

[Here is an example](./sw5/src/custom/plugins/WWFDonationPlugin/Resources/views/frontend/exampledetail) to add the
banner to every product detail page.

Note: To test mail delivery with the Shopware 5 Dockware system, you need to edit the sendmail configuration of
the `php.ini`:

- `sudo vim /etc/php/7.4/cli/php.ini`
    - Ensure `mail.force_extra_parameters = -t`

## Shopware 6 Setup

TODO