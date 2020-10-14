define([
    'jquery'
], function ($) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.tocart-form',
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
            var productFormSelector = this.aipConfig.isListView
            ? this.listProductFormSelector
            : this.viewProductFormSelector;
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
