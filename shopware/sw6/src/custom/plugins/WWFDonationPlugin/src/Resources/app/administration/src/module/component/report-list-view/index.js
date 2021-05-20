const {Component, Mixin, Utils} = Shopware;
const {Criteria} = Shopware.Data;

import template from './wwf-report-list-view.html.twig';

Component.register('wwf-banner-donation-reports-list-view', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('listing')
    ],

    data() {
        return {
            reports: null,
            total: 0,
            isLoading: true,
            systemCurrency: null,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        reportRepository() {
            return this.repositoryFactory.create('wwf_donation_report');
        },

        currencyRepository() {
            return this.repositoryFactory.create('currency');
        },

        donationReportColumns() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    allowResize: true,
                    label: 'wwfPluginAdminIntegration.donationReports.name',
                    primary: true,
                    align: 'left',
                }, {
                    property: 'totalAmount',
                    dataIndex: 'totalAmount',
                    label: 'wwfPluginAdminIntegration.totalAmount.label',
                    allowResize: true,
                }, {
                    property: 'orderCount',
                    dataIndex: 'orderCount',
                    label: 'wwfPluginAdminIntegration.orderCount.label',
                    allowResize: false,
                }, {
                    property: 'intervalMode',
                    dataIndex: 'intervalMode',
                    label: 'wwfPluginAdminIntegration.intervalModes.label',
                    allowResize: false,
                }, {
                    property: 'startDate',
                    dataIndex: 'startDate',
                    label: 'wwfPluginAdminIntegration.startDate.label',
                    allowResize: false,
                }, {
                    property: 'endDate',
                    dataIndex: 'endDate',
                    label: 'wwfPluginAdminIntegration.endDate.label',
                    allowResize: false,
                }, {
                    property: 'createdAt',
                    dataIndex: 'createdAt',
                    label: 'wwfPluginAdminIntegration.createdAt.label',
                    allowResize: false,
                }
            ];
        },

        donationReportCriteria() {
            const criteria = new Criteria();
            const params = this.getListingParams();

            // Default sorting
            params.sortBy = params.sortBy || 'name';
            params.sortDirection = params.sortDirection || 'ASC';

            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort(params.sortBy, params.sortDirection));

            return criteria;
        }
    },

    methods: {
        getList() {
            this.isLoading = true;

            this.loadSystemCurrency().then(
                () => {
                    if (this.systemCurrency === null) {
                        console.error('WWFDonationPlugin: Could not retrieve system default currency!');
                        this.reports = [];
                        this.total = 0;
                        this.isLoading = false;
                        return;
                    }
                    this.reportRepository.search(this.donationReportCriteria, Shopware.Context.api)
                        .then(searchResult => {
                            for (let searchResultElement of searchResult) {
                                // append currency symbol
                                searchResultElement.totalAmount += ' ' + this.systemCurrency.symbol;
                                // format interval mode
                                searchResultElement.intervalMode = this.$tc('wwfPluginAdminIntegration.intervalModes.' + searchResultElement.intervalMode);
                                searchResultElement.createdAt = Utils.format.date(searchResultElement.createdAt, {
                                    dateStyle: 'short',
                                    timeStyle: 'short',
                                    year: undefined,
                                    month: undefined,
                                    day: undefined,
                                });
                                searchResultElement.startDate = Utils.format.date(searchResultElement.startDate);
                                searchResultElement.endDate = Utils.format.date(searchResultElement.endDate);
                            }
                            this.reports = searchResult;
                            this.total = searchResult.total;
                            this.isLoading = false;
                        });
                }
            )
        },

        updateTotal({total}) {
            this.total = total;
        },

        async loadSystemCurrency() {
            return this.currencyRepository
                .get(Shopware.Context.app.systemCurrencyId, Shopware.Context.api)
                .then((systemCurrency) => {
                    this.systemCurrency = systemCurrency;
                })
        },
    },
});