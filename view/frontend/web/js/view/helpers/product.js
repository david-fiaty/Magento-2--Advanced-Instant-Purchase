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
    'Naxero_BuyNow/js/view/helpers/product/option/select',
    'Naxero_BuyNow/js/view/helpers/product/option/swatch',
    'popover',
], function ($, __, NbnLogger, NbnView, NbnProductOptionSelect, NbnProductOptionSwatch, popover) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.nbn-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        popoverSelector: '.popover',
        productDataSelectorPrefix: '#nbn-product-data-',
        buttonErrorClass: 'nbn-button-error',
        optionHandlers: [
            'swatch',
            'select'
        ],

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Set product options events.
         */
        initOptionsEvents: function () {
            if (this.hasOptions()) {
                for (var i = 0; i < this.optionHandlers.length; i++) {
                    this.getOptionHandler(this.optionHandlers[i]).initOptionsEvents(this.getOptions());
                }
            }
        },

        /**
         * Get the option handler component.
         */
        getOptionHandler: function (optionType) {
            // Argument provided
            optionType = optionType || null;
            if (optionType) {
                var optionComponent = 'NbnProductOption'
                + optionType.charAt(0).toUpperCase() + optionType.slice(1);
                
                return eval(optionComponent);
            }

            // No argument provided
            if (NbnView.isPageView()) {
                return NbnProductOptionSwatch;
            }
            else if (NbnView.isListView()) {
                return NbnProductOptionSwatch;
            }
            else if (NbnView.isBlockView()) {
                return NbnProductOptionSelect;        
            }
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionsValues: function (obj) {
            var condition1 = this.hasOptions();
            var condition2 = obj.jsConfig.blocks.show_product && NbnView.isBlockView();
            var condition3 = !NbnView.isBlockView();
            if (condition1 && (condition2 || condition3)) {
                for (var i = 0; i < this.optionHandlers.length; i++) {
                    this.getOptionHandler(this.optionHandlers[i]).updateSelectedOptionsValues(obj);
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
            var form = $(this.o.jsConfig.product.button_selector).closest(productContainerSelector)
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
                var cartFormData = $(this.o.jsConfig.product.button_selector)
                .closest(productContainerSelector)
                .find(this.listProductCartFormSelector)
                .serialize();

                // Add the cart form data to the purchase data
                buyNowData += '&' + cartFormData;
            }

            return buyNowData;
        },

        /**
         * Product options validation.
         */
        validateOptions: function (e) {
            if (this.hasOptions(e)) {
                var errors = [];
                for (var i = 0; i < this.optionHandlers.length; i++) {
                    var optionErrors = this.getOptionHandler(this.optionHandlers[i]).getOptionsErrors(
                        this.getProductData(e)['options'],
                        e
                    );
                    Array.prototype.push.apply(errors, optionErrors); 
                }

                console.log('errors');

                console.log(errors);

                return errors.length == 0;
            }

            return true;
        },

        /**
         * Check if a product has options.
         */
        hasOptions: function (e) {
            return this.getProductData(e)['options'].length > 0;
        },

        /**
         * Get a product options.
         */
        getOptions: function (e) {
            return this.getProductData(e)['options'];
        },

        /**
         * Get updated product data for events.
         */
        getProductData: function (e) {
            var productId = e
            ? $(e.currentTarget).data('product-id')
            : this.o.jsConfig.product.id;

            return JSON.parse(
                $(this.productDataSelectorPrefix + productId).val()
            );            
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
