import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'wwf-banner',
    category: 'commerce',
    label: 'sw-cms.blocks.wwfBanner.label',
    hidden: false,
    removable: true,
    component: 'sw-cms-block-wwf-banner',
    previewComponent: 'sw-cms-preview-block-wwf-banner',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '0',
        marginRight: '0',
        sizingMode: 'boxed',
    },
    slots: {
        content: 'wwf-banner',
    }
});