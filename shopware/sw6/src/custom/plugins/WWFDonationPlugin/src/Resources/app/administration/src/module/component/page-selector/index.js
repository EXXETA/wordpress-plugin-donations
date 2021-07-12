const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

import template from './wwf-banner-page-selector.html.twig';

Component.extend('wwf-banner-page-selector', 'sw-entity-single-select', {
    template,

    computed: {
        pageCategoryCriteria() {
            const criteria = new Criteria(1, 15);
            criteria.addFilter(Criteria.equals('type', 'page'))
            criteria.addFilter(Criteria.equals('active', true))
            return criteria;
        }
    }
});