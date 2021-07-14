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
// import admin block configuration
import './module/sw-cms/blocks/commerce/wwf-banner';
import './module/sw-cms/elements/wwf-banner';
import './module/component/page-selector';
import './module/component/report-list-view';

// import and register locales
import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

Shopware.Module.register('wwf-plugin-admin', {
    color: '#FFD700',
    routes: {
        overview: {
            component: 'wwf-banner-donation-reports-list-view',
            path: 'donation_report_overview',
        }
    },
    navigation: [{
        label: 'wwfPluginAdminIntegration.donationReports.label',
        color: '#8a0000',
        path: 'wwf.plugin.admin.overview',
        icon: 'default-chart-bar-filled',
        parent: 'sw-marketing',
        position: 150,
    }],
});

Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);

