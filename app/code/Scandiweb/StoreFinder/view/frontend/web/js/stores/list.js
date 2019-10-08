/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

define([
    'storefinderAbstractList',
    'jquery',
    'ko',
    'mage/translate',
    'koChosen'
], function (Component, $, ko, $t) {
    'use strict';

    return Component.extend({
        stores: ko.observableArray([]),
        ajaxHttpMethod: 'GET',
        listSelector: '#storefinder-store-list-wrapper',
        countryFilter: null,

        filterCountry: function (item, event) {
            var params = {
                storecountry: $(event.currentTarget).val()
            };

            this.updateAjax(params);
        },

        /**
         * @param response
         */
        onAjaxSuccess: function (response) {
            if (typeof(response.stores) === 'undefined') {
                this.onAjaxError($t('Something went wrong with request'));
            } else {
                this.stores(response.stores);
            }
        },

        /**
         * @param address
         * @returns {string}
         */
        formatAddress: function (address) {
            return address;
        },

        /**
         * @param item
         * @returns {string}
         */
        getBaseImageSrc: function (item) {
            return item.base_image_url;
        },

        /**
         * @param item
         * @returns {*}
         */
        getBaseImageAlt: function (item) {
            if (typeof(item.base_image_alt) !== 'string') {
                return $t('Image of store "%1"').replace('%1', item.store_name);
            }

            return item.base_image_alt;
        },

        /**
         * @param item
         * @returns {boolean}
         */
        isShowDirectionUrl: function (item) {
            return (typeof(item.custom_directions_url) === 'string')
                && item.custom_directions_url.length > 0;
        },

        getDetailPageUrl: function (item) {
            return item.detail_page_url;
        }
    });
});
