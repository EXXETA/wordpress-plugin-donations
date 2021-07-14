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
            value: 'protect_species_coin',
        },
        isMiniBannerEnabled: {
            source: 'static',
            value: false,
        },
        isOffCanvasDisplayed: {
            source: 'static',
            value: false,
        },
        miniBannerTargetCategory: {
            source: 'static',
            value: null,
        },
    }
});