//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.product-list-window',
    height: 450,
    title: '{s name=window_title}WWF Donation Reports{/s}',

    configure: function () {
        return {
            listingGrid: 'Shopware.apps.WWFDonationReports.view.list.Product',
            listingStore: 'Shopware.apps.WWFDonationReports.store.Product'
        };
    }
});
//{/block}