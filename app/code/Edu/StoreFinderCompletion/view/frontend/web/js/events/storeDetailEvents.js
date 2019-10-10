/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

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
