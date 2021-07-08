//{block name="backend/wwf_donation_reports/application"}
Ext.define('Shopware.apps.WwfDonationReports.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.report-list-window',
    height: 450,
    width: '80%',
    title: '{s name=window_title}WWF Donation Reports{/s}',

    configure: function () {
        return {
            listingGrid: 'Shopware.apps.WwfDonationReports.view.list.Report',
            listingStore: 'Shopware.apps.WwfDonationReports.store.Report'
        };
    }
});
//{/block}