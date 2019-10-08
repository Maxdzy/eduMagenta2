/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */
require([
    'jquery',
    'ko',
    'mage/translate',
    'Magento_Ui/js/lib/knockout/template/renderer',
    'chosen'
], function ($, ko, $t, renderer) {
    if (typeof(ko.bindingHandlers.chosen) === 'undefined' && typeof($().chosen) === 'function') {
        ko.bindingHandlers.chosen = {
            init: function (element, valueAccessor, allBindingsAccessor) {
                var allBindings = allBindingsAccessor(),
                    options = {default: $t('Select one...')};

                $.extend(options, allBindings.chosen);
                $(element).attr('data-placeholder', options.default);
            },
            update: function (element) {
                if (!$(element).hasClass('koChosen-active')) {
                    $(element).chosen().addClass('koChosen-active');
                } else {
                    $(element).trigger('chosen:updated');
                }
            }
        };

        renderer.addAttribute('chosen');
    }
});
