//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports', {
    name: 'Shopware.apps.WWFDonationReports',
    extend: 'Enlight.app.SubApplication',

    loadPath: '{url controller="wwf_donation_reports" action=load}',
    bulkLoad: true,

    controllers: ['Main'],

    views: [
        'list.Window',
    ],

    models: ['Product'],
    stores: ['Product'],

    launch: function () {
        return this.getController('Main').mainWindow;
    }
});
//{/block}