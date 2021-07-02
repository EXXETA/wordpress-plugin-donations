//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.model.Product', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'WWFDonationReports'
        };
    },

    fields: [
        //{block name="backend/wwf_donation_reports/model/reports/fields"}{/block}
        'id',
        'name',
        'order_counter',
        'interval_mode',
        'total_amount',
        'start_date',
        'end_date'
    ]
});
//{/block}