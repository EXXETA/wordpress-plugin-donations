/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
import template from './sw-cms-config-block-commerce-wwf-banner.html.twig';

const {Criteria} = Shopware.Data;

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
                if (!isEnabled) {
                    this.element.config.miniBannerTargetCategory.value = null;
                    this.element.config.isOffCanvasDisplayed.value = false;
                    this.element.config.miniBannerTargetCategory.required = undefined;
                } else {
                    this.element.config.miniBannerTargetCategory.required = true;
                }
            }
        },
        isOffCanvasDisplayed: {
            get() {
                return this.element.config.isOffCanvasDisplayed.value;
            },
            set(showOffCanvas) {
                this.element.config.isOffCanvasDisplayed.value = showOffCanvas;
            }
        },
        miniBannerTargetCategory: {
            get() {
                return this.element.config.miniBannerTargetCategory.value;
            },
            set(targetCategory) {
                this.element.config.miniBannerTargetCategory.value = targetCategory;
            }
        },
        pageCategoryCriteria() {
            const criteria = new Criteria(1, 15);
            criteria.addFilter(Criteria.equals('type', 'page'))
            criteria.addFilter(Criteria.equals('active', true))
            return criteria;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('wwf-banner');
            if (this.element.config.isMiniBannerEnabled.value) {
                this.element.config.miniBannerTargetCategory.required = true;
            } else {
                this.element.config.miniBannerTargetCategory.required = undefined;
            }
        },

        onElementUpdate(element) {
            this.$emit('element-update', element);
        }
    }
});