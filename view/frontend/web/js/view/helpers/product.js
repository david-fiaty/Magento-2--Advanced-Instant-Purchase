define([
    'jquery'
], function ($) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.aip-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',

        /**
         * Get a product container selector.
         */
        getProductContainer: function(obj) {
            return obj.jsConfig.is_list_view
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product container selector.
         */
        getProductForm: function(obj) {
            // Product container selector
            var productContainerSelector = this.getProductContainer(obj);

            // Get product form selector
            var productFormSelector = obj.jsConfig.is_list_view
            ? this.listProductFormSelector
            : this.viewProductFormSelector;

            // Get the form
            var form = $(obj.getButtonId()).closest(productContainerSelector)
            .find(productFormSelector);

            return form;
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function(obj) {
            // Product container selector
            var buttonId = obj.getButtonId();
            var productContainerSelector = this.getProductContainer(obj);

            // Get the buy now data
            var buyNowData = this.getProductForm(buttonId).serialize();

            // Get the cart form data if list view
            if (obj.jsConfig.is_list_view) {
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
        getOptions: function(obj) {
            var productContainerSelector = this.getProductContainer(obj);
            var options = $(obj.getButtonId())
            .parents(productContainerSelector)
            .find('input[name^="super_attribute"]');

            return options;
        },

        /**
         * Checkf if a product has options.
         */
        hasOptions: function(obj) {
            return this.getOptions(obj.getButtonId()).length > 0;
        }
    };
});
