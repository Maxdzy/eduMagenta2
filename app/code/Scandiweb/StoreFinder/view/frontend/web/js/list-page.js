/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

define([
    'uiComponent',
    'jquery',
    'mage/translate'
], function (Component, $, $t) {
    'use strict';

    return Component.extend({
        storeListSelector: '#storefinder-store-list-wrapper',
        eventListSelector: '#storefinder-event-list-wrapper',
        storeMenuSelector: '.menu-store-button',
        eventMenuSelector: '.menu-event-button',
        showListEvent: 'storefinder-show-list',
        activeClass: 'active',
        storePageUrl: null,
        eventPageUrl: null,

        /** @inheritdoc */
        initialize: function () {
            this._super();

            var self = this;
            $('[data-action="storefinder-open-stores"]').click(function () {
                self.openStores(self);
            });
            $('[data-action="storefinder-open-events"]').click(function () {
                self.openEvents(self);
            });
        },

        /**
         * @param self
         */
        openStores: function (self) {
            this.changeUrl(this.storePageUrl, $t('Stores'));
            var wrapper = $(self.storeListSelector);
            wrapper.trigger(self.showListEvent);
            wrapper.addClass(self.activeClass);
            $(self.storeMenuSelector).addClass(self.activeClass);
            $(self.eventMenuSelector).removeClass(self.activeClass);
            $(self.eventListSelector).removeClass(self.activeClass);
        },

        /**
         * @param self
         */
        openEvents: function (self) {
            this.changeUrl(this.eventPageUrl, $t('Events'));
            var wrapper = $(self.eventListSelector);
            wrapper.trigger(self.showListEvent);
            wrapper.addClass(self.activeClass);
            $(self.eventMenuSelector).addClass(self.activeClass);
            $(self.storeMenuSelector).removeClass(self.activeClass);
            $(self.storeListSelector).removeClass(self.activeClass);
        },

        changeUrl: function (url, title) {
            history.pushState({}, title, url);
            document.title = title;
        }
    });
});
