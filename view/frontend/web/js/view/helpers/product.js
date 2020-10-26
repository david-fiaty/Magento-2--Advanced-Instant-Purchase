define([
    'jquery',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger'
], function($, AipLogger) {
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
            return obj.isListView()
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
            var productFormSelector = obj.isListView()
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
            var productContainerSelector = this.getProductContainer(obj);

            // Get the buy now data
            var buyNowData = this.getProductForm(obj).serialize();

            // Log the purchase data
            AipLogger.log(
                obj,
                __('Place order purchase data'),
                this.getProductForm(obj).serializeArray()
            );

            // Get the cart form data if list view
            if (obj.isListView()) {
                var cartFormData = $(obj.getButtonId())
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
        }
    };
});
