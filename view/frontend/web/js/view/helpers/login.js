define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, Modal) {
    'use strict';

    return {
        loginBlockSelector: '.block-authentication',
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
        },

        /**
         * Create popUp window for provided element
         */
        createPopUp: function () {
            var options = {
                'type': 'popup',
                'modalClass': 'popup-authentication',
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout, .aip-login-popup',
                'buttons': []
            };

            modal(options, $(this.loginBlockSelector));
            $(this.modalWindow).modal('openModal').trigger('contentUpdated');
        }
    };
});
