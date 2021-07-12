import template from './sw-cms-el-wwf-banner.html.twig';
import './sw-cms-el-wwf-banner.scss';

Shopware.Component.register('sw-cms-el-wwf-banner', {
    template,

    mixins: [
        'cms-element'
    ],

    computed: {
        campaignMode() {
            return this.element.config.campaignMode;
        },
        isMiniBannerEnabled() {
            return this.element.config.isMiniBannerEnabled;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('wwf-banner');
        }
    }
});