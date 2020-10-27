define([
    'jquery'
], function($) {
    'use strict';

    return {
        /**
         * Check if the current product is in list view.
         */
        isListView: function(obj) {
            return obj.jsConfig.product.display == 'list';
        },

        /**
         * Check if the current product is in block view.
         */
        isBlockView: function(obj) {
            return obj.jsConfig.product.display == 'block'
            || obj.jsConfig.product.display == 'widget';
        },

        /**
         * Check if the current product is in page view.
         */
        isPageView: function(obj) {
            return !this.isBlockView(obj) && !this.isListView(obj);
        },

        /**
         * Check if the current product has options.
         */
        hasOptions: function(obj) {
            return obj.jsConfig.product.has_options;
        }
    };

});
