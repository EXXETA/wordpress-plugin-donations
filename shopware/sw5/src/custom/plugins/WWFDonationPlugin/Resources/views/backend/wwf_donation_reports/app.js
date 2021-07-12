//{block name="backend/wwf_donation_reports/application"}
Ext.define('Shopware.apps.WwfDonationReports', {
    name: 'Shopware.apps.WwfDonationReports',
    extend: 'Enlight.app.SubApplication',

    loadPath: '{url controller="wwf_donation_reports" action=load}',
    bulkLoad: true,

    controllers: ['Main'],

    views: [
        'list.Window',
    ],

    models: ['Report'],
    stores: ['Report'],

    launch: function () {
        return this.getController('Main').mainWindow;
    }
});
//{/block}