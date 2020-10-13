define([
    'jquery'
], function ($) {
    'use strict';

    return {
        productContainerSelector: '.product-item',

        /**
         * Get a product options.
         */
        getOptions: function(buttonId) {
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
