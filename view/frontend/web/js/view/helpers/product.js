define([
    'jquery'
], function ($) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
        productBoxSelector: '.aip-product-box',

        /**
         * Create a login popup.
         */
        loadBoxView: function(container) {
            if (this.aipConfig.display.popup_product) {
                var box = $(container).find(this.productBoxSelector);
                box.find("img[data-role='image']").attr('src', this.aipConfig.product.url);
                box.find("p[data-role='name']").text(this.aipConfig.product.name);
                box.find("p[data-role='price']").text(this.aipConfig.product.price);
            }
        }
    };
});
