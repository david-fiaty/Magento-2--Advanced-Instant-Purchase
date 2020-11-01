define([
    'jquery'
], function($) {
    'use strict';

    return {

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
        },

        /**
         * Check if the current product is in list view.
         */
        isListView: function() {
            return this.o.jsConfig.product.display == 'list';
        },

        /**
         * Check if the current product is in block view.
         */
        isBlockView: function() {
            return this.o.jsConfig.product.display == 'block'
            || this.o.jsConfig.product.display == 'widget';
        },

        /**
         * Check if the current product is in page view.
         */
        isPageView: function() {
            return !this.isBlockView() && !this.isListView();
        },

        /**
         * Check if the current product has options.
         */
        hasOptions: function() {
            return this.o.jsConfig.product.has_options;
        }
    };

});
