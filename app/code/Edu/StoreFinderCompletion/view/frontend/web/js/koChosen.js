/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

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
