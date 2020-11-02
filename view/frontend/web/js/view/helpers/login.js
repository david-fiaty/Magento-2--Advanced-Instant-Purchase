define([
    'Naxero_BuyNow/js/model/authentication-popup'
], function(AuthPopup) {
    'use strict';

    return {
        loginBlockSelector: '.block-authentication',
        loginUrl: 'customer/account/login',

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
        },

        /**
         * Create a login popup.
         */
        loginPopup: function() {
            AuthPopup.createPopUp(this.loginBlockSelector);
            AuthPopup.showModal();
        },

        /**
         * Create a login redirection.
         */
        loginRedirect: function() {
            window.location.href = this.o.url.get(this.loginUrl);
        },

        /**
         * Check if customer is logged in.
         */
        isLoggedIn: function() {
            return this.o.jsConfig.user.connected;
        }
    };
});
