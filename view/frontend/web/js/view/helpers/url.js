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

        /**
         * Get a URL.
         */
        getUrl: function(path) {
            var url = this.o.jsConfig.module.route + '/' + path;
            return UrlBuilder.build(url);
        },

        /**
         * Get the logger URL.
         */
        getLoggerUrl: function() {
            return this.getUrl(this.loggerUrl);
        },

        /**
         * Get the login URL.
         */
        getLoginUrl: function() {
            return this.getUrl(this.loginUrl);
        },

        /**
         * Get the confirmation URL.
         */
        getConfirmationUrl: function() {
            return this.getUrl(this.confirmationUrl);
        },

        /**
         * Get the save address URL.
         */
        getSaveAddressUrl: function() {
            return this.getUrl(this.saveAddressUrl);
        },

        /**
         * Get the purchase URL.
         */
        getPurchaseUrl: function() {
            return this.getUrl(this.purchaseUrl);
        }
    }
});


