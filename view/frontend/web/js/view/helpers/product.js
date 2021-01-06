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
    'Naxero_BuyNow/js/view/helpers/product/attributes',
    'popover',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function ($, __, NbnLogger, NbnView, NbnProductAttributes, popover) {
    'use strict';

    return {
        popoverSelector: '.popover',
        buttonErrorClass: 'nbn-button-error',
        
        /**
         * Initialise the product fields events
         */
        initFields: function (productId) {
            // Prepare variables
            var attributes = window.naxero.nbn.instances[productId].product.attributes;

            // Check availability of product fields
            var hasAttributes = attributes && attributes.length > 0;

            // List product swatch fields events
            if (NbnView.isListView() && hasAttributes) {
                for (var i = 0; i < attributes.length; i++) {
                    if (attributes[i].attribute_type == 'swatch') {
                        NbnProductAttributes.initFields(attributes[i]);
                    }
                }
            }
        },

        /**
         * Run a product fields validation.
         */
        validateFields: function (productId) {
            // Prepare variables
            var attributes = window.naxero.nbn.instances[productId].product.attributes;
            var options = window.naxero.nbn.instances[productId].product.options;
            var productFormSelector = this.getProductFormSelector(productId);

            // Check availability of product fields
            var hasAttributes = attributes && attributes.length > 0;
            var hasOptions = options && options.length > 0;

            // Widget product fields validation
            if (NbnView.isWidgetView() && (hasAttributes || hasOptions)) {
                $(productFormSelector).validation();
                return $(productFormSelector).validation('isValid');
            }

            // List product swatch fields validation
            if (NbnView.isListView() && hasAttributes) {
                var errors = 0;
                for (var i = 0; i < attributes.length; i++) {
                    if (attributes[i].attribute_type == 'swatch') {
                        // Build the target hidden field selector
                        var hiddenField = '#nbn-super-attribute-' + attributes[i].product_id
                        + '-' + attributes[i].attribute_id;

                        // Check the hidden field value
                        var val = $(hiddenField).val();
                        var fieldIsValid = val && val.length > 0 && parseInt(val) > 0;
                        
                        // Update the error count
                        if (!fieldIsValid) errors++;
                    }
                }

                return errors == 0;
            }

            return true;
        },

        /**
         * Get a product form selector.
         */
        getProductFormSelector: function (productId) {
            if (NbnView.isListView()) return  '#nbn-list-form-' + productId;
            else if (NbnView.isWidgetView()) return  '#nbn-widget-form-' + productId;
            else return  '#product_addtocart_form';
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function (productId) {
            // Product container selector
            var productFormSelector = this.getProductFormSelector(productId);

            // Todo - remove logging code
            console.log('-----> getProductFormData');
            console.log(productFormSelector);
            console.log(productFormSelector.serializeArray());

            // Get the buy now data
            var buyNowData = $(productFormSelector).serialize();

            // Log the purchase data
            NbnLogger.log(
                __('Place order form data'),
                this.getProductForm().serializeArray()
            );

            return buyNowData;
        },

        /**
         * Get the order confirmation form data.
         */
        getOrderFormData: function (productId) {
            var orderFormSelector = '#nbn-order-form-' + productId;

            console.log('getOrderFormData');
            console.log($(orderFormSelector).serializeArray());

            return $(orderFormSelector).serialize();
        },

        /**
         * Display the product options errors.
         */
        displayErrors: function (e) {
            // Prepare variables
            var self = this;
            var button = $(e.currentTarget);

            // Clear previous errors
            self.clearErrors(e);

            // Update the button state
            button.popover({
                title : '',
                content : __('Please check the invalid fields'),
                autoPlace : false,
                trigger : 'hover',
                placement : 'right',
                delay : 10
            });
            button.addClass(this.buttonErrorClass);
            button.trigger('mouseover');
        },

        /**
         * Clear UI error messages.
         */
        clearErrors: function (e) {
            $(e.currentTarget).removeClass(this.buttonErrorClass);
            $(this.popoverSelector).remove();
        }
    };
});
