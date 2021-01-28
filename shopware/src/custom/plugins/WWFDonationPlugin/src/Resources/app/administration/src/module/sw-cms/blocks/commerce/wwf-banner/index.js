import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'wwf-banner',
    label: 'sw-cms.blocks.wwfBanner.label',
    category: 'commerce',
    hidden: false,
    removable: false,
    component: 'sw-cms-block-commerce-wwf-banner',
    previewComponent: 'sw-cms-preview-block-commerce-wwf-banner',
    defaultConfig: {
        marginBottom: '0',
        marginTop: '0',
        marginLeft: '0',
        marginRight: '0',
        sizingMode: 'boxed'
    },
    slots: {
        "content": {
            type: "wwf-banner",
            default: {
                config: {},
                data: {}
            }
        }
    }
});