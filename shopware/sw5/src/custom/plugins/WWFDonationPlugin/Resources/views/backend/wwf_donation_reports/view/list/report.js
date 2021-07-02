//{block name="backend/marketing/wwf_donation_reports"}
Ext.define('Shopware.apps.WWFDonationReports.view.list.Report', {
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