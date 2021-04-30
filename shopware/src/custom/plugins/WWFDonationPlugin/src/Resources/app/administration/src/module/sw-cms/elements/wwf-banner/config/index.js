import template from './sw-cms-config-block-commerce-wwf-banner.html.twig';

Shopware.Component.register('sw-cms-el-config-wwf-banner', {
    template,

    mixins: [
        'cms-element'
    ],

    computed: {
        campaignMode: {
            get() {
                return this.element.config.campaignMode;
            },
            set(mode) {
                return this.element.config.campaignMode.value = mode;
            }
        },
        allModes() {
            return [
                {
                    label: this.$tc('wwf-campaign-labels.protect_species_coin'),
                    value: 'protect_species_coin'
                },
                {
                    label: this.$tc('wwf-campaign-labels.protect_ocean_coin'),
                    value: 'protect_ocean_coin'
                },
                {
                    label: this.$tc('wwf-campaign-labels.protect_forest_coin'),
                    value: 'protect_forest_coin'
                },
                {
                    label: this.$tc('wwf-campaign-labels.protect_climate_coin'),
                    value: 'protect_climate_coin'
                },
                {
                    label: this.$tc('wwf-campaign-labels.protect_diversity_coin'),
                    value: 'protect_diversity_coin'
                },
            ];
        },
        isMiniBannerEnabled: {
            get() {
                return this.element.config.isMiniBannerEnabled.value;
            },
            set(isEnabled) {
                this.element.config.isMiniBannerEnabled.value = isEnabled;
            }
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