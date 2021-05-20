// import admin block configuration
import './module/sw-cms/blocks/commerce/wwf-banner';
import './module/sw-cms/elements/wwf-banner';
import './module/component/page-selector';
import './module/component/report-list-view';

// import and register locales
import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

Shopware.Module.register('wwf-plugin-admin', {
    routes: {
        overview: {
            component: 'wwf-banner-donation-reports-list-view',
            path: 'donation_report_overview',
        }
    },
    navigation: [{
        label: 'wwfPluginAdminIntegration.donationReports.label',
        color: '#8a0000',
        path: 'wwf.plugin.admin.overview',
        icon: 'default-chart-bar-filled',
        parent: 'sw-marketing',
        position: 150,
    }],
});

Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);

