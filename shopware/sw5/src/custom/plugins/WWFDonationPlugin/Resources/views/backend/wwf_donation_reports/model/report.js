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
Ext.define('Shopware.apps.WwfDonationReports.model.Report', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'wwf_donation_reports',
        };
    },

    fields: [
        //{block name="backend/wwf_donation_reports/model/reports/fields"}{/block}
        'id',
        'startDate',
        'endDate',
        'name',
        'orderCounter',
        'intervalMode',
        'totalAmount',
        'campaignDetails'
    ]
});
//{/block}