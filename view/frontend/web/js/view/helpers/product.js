define([
    'jquery'
], function ($) {
    'use strict';

    return {
        aiiConfig: window.advancedInstantPurchase,
        productBoxSelector: '.aii-product-box',

        /**
         * Create a login popup.
         */
        loadBoxView: function(container) {
            if (this.aiiConfig.display.popup_product) {
                var box = $(container).find(this.productBoxSelector);
                box.find("img[data-role='image']").attr('src', this.aiiConfig.product.url);
                box.find("p[data-role='name']").text(this.aiiConfig.product.name);
                box.find("p[data-role='price']").text(this.aiiConfig.product.price);
            }
        }
    };
});
