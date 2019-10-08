/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

define([
    'storefinderEventList',
    'uiRegistry'
], function (Component, registry) {
    'use strict';

    return Component.extend({
        storeId: null,

        /**
         * Initialization that only happens once
         */
        activate: function () {
            if (this.activated) {
                return;
            }

            this.updateAjax({
                store: this.storeId
            });
            this.activated = true;
        },

        showRsvpPopup: function (item) {
            if (this.rsvpPopup === null) {
                return;
            }

            registry.get(this.rsvpPopup).showPopup(item, this.storeId);
        }
    });
});
