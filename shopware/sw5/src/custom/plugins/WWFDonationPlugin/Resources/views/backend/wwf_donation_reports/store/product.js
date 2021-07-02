//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.store.Product', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'WWFDonationReports'
        };
    },
    model: 'Shopware.apps.WWFDonationReports.model.Product'
});
//{/block}