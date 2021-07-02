//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.store.Report', {
    extend: 'Shopware.store.Listing',
    pageSize: 9999,
    remoteSort: false,
    remoteFilter: false,

    configure: function () {
        return {
            controller: 'wwf_donation_reports'
        };
    },
    model: 'Shopware.apps.WWFDonationReports.model.Report'
});
//{/block}