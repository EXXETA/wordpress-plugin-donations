# Release Testing

This document is a work-in-progress document to collect all possible (E2E) test cases.

## General Plugin Aspects

- [ ] Plugin setup and installation documentation is available
- [ ] Plugin can be installed
- [ ] Plugin can be activated
- [ ] Plugin can be deactivated
- [ ] Plugin can be uninstalled
- [ ] Plugin's version and license/copyright information is correct
- [ ] Plugin configuration is working/displayed
- [ ] All plugin configuration options are working as expected
    - [ ] Campaign selection
    - [ ] Report interval selection
    - [ ] Optional: OffCanvas-Integration
    - [ ] Optional: OffCanvas is opened after Add-To-Cart button was clicked
- [ ] Optional, if there are administration GUIs to adminster *CronJobs* (e.g. SW 5), the jobs have to be enabled.

## Content Aspects (not available for SW 5)

- [ ] The shop's (CMS) editor can be used to integrate a donation banner
    - [ ] The user can change the campaign
- [ ] The shop's (CMS) editor can be used to integrate a donation *mini* banner
    - [ ] The user can change the campaign
- [ ] (Mini) Banner preview works in the content editor
- [ ] Show one banner on one page and test the Add-To-Cart-Button
- [ ] Show multiple banners each with a different campaign on one page and test the Add-To-Cart-Button
- [ ] The "More information" text is displayed for default (big) banners after a click on "hier" link of the banner
- [ ] The "More information" text dismisses after a click on "Informationstext schließen" button

## Cart, Order and Product Aspects

- [ ] Custom products are created by the plugin and they fit the default product model of the shop system
- [ ] The donation products have proper product media associated
- [ ] The donation products are linked to a a (newly created) 0 % tax entry
- [ ] The donation products are linked to the newly created WWF manufacturer record
- [ ] The donation products are free of shipping-costs
- [ ] All of the created products can be added to the cart with quantity 1
- [ ] All of the created products can be added to the cart with quantity > 1
- [ ] Mini Banner information target page can be customized and can point to a custom shop page
- [ ] Mini Banner information target page can be customized and can point to an external URL
- [ ] An order with only a donation product can be placed
- [ ] An order with a donation product among other products can be placed
- [ ] After (some) orders had placed, the product's stock was updated as expected (basically: stock was (re-)set to
  5000).
- [ ] The default banner Add-To-Cart-Button redirects to cart (entry) page

## Donation Report Aspects

- [ ] Report generation works and mails are being sent
- [ ] User can see the generated reports, at least the total amounts.
- [ ] An order can be revoked/cancelled and the plugin's donation report won't consider this order

## Specific Plugin Aspects

- [ ] Specific Plugin installation/setup documentation is understandable for the user
- [ ] Optional: "Live-Preview" works as expected
