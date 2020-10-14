define([
    'jquery'
], function ($) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.aip-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',

        /**
         * Get a product container selector.
         */
        getProductContainer: function() {
            return this.aipConfig.isListView
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product container selector.
         */
        getProductForm: function(buttonId) {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get product form selector
            var productFormSelector = this.aipConfig.isListView
            ? this.listProductFormSelector
            : this.viewProductFormSelector;

            // Get the form
            var form = $(buttonId).closest(productContainerSelector)
            .find(productFormSelector);

            return form;
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function(buttonId) {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get the buy now data
            var buyNowData = this.getProductForm(buttonId).serialize();

            // Get the cart form data if list view
            if (this.aipConfig.isListView) {
                var cartFormData = $(buttonId)
                .closest(productContainerSelector)
                .find(this.listProductCartFormSelector)
                .serialize();

                // Add the cart form data to the purchase data
                buyNowData += '&' + cartFormData;
            }

            return buyNowData;
        },

        /**
         * Get a product options.
         */
        getOptions: function(buttonId) {
            var productContainerSelector = this.getProductContainer();
            var options = $(buttonId)
            .parents(productContainerSelector)
            .find('input[name^="super_attribute"]');

            return options;
        },

        /**
         * Checkf if a product has options.
         */
        hasOptions: function(buttonId) {
            return this.getOptions(buttonId).length > 0;
        }
    };
});
