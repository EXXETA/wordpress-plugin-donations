import './component';

Shopware.Service('cmsService').registerCmsElement({
    name: 'wwf-banner',
    label: 'sw-cms.blocks.wwfBanner.label',
    component: 'sw-cms-el-wwf-banner',
    defaultConfig: {
        campaignMode: {
            source: 'static',
            value: ''
        }
    }
});