/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 define([
    'Naxero_BuyNow/js/model/authentication-popup'
], function (AuthPopup) {
    'use strict';

    return {
        loginBlockSelector: '.block-authentication',
        loginUrl: 'customer/account/login',

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
