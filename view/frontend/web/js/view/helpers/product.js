define([
    'jquery'
], function ($) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
        productContainerSelector: '',
        listProductContainerSelector: '.product-item',
        viewProductContainerSelector: '.product-info-main',

            /**
         * Get a product options.
         */
        getProductContainer: function() {
            return this.aipConfig.isListView
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product options.
         */
        getOptions: function(buttonId) {
            var productContainerSelector = this.getProductContainer();
            return $(buttonId)
            .parents(this.productContainerSelector)
            .find('input[name^="super_attribute"]');
        },

        /**
         * Checkf if a product has options.
         */
        hasOptions: function(buttonId) {
            return this.getOptions().length > 0;
        }
    };
});
