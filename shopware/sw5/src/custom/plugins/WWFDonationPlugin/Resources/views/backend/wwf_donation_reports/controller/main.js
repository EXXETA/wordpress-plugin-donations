//{block name="backend/wwf_donation_reports/application"}
Ext.define('Shopware.apps.WwfDonationReports.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function () {
        let me = this;
        me.mainWindow = me.getView('list.Window').create({}).show();
    }
});
//{/block}