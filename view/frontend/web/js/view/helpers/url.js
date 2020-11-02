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
        purchaseUrl: 'ajax/order',
        confirmationUrl: 'ajax/confirmation',
        loginUrl: 'customer/account/login',
        saveAddressUrl: 'customer/address/formPost',

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

        getSaveAddressUrl: function() {
            return this.getUrl(this.saveAddressUrl);
        },

        getPurchaseUrl: function() {
            return this.getUrl(this.purchaseUrl);
        }
    }
});


