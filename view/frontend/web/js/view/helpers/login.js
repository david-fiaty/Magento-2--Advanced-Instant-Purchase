define([
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/model/authentication-popup'
], function (UrlBuilder, AuthPopup) {
    'use strict';

    return {
        loginUrl: 'customer/account/login',
        loginBlockSelector: '.block-authentication',

        /**
         * Create a login popup.
         */
        loginPopup: function () {
            AuthPopup.createPopUp(this.loginBlockSelector);
            AuthPopup.showModal();
        },

        /**
         * Create a login redirection.
         */
        loginRedirect: function () {
            window.location.href = UrlBuilder.build(this.loginUrl);
        },

        /**
         * Check if customer is logged in.
         */
        isLoggedIn: function (obj) {
            return obj.jsConfig.user.connected;
        }
    };
});
