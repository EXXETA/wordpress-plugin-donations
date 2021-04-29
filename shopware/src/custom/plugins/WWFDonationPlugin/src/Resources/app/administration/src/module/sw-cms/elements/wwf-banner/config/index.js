import template from './sw-cms-config-block-commerce-wwf-banner.html.twig';

Shopware.Component.register('sw-cms-el-config-wwf-banner', {
    template,

    mixins: [
        'cms-element'
    ],

    computed: {
        campaignMode() {
            return this.element.config.campaignMode;
        },
        allModes() {
            return [
                'protect_species_coin',
                'protect_ocean_coin',
                'protect_forest_coin',
                'protect_climate_coin',
                'protect_diversity_coin',
            ];
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('wwf-banner');
        },

        onElementUpdate(element) {
            this.$emit('element-update', element);
        }
    }
});