/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

define([
    'storefinderAbstractList',
    'jquery',
    'ko',
    'mage/translate',
    'uiRegistry',
    'koChosen'
], function (Component, $, ko, $t, registry) {
    'use strict';

    return Component.extend({
        events: ko.observableArray([]),
        listSelector: '#storefinder-event-list-wrapper',
        rsvpPopup: null,
        lazyLoadTriggerSelector: '.lazy-load-trigger',
        ajaxHttpMethod: 'GET',

        storeCountryFilter: null,
        currentPage: 1,
        entriesPerPage: 8,

        hasMoreToLoad: true,

        /**
         * @param item
         * @param event
         */
        filterCountry: function (item, event) {
            this.storeCountryFilter = $(event.currentTarget).val();
            this.currentPage = 1;
            var params = {
                storecountry: this.storeCountryFilter,
                limit: this.entriesPerPage,
                page: this.currentPage
            };

            this.updateAjax(params);
        },

        /**
         * Initialization that only happens once
         */
        activate: function () {
            if (this.activated) {
                return;
            }

            var self = this;
            $(document).on('scroll', function() {
                self.onPageScroll();
            });

            this.updateAjax({
                limit: this.entriesPerPage
            });
            this.activated = true;
        },

        onPageScroll: function () {
            if (this.loadingInProgress() || !this.hasMoreToLoad) {
                return;
            }

            var scrollTop = $(window).scrollTop() + $(window).height() + 200,
                offset = 0;

            if (typeof($(this.lazyLoadTriggerSelector).offset()) === 'object') {
                offset = $(this.lazyLoadTriggerSelector).offset().top;
            }

            if (scrollTop - offset > 0) {
                $(this.lazyLoadTriggerSelector).trigger('lazyload');
            }
        },

        lazyLoad: function () {
            this.currentPage++;
            var params = {
                limit: this.entriesPerPage,
                page: this.currentPage,
                updatePage: 1
            };

            if (this.storeCountryFilter !== null) {
                params.storecountry = this.storeCountryFilter;
            }

            this.updateAjax(params);
        },

        /**
         * @param response
         */
        onAjaxSuccess: function (response) {
            if (typeof(response.events) === 'undefined') {
                this.onAjaxError($t('Something went wrong with request'));
            } else {
                if (response.params.updatePage) {
                    if (response.events.length === 0) {
                        this.hasMoreToLoad = false;
                    } else {
                        ko.utils.arrayPushAll(this.events, response.events);
                    }
                } else {
                    this.events(response.events);
                }
            }
        },

        onAjaxCompleted: function () {
            this.onPageScroll();
        },

        isEqualMonths: function (item) {
            return (item.date_start_month === item.date_end_month);
        },

        isEqualDays: function (item) {
            return (item.date_start_day === item.date_end_day);
        },

        getMonth: function (item) {
            if (this.isEqualMonths(item)) {
                return item.date_start_month;
            }

            return item.date_start_month + '-' + item.date_end_month;
        },

        getDay: function (item) {
            if (this.isEqualMonths(item) && this.isEqualDays(item)) {
                return item.date_start_day;
            }

            return item.date_start_day + '-' + item.date_end_day;
        },

        getTimeString: function (item) {
            if (item.custom_time) {
                return item.custom_time;
            }

            return item.date_start_time + ' - ' + item.date_end_time;
        },

        /**
         * @param address
         * @returns {string}
         */
        formatAddress: function (address) {
            return address;
        },

        isSingleAddress: function (item) {
            if (typeof(item.stores) === 'undefined') {
                return false;
            }

            return item.stores.length === 1;
        },

        isMultipleAddress: function (item) {
            if (typeof(item.stores) === 'undefined') {
                return false;
            }

            return item.stores.length > 1;
        },

        /**
         * @param url
         * @returns {boolean}
         */
        isShowUrl: function (url) {
            return (typeof(url) === 'string') && url.length > 0;
        },

        toggleShowAddress: function (event_id, item, event) {
            var $eventItem = $('[data-event-id=' + event_id + ']'),
                $showMoreButton = $(event.currentTarget),
                moreShown = $eventItem.hasClass('show-more');

            if (moreShown) {
                $showMoreButton.text($t('Show other addresses'));
                $eventItem.removeClass('show-more');
            } else {
                $showMoreButton.text($t('Hide other addresses'));
                $eventItem.addClass('show-more');
            }
        },

        showRsvpPopup: function (item) {
            if (this.rsvpPopup === null) {
                return;
            }

            registry.get(this.rsvpPopup).showPopup(item);
        },

        emptyEventList: function () {
            return !(this.events().length > 0);
        }
    });
});
