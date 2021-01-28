import './component';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'wwf-banner',
    label: 'sw-cms.blocks.wwfBanner.label',
    component: 'sw-cms-el-wwf-banner',
    hidden: true,
    // configComponent: 'sw-cms-el-config-dailymotion',
    previewComponent: 'sw-cms-el-preview-wwf-banner',
    defaultConfig: {
        campaignMode: {
            source: 'static',
            value: ''
        }
    }
});