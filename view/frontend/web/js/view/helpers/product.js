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
    'domReady!'
], function ($, __, NbnLogger, NbnView) {
    'use strict';

    return {
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
        attributesValid: function (product) {
            // Swatch option
            var success = true;
            //var cartForm = $('form[data-product-sku="' + product.sku + '"]');
            var attributeSelector = '.swatch-opt-' + product.id + ' .swatch-option .selected';
            //var attributes = cartForm.find(attributeSelector);

            $(attributeSelector).each(function(i, elt) { 
                console.log($(elt).attr('option-id'));
                if ($(elt).val() === 0) {
                    success = false;
                }
            });

            return false;
            return success;
        }
    };
});
