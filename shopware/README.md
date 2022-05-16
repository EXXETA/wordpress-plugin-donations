# WWFDonationPlugin for Shopware 5 + 6

This document covers the documentation of a Donation Plugin for WWF Germany for shop systems running Shopware 5 or 6.
You should read it carefully before plugin's installation and usage. Please keep in mind
to [report bugs and file issues](https://github.com/EXXETA/wwf-plugin-donations/issues) if something is not working as
expected or of there is information missing.

The following sections assume you are using a Bash-like shell to execute `.sh`-scripts.

Last officially tested Shopware versions: `5.7.2 | 6.4.11.1`

## First Time (Development) Setup

*If you are a plugin user, this section is NOT relevant for you.*

This directory contains the development files for Shopware 5 and 6 (SW5, SW6) WWF Germany shop plugins. In both
cases [Dockware](https://dockware.io/) is being used for development and testing. You should make yourself familiar with
Dockware before starting development. You should also set up your IDE according to the Dockware documentation. If you
are a tester, you won't need an IDE.

Before you continue, you should also run the `setup.sh` script in the repository root and you should have read
the [main README](./../README.md) and the [README of the banner core library](./../core/README.md) to get a basic
understanding of the technical aspects of this project.

For development purposes run `docker-compose up` and sync changes of plugin's code via SFTP into the container as
described in the Dockware docs you should follow.

- Execute `<app_root>/shopware/setup.sh` script. This is already done in case you executed `<app_root>/setup.sh` before
- Navigate to the [sw5](./sw5) or [sw6](./sw6) subdirectory
- Run the Dockware development container with `docker-compose up`
- Run `./sync_dev.sh` which will copy the local plugin files into the container and the plugin will be enabled and
  activated.

## (Release) Testing

*If you are a plugin user, this section is NOT relevant for you.*

- Ensure you're following the first time setup steps in the last section.
- Execute the script `<repo_root>/shopware/release.sh` to build both SW5 and SW6 plugins
  in `<repo_root>/release/sw[5|6]`
  directory. Alternatively you can execute `<repo_root>/shopware/release_sw[5|6].sh` to build a single plugin only.
- Navigate to `<repo_root>/shopware/sw[5|6]/test` and execute the script `release_test.sh` which will start a Shopware
  instance in a Dockware container with the plugin installed and activated already.

## File System Repository Structure

*If you are a plugin user, this section is NOT relevant for you.*

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

## General Notes and Requirements

*If you are a plugin user OR a plugin developer, this section IS relevant for you.*

- The Shopware plugins are developed and tested with the default themes only. Therefore, it may be necessary to adjust
  styles and themes for your customized Shopware setup. If you think your changes are relevant for others, feel free to
  file a GitHub issue or a Pull Request.
- You [MUST NOT](https://datatracker.ietf.org/doc/html/rfc2119) change the name of the supplier (SW5) or the
  manufacturer (SW6) and the product's number (SW5 + SW6).
- You MUST NOT change the article number (prefixed with "WWF-DE-") in SW5.
- You MUST NOT change the value of the "Freitextfeld 1" of the created articles in SW5 context.
- Both plugins will create a 0 % tax record - if it does not already exist in the shop.
- Both plugins will import product media assets into the shop-specific media library.
- Both plugins contain English and German translations wherever possible.
- The SW6 plugin will enable the WWF products for all sales channels initially.
- Due to the default product stock management logic of Shopware 5 and 6 both plugins contain own logic to handle the
  five created WWF products as "virtual" products. Basically the stock of the WWF products created by the plugin is
  always
  (reset to) **5000**. This should not interfere with other products in your shop.
- Your shop MUST be able to send emails.
- As a core feature both plugins provide a donation report generation mechanism. Due to highly-customizable order and
  payment states or even state machines of the Shopware software there is **no guarantee to consider all (of your
  custom)
  order and payment states**. In theory, if you use custom order states, e.g. for cancellation or partial payments, it
  is possible that the generated donation report amounts are higher than reality.
    - The SW 5 plugin does not consider orders with the following states during report generation:
        - `Status::ORDER_STATE_CANCELLED`
        - `Status::ORDER_STATE_CANCELLED_REJECTED`
    - .. and in the SW 6 plugin:
        - `cancelled`
        - `failed`
- All generated reports are stored in the database in a new table called `s_wwf_donation_reports` (SW5)
  or `wwf_donation_report` (SW6).
- The setup and release scripts expect the `docker` command to be runnable as non-root user. Otherwise, use `sudo` or
  change your setup.
- You need the PHP extension `ext-sodium` enabled.
- NOTE: The first generated donation report probably will be empty.

## Shopware 5 Plugin

### Features

- Have a look at the [general feature list here](./../README.md)
- For Shopware 5 there is no "live preview" of the donation reports.
- Daily CronJob (integrated "natively" into SW5, not a unix cron job!). Shopware 5 is responsible to execute these
  SW-CronJobs.

### Installation

1. Install and activate the plugin in a Shopware 5 shop instance. Unzip the plugin's release archive
   to `<shop_root>/custom/plugins`.
    - `bin/console sw:plugin:refresh`
    - `bin/console sw:plugin:list`
    - `bin/console sw:plugin:install -v --activate --clear-cache WWFDonationPlugin`
    - `bin/console sw:thumbnail:generate`
    - `bin/console sw:cache:clear`
2. There are 5 new products. You MUST add them to an active article category of your shop.
3. View the plugin's configuration options. You can enable the cart integration of the WWF banner there.
    1. Or insert this code snippet into your Smarty templates:

```phpt
<!-- include banner styles -->
<link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/banner.css'}"/>
<!-- [...] -->
<!-- this smarty function includes the banner markup -->
{wwfbanner campaign="protect_species_coin" isAjax=false isMini=false miniBannerTargetPage="https://www.wwf.de"}
```

[Here is an example](./sw5/src/custom/plugins/WWFDonationPlugin/Resources/views/frontend/exampledetail) to add the
banner to each product detail page in a SW5 shop.

Valid values for the *campaign* argument are:

- protect_species_coin
- protect_ocean_coin
- protect_forest_coin
- protect_climate_coin
- protect_diversity_coin

The argument *miniBannerTargetPage* can and should be an absolute or a relative URL.

Note: To test mail delivery with the Shopware 5 Dockware system, you need to edit the sendmail configuration of
the `php.ini`:

- `sudo vim /etc/php/7.4/cli/php.ini`
    - Ensure `mail.force_extra_parameters = -t` is present

To access the backend use [http://localhost/backend](http://localhost/backend) with the credentials `demo:demo`.

### Possible problems of the SW 5 plugin

- If there are a lot of different (sub-)shop instances, the plugin will always use the default shop instance.

## Shopware 6 Plugin Installation

### Features

- Have a look at the [general feature list here](./../README.md)
- For Shopware 6 there is no "live preview" of the donation reports.
- Graphical configurable WWF Banner CMS block (Block category: `Commerce`)
- Daily report generation check job (aka `ScheduledTask`), to check if the time has come for the next donation report.
  If so, the report will be generated, immediately.

### Installation

1. Install and activate the plugin in a Shopware 6 shop instance. Unzip the plugin's release archive
   to `<shop_root>/custom/plugins`.
    - `bin/console plugin:refresh`
    - `bin/console plugin:install --activate WWFDonationPlugin`
    - `bin/console assets:install`
    - `bin/console cache:clear`
2. There are 5 new products. You can add them to an active article category of your shop.
3. You should have a look at the plugin's configuration options. E.g., you can enable the cart integration of the WWF
   banner and select a campaign.
4. You can create a new Layout and you can add the WWF Banner CMS element (block category: `Commerce`) and configure it.
   Afterwards you can assign this layout to a (product) category.

### Usage and integration

If `APP_ENV` is `dev`, you can execute: `bin/console wwf:report-generate` to generate a test report.

If you are a theme developer or if you want to integrate the banner in a Twig template, you can use the `wwfBanner(...)`
Twig extension like this (see `\WWFDonationPlugin\Twig\BannerExtension` for details):

```phpt
<div class="sw-cms-block-commerce-wwf-banner" data-open-offcanvas="<is_offcanvas_opened_on_cart_add>">
    {{ wwfBanner(<campaign_slug>, <is_mini_banner>, <mini_banner_target_url>) | raw }}
</div>
```

Valid values for the *<campaign_slug>* argument are:

- protect_species_coin
- protect_ocean_coin
- protect_forest_coin
- protect_climate_coin
- protect_diversity_coin

*<is_offcanvas_opened_on_cart_add>* and *<is_mini_banner>* are boolean values. *<mini_banner_target_url>* is expected to
be a string.

To access the backend use [http://localhost/admin](http://localhost/admin) with the credentials `admin:shopware`.

### Possible problems with the Shopware 6 plugin

- Wrong CSRF usage if CSRF of shopware is in ajax mode, e.g. if shop is running behind a (page) cache proxy

## Plugin's TODO (SW5 + SW6)

- SW6: Allow (absolute) URLs for mini banner target page configuration values, not only (shop) categories. Workaround:
  Use custom theme integration with `wwfBanner` Twig extension.
- SW6: Fix backend section sorting of donation reports