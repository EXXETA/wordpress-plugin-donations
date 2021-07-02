//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function () {
        let me = this;
        me.mainWindow = me.getView('list.Window').create({}).show();
    }
});
//{/block}