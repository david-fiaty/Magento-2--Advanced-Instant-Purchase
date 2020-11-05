define([], function () {
    'use strict';

    return {
        loginUrl: 'customer/account/login',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Create a login redirection.
         */
        loginRedirect: function () {
            window.location.href = this.o.paths.get(this.loginUrl);
        },

        /**
         * Check if customer is logged in.
         */
        isLoggedIn: function () {
            return this.o.jsConfig.user.connected;
        }
    };
});
