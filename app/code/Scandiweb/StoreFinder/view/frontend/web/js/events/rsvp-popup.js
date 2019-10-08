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
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'koChosen',
    'mage/mage'
], function (Component, $, ko, $t, modal) {
    'use strict';

    return Component.extend({
        modalParent: null,
        modalElement: null,
        defaultModalOptions: {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: $t('RSVP')
        },

        formSelector: null,
        popupSelector: null,
        selectStoreSelector: null,
        emailSelector: null,
        errorMessageWrapperSelector: null,

        submitButtonClass: 'submit-button',
        closeButtonClass: 'close-button',
        errorMessageTemplate: '<div class="error">%message%</div>',

        event: ko.observable({}),
        stores: ko.observableArray([]),
        store: ko.observable({}),
        email: ko.observable(''),

        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.modalElement = $(this.popupSelector);
            modal(this.getModalOptions(), this.modalElement);
            this.modalParent = this.modalElement.closest('.modal-popup').addClass('rsvp-popup-wrapper');
        },

        getModalOptions: function () {
            var self = this;

            return $.extend(this.defaultModalOptions, {
                buttons: [{
                    'text': $t('Sign Up'),
                    'class': this.submitButtonClass,
                    'click': function () {
                        self.submitForm();
                    }
                }, {
                    'text': $t('Close'),
                    'class': this.closeButtonClass,
                    'click': function () {
                        this.closeModal();
                    }
                }]
            });
        },

        showPopup: function (event, storeId) {
            this.event(event);
            this.stores(event.stores);
            this.modalParent.find('.' + this.submitButtonClass).show();
            this.modalParent.find('.' + this.closeButtonClass).hide();
            this.modalElement.find(this.errorMessageWrapperSelector).html('');
            if (typeof(storeId) !== 'undefined') {
                $(this.selectStoreSelector).val(storeId);
            }
            this.modalElement.find(this.selectStoreSelector).trigger('chosen:updated');
            this.modalElement.removeClass('completed').addClass('in-progress');
            this.modalElement.modal('openModal');
        },

        closePopup: function () {
            this.modalElement.modal('closeModal');
        },

        submitForm: function () {
            this.modalElement.find(this.formSelector).submit();
        },

        submit: function () {
            if (this.modalElement.hasClass('completed')) {
                return;
            }

            this.email(this.modalElement.find(this.emailSelector).val());
            this.store(this.getStoreById(this.modalElement.find(this.selectStoreSelector).val()));
            if (this.validateData()) {
                this.ajaxSubmit(
                    this.modalElement.find(this.formSelector).attr('action'),
                    {
                        email: this.email(),
                        event_id: this.event().event_id,
                        store_id: this.store().store_id,
                        form_key: this.getFormKey()
                    }
                );
            }
        },

        ajaxSubmit: function (url, params) {
            var self = this;
            $.ajax({
                type: 'POST',
                url: url,
                data: params,
                success: function (response) {
                    if (response.status === 'success') {
                        self.onAjaxSuccess(response);
                    } else if (typeof(response.error_message) !== 'undefined') {
                        self.onAjaxError(response.error_message);
                    } else {
                        self.onAjaxError($t('Something went wrong with request'));
                    }
                },
                error: function () {
                    self.onAjaxError($t('Something went wrong with request'));
                }
            });
        },

        onAjaxSuccess: function () {
            this.modalParent.find('.' + this.submitButtonClass).hide();
            this.modalParent.find('.' + this.closeButtonClass).show();
            this.modalElement.removeClass('in-progress').addClass('completed');
        },

        onAjaxError: function (message) {
            this.showError(message);
        },

        showError: function (message) {
            this.modalElement.find(this.errorMessageWrapperSelector)
                .html(this.errorMessageTemplate.replace('%message%', message));
        },

        validateData: function () {
            $(this.formSelector).validation();
            return (this.validateEmail() && this.event !== null);
        },

        validateEmail: function () {
            return this.modalElement.find(this.emailSelector).valid();
        },

        isStoreArray: function () {
            return this.stores().length > 1;
        },

        getOnlyStoreId: function () {
            var stores = this.stores();
            if (typeof(stores[0]) !== 'undefined') {
                return stores[0].store_id;
            } else {
                return -1;
            }
        },

        getTitleText: function () {
            return $t('Sign Up for %1').replace('%1', this.event().event_name);
        },

        getStoreById: function (storeId) {
            var store = {};
            $.each(this.stores(), function (index, value) {
                if (value.store_id === storeId) {
                    store = value;
                    return false;
                }
            });

            return store;
        },

        isEqualMonths: function (item) {
            return (item.date_start_month === item.date_end_month);
        },

        isEqualDays: function (item) {
            return (item.date_start_day === item.date_end_day);
        },

        getMonth: function () {
            var item = this.event();
            if (this.isEqualMonths(item)) {
                return item.date_start_month;
            }

            return item.date_start_month + '-' + item.date_end_month;
        },

        getDay: function () {
            var item = this.event();
            if (this.isEqualMonths(item) && this.isEqualDays(item)) {
                return item.date_start_day;
            }

            return item.date_start_day + '-' + item.date_end_day;
        },

        getTimeString: function () {
            if (this.event().custom_time) {
                return this.event().custom_time;
            }

            return this.event().date_start_time + ' - ' + this.event().date_end_time;
        },

        getFormKey: function() {
            var cookie = ' ' + document.cookie;
            var search = ' form_key=';
            var setStr = null;
            var offset = 0;
            var end = 0;
            if (cookie.length > 0) {
                offset = cookie.indexOf(search);
                if (offset != -1) {
                    offset += search.length;
                    end = cookie.indexOf(';', offset);
                    if (end == -1) {
                        end = cookie.length;
                    }
                    setStr = unescape(cookie.substring(offset, end));
                }
            }

            return(setStr);
        },

        getThankYouTitleText: function () {
            return $t('Thank You for Signing Up for %1').replace('%1', this.event().event_name);
        }
    });
});
