define([
    'jquery'
], function ($) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
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
