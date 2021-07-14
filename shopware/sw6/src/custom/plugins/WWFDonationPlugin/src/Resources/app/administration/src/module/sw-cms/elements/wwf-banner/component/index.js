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