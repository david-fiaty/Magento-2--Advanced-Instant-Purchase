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
        productBoxContainerSelector: '.nbn-product-box-container',
        confirmationContainerSelector: '#nbn-confirmation-content',
        optionFieldSelector: '#nbn-option',
        optionSelectorPrefix: '#nbn-option-',
        popoverSelector: '.popover',
        productDataSelectorPrefix: '#nbn-product-data-',
        buttonErrorClass: 'nbn-button-error',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Get the option handler component.
         */
        getOptionHandler: function () {
            if (NbnView.isListView() && this.hasOptions()) {
               return NbnProductOptionSwatch;
            }
            else if (NbnView.isBlockView()) {
                return NbnProductOptionSelect;
            }
        },

        /**
         * Set product options events.
         */
        initOptionsEvents: function () {
            if (!NbnView.isPageView()) {
                this.getOptionHandler().initOptionsEvents(this.o);
            }
        },

        /**
         * Validate the product options.
         */
        validateOptions: function () {
            if (!NbnView.isPageView()) {
                this.getOptionHandler().validateOptions(this.o);
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
                return this.getOptionHandler().getOptionsErrors(e).length == 0;
            }

            return true;
        },

        /**
         * Check if a product has options.
         */
        hasOptions: function (e) {
            var product = this.getProductData(e);

            return product.options.length
            && product.options.length > 0;
        },

        /**
         * Get updated product data for events.
         */
        getProductData: function (e) {
            e = e || null;
            var productData = this.o.jsConfig.product;
            if (e) {
                var productId = $(e.currentTarget).data('product-id');
                productData = JSON.parse(
                    $(this.productDataSelectorPrefix + productId).val()
                );            
            }
            
            return productData;
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
