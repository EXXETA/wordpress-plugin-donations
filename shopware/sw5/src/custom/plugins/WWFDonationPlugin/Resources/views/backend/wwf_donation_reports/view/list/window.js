//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.report-list-window',
    height: 450,
    width: '80%',
    title: '{s name=window_title}WWF Donation Reports{/s}',

    configure: function () {
        return {
            listingGrid: 'Shopware.apps.WWFDonationReports.view.list.Report',
            listingStore: 'Shopware.apps.WWFDonationReports.store.Report'
        };
    }
});
//{/block}