//{block name="backend/wwf_donation_reports/application"}
Ext.define('Shopware.apps.WwfDonationReports.model.Report', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'wwf_donation_reports',
        };
    },

    fields: [
        //{block name="backend/wwf_donation_reports/model/reports/fields"}{/block}
        'id',
        'startDate',
        'endDate',
        'name',
        'orderCounter',
        'intervalMode',
        'totalAmount',
        'campaignDetails'
    ]
});
//{/block}