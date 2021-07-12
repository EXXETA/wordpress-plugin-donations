//{block name="backend/wwf_donation_reports/application"}
Ext.define('Shopware.apps.WwfDonationReports.store.Report', {
    extend: 'Shopware.store.Listing',
    pageSize: 9999,
    remoteSort: false,
    remoteFilter: false,

    configure: function () {
        return {
            controller: 'wwf_donation_reports'
        };
    },
    model: 'Shopware.apps.WwfDonationReports.model.Report'
});
//{/block}