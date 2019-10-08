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
    'ko',
    'uiRegistry',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'koChosen',
    'mage/mage'
], function (Component, $, ko, registry, $t, modal) {
    'use strict';

    return Component.extend({
        modalParent: null,
        modalElement: null,
        defaultModalOptions: {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: $t('Go to your location?')
        },

        popupSelector: null,
        storefinderList: null,

        yesButtonClass: 'button-yes',
        noButtonClass: 'button-no',

        numberOfStores: ko.observable(0),
        storesNearYouMessage: ko.observable(''),
        currentLocation: null,
        searchFromCustomer: 1000,
        popupShown: false,

        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.modalElement = $(this.popupSelector);
            modal(this.getModalOptions(), this.modalElement);
            this.modalParent = this.modalElement.closest('.modal-popup').addClass('stores-near-me-popup-wrapper');
        },

        getModalOptions: function () {
            var self = this;

            return $.extend(this.defaultModalOptions, {
                buttons: [{
                    'text': $t('Yes'),
                    'class': this.yesButtonClass,
                    'click': function () {
                        self.goToMyLocation();
                    }
                }, {
                    'text': $t('No'),
                    'class': this.noButtonClass,
                    'click': function () {
                        this.closeModal();
                    }
                }]
            });
        },

        showPopup: function (numberOfStores) {
            if (this.popupShown) {
                return;
            }
            if (numberOfStores > 0) {
                if (numberOfStores === 1) {
                    this.storesNearYouMessage($t('There is at least one store near you.'));
                } else {
                    this.storesNearYouMessage($t('There are at least %1 stores near you.')
                        .replace('%1', numberOfStores));
                }
                this.popupShown = true;
                this.modalElement.modal('openModal');
            }
        },

        closePopup: function () {
            this.modalElement.modal('closeModal');
        },

        goToMyLocation: function () {
            this.getStoreFinderList().goToPoint(this.currentLocation);
            this.closePopup();
        },

        getStoreFinderList: function () {
            return registry.get(this.storefinderList);
        },

        activateGeoLocation: function () {
            var self = this;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    self.currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    var params = {
                        lat: self.currentLocation.lat,
                        lon: self.currentLocation.lng,
                        distance: self.searchFromCustomer
                    };

                    $.ajax({
                        type: 'POST',
                        url: self.ajaxUrl,
                        data: params,
                        success: function (response) {
                            if (response.status === 'success' && response.stores) {
                                self.goToMyLocation();
                            } else {
                                self.getStoreFinderList().warningMessage(
                                    $t('Sadly, we couldn\'t find any stores near you.')
                                );
                            }
                        }
                    });
                });
            }
        }
    });
});
