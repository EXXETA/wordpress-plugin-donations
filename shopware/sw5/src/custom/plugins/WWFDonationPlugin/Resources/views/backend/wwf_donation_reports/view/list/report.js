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
Ext.define('Shopware.apps.WwfDonationReports.view.list.Report', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.report-listing-grid',
    region: 'center',

    configure: function () {
        return {
            toolbar: false,
            addButton: false,
            deleteButton: false,
            actionColumn: false,
            editColumn: false,
            deleteColumn: false,
            showIdColumn: true,
            columns: {
                id: {
                    flex: 0,
                    width: 50,
                },
                name: {
                    flex: 4,
                },
                totalAmount: {
                    flex: 0,
                    width: 100,
                    renderer: this.totalAmountRenderer
                },
                orderCounter: {
                    flex: 0,
                    width: 75,
                },
                intervalMode: {
                    flex: 0,
                    width: 75,
                },
                campaignDetails: {
                    flex: 5,
                    renderer: this.campaignDetailsRenderer
                }
            }
        }
    },

    campaignDetailsRenderer(campaignDetails) {
        const campaignReport = JSON.parse(campaignDetails);
        if (!campaignReport) {
            return '-';
        }
        let info = [];
        Object.keys(campaignReport).forEach(key => {
            console.log(campaignReport[key]);
            if (campaignReport[key]) {
                info.push(key + ': ' + campaignReport + ' €');
            }
        });
        if (info.length === 0) {
            return '-';
        }
        return info.join(', ');
    },

    totalAmountRenderer(totalAmount) {
        return totalAmount + ' €';
    }
});
//{/block}