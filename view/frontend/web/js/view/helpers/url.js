/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'mage/url'
], function(UrlBuilder) {
    'use strict';

    return {
        loggerUrl: 'ajax/logs',
        confirmationUrl: 'ajax/confirmation',
        loginUrl: 'customer/account/login',
        productDataUrl: 'naxero-aip/ajax/product',
        saveAddressUrl: 'customer/address/formPost',
        purchaseUrl: 'naxero-aip/ajax/order',

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
        },

        getUrl: function(path) {
            var url = this.o.jsConfig.module.route + '/' + path;
            return UrlBuilder.build(url);
        },

        getLoggerUrl: function() {
            return this.getUrl(this.loggerUrl);
        },

        getLoginUrl: function() {
            return this.getUrl(this.loginUrl);
        },

        getConfirmationUrl: function() {
            return this.getUrl(this.confirmationUrl);
        },

        getProductDataUrl: function() {
            return this.getUrl(this.productDataUrl);
        },

        getSaveAddressUrl: function() {
            return this.getUrl(this.saveAddressUrl);
        },

        getPurchaseUrl: function() {
            return this.getUrl(this.purchaseUrl);
        }
    }
});


