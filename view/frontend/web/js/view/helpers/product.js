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
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/view',
    'mage/validation',
    'mage/cookies',
    'popover',
    'domReady!'
], function ($, __, NbnLogger, NbnView) {
    'use strict';

    return {
        popoverSelector: '.popover',
        buttonErrorClass: 'nbn-button-error',

        /**
         * Get a product form selector.
         */
        getProductFormSelector: function (productId) {
            if (NbnView.isListView()) return  '#nbn-list-form-' + productId;
            else return  '#product_addtocart_form';
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function (productId) {
            // Get the buy now data
            var data = $(this.getProductFormSelector(productId)).serialize();

            // Log the purchase data
            NbnLogger.log(
                __('Place order form data'),
                $(this.getProductFormSelector(productId)).serializeArray()
            );

            return data;
        },

        /**
         * Get the order confirmation form data.
         */
        getOrderFormData: function (productId) {
            var productData = $(this.getProductFormSelector(productId)).serialize();
            var orderData = $('#nbn-order-form-' + productId).serialize();

            return productData + '&' + orderData;
        },

        /**
         * Validate product list attributes.
         */
        attributesValid: function (target) {
            // Swatch option
            var success = true;
            var productId = $(target).data('product-id');
            $('.swatch-opt-' + productId).find('.swatch-attribute').each(function(i, elt) { 
                var isRequired = $(elt).find('.swatch-attribute-options').attr('aria-required');
                if (isRequired === 'true') {
                    var selectedValue = parseInt($(elt).attr('option-selected'));
                    if (isNaN(selectedValue)) {
                        success = false;
                    }
                }
            });

            // Error display
            if (!success) {
                this.displayErrors(target);
            }

            return success;
        },

        /**
         * Display the product options errors.
         */
         displayErrors: function (target) {
            // Clear previous errors
            this.clearErrors(target);

            // Update the button state
            $(target).popover({
                title : '',
                content : __('Please check the invalid fields'),
                autoPlace : false,
                trigger : 'hover',
                placement : 'right',
                delay : 10
            })
            .addClass(this.buttonErrorClass)
            .trigger('mouseover');
        },

        /**
         * Clear UI error messages.
         */
         clearErrors: function (target) {
            $(target).removeClass(this.buttonErrorClass);
            $(this.popoverSelector).remove();
        }
    };
});
