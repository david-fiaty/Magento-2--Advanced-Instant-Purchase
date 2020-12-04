/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 define([
    'jquery',
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/util',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/address',
    'Naxero_BuyNow/js/view/helpers/payment',
    'select2'
], function ($, __, NbnUtil, NbnSlider, NbnAddress, NbnPayment, select2) {
    'use strict';

    return {
        listSelector: '.nbn-select',
        linkSelector: '.nbn-new, .nbn-plus-icon',
        paymentMethodSelector: '#nbn-payment-method-select',
        otherMethodsToggleSelector: '#nbn-show-other-methods',
        otherMethodsSelector: '#nbn-other-method-select',
        addressLinkSelector: '.nbn-address-link',
        cardLinkSelector: '.nbn-card-link',
        optionFieldSelector: '.nbn-widget-option',

        /**
         * Create a login popup.
         */
        build: function () {
            // Product options select 2
            var placeholder = $(this.optionFieldSelector)
            .find('option[data-placeholder="*"]')
            .data('placeholder');
            $(this.optionFieldSelector).select2 ({
                placeholder: placeholder,
                minimumResultsForSearch: -1,
                theme: 'classic'
            });

            // Initialise the select lists
            var self = this;
            $(this.listSelector).select2({
                placeholder: __('Select an option'),
                language: self.getLocale(window.naxero.nbn.current.user.language),
                theme: 'classic',
                templateResult: NbnUtil.formatIcon,
                templateSelection: NbnUtil.formatIcon
            });

            // Set the lists events
            $(this.listSelector).on('change', function () {
                // Get the current field value
                var thisFieldValue = $(this).val();

                // Set the new field value
                var newFieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                ? NbnUtil.getOptionPublicHash(thisFieldValue)
                : thisFieldValue;

                // Update the hidden target field value
                var targetFieldId = $(this).attr('data-field');
                $('input[name="' + targetFieldId + '"]').val(newFieldValue);
            });

            // Other payment methods toggle
            $(this.otherMethodsSelector).prop('disabled', true);
            $(this.otherMethodsToggleSelector).on('click touch', function () {
                // Other methods select state
                $(self.otherMethodsSelector).prop(
                    'disabled',
                    !$(this).is(':checked')
                );

                // Saved cards select state
                $(self.paymentMethodSelector).prop(
                    'disabled',
                    $(this).is(':checked')
                );
            });

            // Set the new address link event
            $(this.addressLinkSelector).on('click touch', function (e) {
                NbnSlider.toggleView(e);
                NbnAddress.getAddressForm(self, e);
            });

            // Set the new card link event
            $(this.cardLinkSelector).on('click touch', function (e) {
                NbnSlider.toggleView(e);
                NbnPayment.getCardForm(self, e);
            });
        },

        getLocale: function (code) {
            return code.split('_')[0];
        }
    };
});
