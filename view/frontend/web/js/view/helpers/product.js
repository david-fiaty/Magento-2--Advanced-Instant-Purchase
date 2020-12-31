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
    'Naxero_BuyNow/js/view/helpers/product/options',
    'Naxero_BuyNow/js/view/helpers/product/attributes/select',
    'Naxero_BuyNow/js/view/helpers/product/attributes/swatch',
    'popover',
], function ($, __, NbnLogger, NbnView, NbnProductAttributes, NbnProductOptions, NbnProductAttributeSelect, NbnProductAttributeSwatch, popover) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.nbn-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        popoverSelector: '.popover',
        buttonErrorClass: 'nbn-button-error',
        optionHandlers: [
            'swatch',
            'select'
        ],

        /**
         * Set product attributes events.
         */
        initAttributesEvents: function (config) {
            var attributes = this.getAttributes(config);
            if (attributes && attributes.length > 0) {
                for (var i = 0; i < attributes.length; i++) {
                    this.getAttributeHandler(attributes[i]['attribute_type'])
                    .initAttributeEvent(attributes[i]);
                }
            }
        },

        /**
         * Set product options events.
         */
        initOptionsEvents: function (config) {
            var options = this.getOptions(config);
            if (options && options.length > 0) {
                for (var i = 0; i < options.length; i++) {
                    this.getOptionHandler(options[i]['type'])
                    .initOptionEvent(options[i]);
                }
            }
        },

        /**
         * Get the option handler component.
         */
        getOptionHandler: function (optionType) {
            
        },

        /**
         * Get the attribute handler component.
         */
        getAttributeHandler: function (attributeType) {
            // Argument provided
            attributeType = attributeType || null;
            if (attributeType) {
                var optionComponent = 'NbnProductOption'
                + attributeType.charAt(0).toUpperCase() + attributeType.slice(1);

                return eval(optionComponent);
            }

            // No argument provided
            if (NbnView.isPageView()) {
                return NbnProductAttributeSwatch;
            } else if (NbnView.isListView()) {
                return NbnProductAttributeSwatch;
            } else if (NbnView.isWidgetView()) {
                return NbnProductAttributeSelect;
            }
        },

        /**
         * Update the selected product attributes values.
         */
        updateSelectedAttributesValues: function () {
            var attributes = this.getAttributes();
            var condition1 = attributes && attributes.length > 0;
            var condition2 = window.naxero.nbn.current.widgets.widget_show_product && NbnView.isWidgetView();
            var condition3 = !NbnView.isWidgetView();
            if (condition1 && (condition2 || condition3)) {
                for (var i = 0; i < attributes.length; i++) {
                    this.getAttributeHandler(attributes[i]['attribute_type'])
                    .updateSelectedAttributeValue(attributes[i]);
                }
            }
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
         * Product custom options validation.
         */
        validateOptions: function (e) {
            // Prepare variables
            var options = this.getOptionsFromEvent(e);
            var condition1 = options && options.length > 0;
            var errors = 0;

            // Loop through the product options
            if (condition1) {
                for (var i = 0; i < options.length; i++) {
                    // Validate the option
                    var error = this.getAttributeHandler(option[i]['type'])
                    .getAttributeErrors(option[i], e)
                    .length > 0;

                    // Register the error
                    if (error) {
                        errors++;
                    }
                }

                return errors == 0;
            }

            return true;

        },

        /**
         * Product attributes validation.
         */
        validateAttributes: function (e) {
            // Prepare variables
            var attributes = this.getAttributesFromEvent(e);
            var condition1 = attributes && attributes.length > 0;
            var errors = 0;

            // Loop through the product attributes
            if (condition1) {
                for (var i = 0; i < attributes.length; i++) {
                    // Validate the attribute
                    var error = this.getAttributeHandler(attributes[i]['attribute_type'])
                    .getAttributeErrors(attributes[i], e)
                    .length > 0;

                    // Register the error
                    if (error) {
                        errors++;
                    }
                }

                return errors == 0;
            }

            return true;
        },

        /**
         * Check if a product has attributes.
         */
        hasAttributes: function (productId) {
            return this.getProductData(productId)['attributes'].length > 0;
        },

        /**
         * Get a product attributes from a click event.
         */
        getAttributesFromEvent: function (e) {
            var productId = $(e.currentTarget).data('product-id');
            return this.getProductData(productId)['attributes'];
        },

        /**
         * Get a product options from a click event.
         */
        getOptionsFromEvent: function (e) {
            var productId = $(e.currentTarget).data('product-id');
            return this.getProductData(productId)['options'];
        },
        
        /**
         * Get a product attributes.
         */
        getAttributes: function () {
            var productId = window.naxero.nbn.current.product.id;
            return this.getProductData(productId)['attributes'];
        },

        /**
         * Get updated product data for events.
         */
        getProductData: function (productId) {
            return window.naxero.nbn.instances[productId];
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
                content : __('Please select options for this product'),
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
