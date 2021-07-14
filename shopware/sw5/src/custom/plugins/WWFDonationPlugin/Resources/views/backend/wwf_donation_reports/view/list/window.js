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
//{block name="backend/wwf_donation_reports/application"}
Ext.define('Shopware.apps.WwfDonationReports.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.report-list-window',
    height: 450,
    width: '80%',
    title: '{s name=window_title}WWF Donation Reports{/s}',

    configure: function () {
        return {
            listingGrid: 'Shopware.apps.WwfDonationReports.view.list.Report',
            listingStore: 'Shopware.apps.WwfDonationReports.store.Report'
        };
    }
});
//{/block}