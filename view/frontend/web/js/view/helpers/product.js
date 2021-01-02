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
    'popover',
    'mage/validation',
    'domReady!'
], function ($, __, NbnLogger, NbnView, popover) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.nbn-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        popoverSelector: '.popover',
        buttonErrorClass: 'nbn-button-error',
        formSelector: '#nbn-product-params-form', 
        
        /**
         * Run a product fields validation.
         */
        validateFields: function (productId) {
            // Prepare variables
            var attributes = window.naxero.nbn.instances[productId].product.attributes;
            var options = window.naxero.nbn.instances[productId].product.options;

            // Check availability of product fields
            var hasAttributes = attributes && attributes.length > 0;
            var hasOptions = options && options.length > 0;

            // Validate the product fields
            if (hasAttributes || hasOptions) {
                $(this.formSelector).validation();
                return $(this.formSelector).validation('isValid');
            }

            return true;
        },

        /**
         * Get a product container selector.
         */
        getProductContainer: function () {
            return NbnView.isListView()
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product container selector.
         */
        getProductForm: function () {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get product form selector
            var productFormSelector = NbnView.isListView()
            ? this.listProductFormSelector
            : this.viewProductFormSelector;

            // Get the form
            var form = $(window.naxero.nbn.current.product.button_selector).closest(productContainerSelector)
            .find(productFormSelector);

            return form;
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function () {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get the buy now data
            var buyNowData = this.getProductForm().serialize();

            // Log the purchase data
            NbnLogger.log(
                __('Place order form data'),
                this.getProductForm().serializeArray()
            );

            // Get the cart form data if list view
            if (NbnView.isListView()) {
                var cartFormData = $(window.naxero.nbn.current.product.button_selector)
                .closest(productContainerSelector)
                .find(this.listProductCartFormSelector)
                .serialize();

                // Add the cart form data to the purchase data
                buyNowData += '&' + cartFormData;
            }

            return buyNowData;
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedAttributesValues: function (config) {
            var attributes = NbnProductAttributes.getAttributes(config.product.id);
            var condition1 = attributes && attributes.length > 0;
            var condition2 = config.widgets.widget_show_product && NbnView.isWidgetView();
            var condition3 = !NbnView.isWidgetView();
            if (condition1 && (condition2 || condition3)) {
                for (var i = 0; i < attributes.length; i++) {
                    NbnProductAttributes.getAttributeHandler(attributes[i]['attribute_type'])
                    .updateSelectedAttributeValue(attributes[i]);
                }
            }
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
