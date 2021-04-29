import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'wwf-banner',
    label: 'sw-cms.blocks.wwfBanner.label',
    component: 'sw-cms-el-wwf-banner',
    configComponent: 'sw-cms-el-config-wwf-banner',
    previewComponent: 'sw-cms-el-preview-wwf-banner',
    defaultConfig: {
        campaignMode: {
            source: 'static',
            value: 'protect_species',
        },
    }
});