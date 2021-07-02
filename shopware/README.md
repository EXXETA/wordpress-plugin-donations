# WWFDonationPlugin for Shopware 5 + 6

This directory contains the development files for Shopware 5 and 6 WWF Germany shop plugins. In both
cases [Dockware](https://dockware.io/) is being used. You should make yourself familiar with Dockware before starting a
development process. You should also set up your IDE according to the Dockware documentation.

Before you continue, you should also run the `setup.sh` script in the repository root and you should have read
the [main README](./../README.md) and the [README of the banner core library](./../core/README.md).

## First time (development) setup

- Navigate to the [sw5](./sw5) or [sw6](./sw6) subdirectory
- Run the Dockware development container with `docker-compose up`
- Run `./sync_dev.sh` which will copy the local plugin files into the container and the plugin will be enabled and
  activated.

Run: `composer install` both in `./shopware/sw6/src` and in `./shopware/sw6/src/custom/plugins/WWFDonationPlugin`.

For development run `docker-compose up` and sync changes of plugin's code via SFTP into the container as described in
the Dockware docs.

## File system structure

- `./sw[5|6]` Shopware-specific Dockware development setup
    - `docker-compose.yml`: Start the development shop with `docker-compose up -d`
    - `sync_dev.sh`: Bash script to sync the repository contents with the started shop inside the container
    - `sync_release.sh`: Same as `sync_dev.sh`, but for release tests. Release scripts MUST be executed before. Not
      relevant for SW5.
- `./sw[5|6]/src` Shopware shop root source directory
- `./sw[5|6]/src/custom/plugins` Plugin (development) source files
- `./sw[5|6]/test` Directory to store final release test files, for example a `docker-compose.yml`
    - `docker-compose.yml`: "Production" shop Dockware container to test the releases
    - `release_test.sh`: A script which deploys the release files in `<repo-root>/release/...` to the test shop
      instance. Release scripts MUST be executed before.

## General Notes

- The Shopware plugins are developed and tested with the default themes only. Therefore, it may be necessary to adjust
  styles and themes for your customized Shopware setup. If you think your changes are relevant for others, feel free to
  file a GitHub issue or a Pull Request.
- You MUST NOT change the name of the supplier (SW5) or the manufacturer (SW6).
- You MUST NOT change the article number (prefixed with "WWF-DE-") in SW5.
- You MUST NOT change the value of the "Freitextfeld 1" of the created articles in SW5 context.
- Both plugins will create a 0 % tax record - if it does not already exist in the shop.

## Shopware 5 Plugin Installation

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
banner to each product detail page in a SW5 shop.

Note: To test mail delivery with the Shopware 5 Dockware system, you need to edit the sendmail configuration of
the `php.ini`:

- `sudo vim /etc/php/7.4/cli/php.ini`
    - Ensure `mail.force_extra_parameters = -t` is present

## Shopware 6 Plugin Installation

TODO

### Possible problems with the Shopware 6 plugin

- Wrong CSRF usage if CSRF of shopware is in ajax mode, e.g. if shop is running behind a (page) cache proxy
