/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

define([
    'uiComponent',
    'underscore',
    'jquery',
    'ko',
    'mage/translate',
    'Magento_Ui/js/model/messageList'
], function (Component, _, $, ko, $t, messageList) {
    'use strict';

    return Component.extend({
        ajaxUrl: null,
        ajaxHttpMethod: 'POST',
        listSelector: null,

        showListEvent: 'storefinder-show-list',
        activeClass: 'active',
        activated: false,
        loadingInProgress: ko.observable(false),
        errorMessage: ko.observable(null),
        warningMessage: ko.observable(null),

        /** @inheritdoc */
        initialize: function () {
            this._super();

            if ($(this.listSelector).hasClass(this.activeClass)) {
                this.activate();
            }

            this.initListeners();
        },

        /**
         * Initialization that only happens once
         */
        activate: function () {
            if (this.activated) {
                return;
            }

            this.updateAjax({});
            this.activated = true;
        },

        initListeners: function () {
            var self = this;
            $(this.listSelector).on(this.showListEvent, function () {
                self.activate();
            });
        },

        /**
         * @param message
         * @param type
         */
        showMessage: function (message, type) {
            if (typeof(type) === 'undefined') {
                type = 'info';
            }

            if (type === 'error') {
                messageList.addErrorMessage(String(message));
            } else {
                messageList.addSuccessMessage(String(message));
            }
        },

        /**
         * @param params
         */
        updateAjax: function (params) {
            var self = this;
            this.loadingInProgress(true);
            this.clearErrorMessage();
            $.ajax({
                type: self.ajaxHttpMethod,
                cache: true,
                url: self.ajaxUrl,
                data: params,
                success: function (response) {
                    if (response.status === 'success') {
                        self.onAjaxSuccess(response);
                    } else if (typeof(response.message) !== 'undefined') {
                        self.onAjaxError(response.message);
                    } else {
                        self.onAjaxError($t('Something went wrong with request'));
                    }
                },
                error: function () {
                    self.onAjaxError($t('Something went wrong with request'));
                },
                complete: function () {
                    self.loadingInProgress(false);
                    self.onAjaxCompleted();
                }
            });
        },

        /**
         * @param response
         */
        onAjaxSuccess: function (response) {},

        /**
         * @param errorMessage
         */
        onAjaxError: function (errorMessage) {
            this.errorMessage(errorMessage);
            this.showMessage(errorMessage, 'error');
        },

        onAjaxCompleted: function () {},

        clearMessages: function () {
            this.clearErrorMessage();
            this.clearWarningMessage();
        },

        clearErrorMessage: function () {
            this.errorMessage(null);
        },

        clearWarningMessage: function () {
            this.warningMessage(null);
        }
    });
});
