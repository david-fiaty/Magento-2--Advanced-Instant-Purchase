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
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal) {
    'use strict';

    return {
        modalWindow: null,

        /**
         * Create popUp window for provided element
         *
         * @param {HTMLElement} element
         */
        createPopUp: function (element) {
            var options = {
                'type': 'popup',
                'modalClass': 'popup-authentication',
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout, .nbn-login-popup',
                'buttons': []
            };

            this.modalWindow = element;
            modal(options, $(this.modalWindow));
        },

        /** Show login popup window */
        showModal: function () {
            $(this.modalWindow).modal('openModal').trigger('contentUpdated');
        }
    };
});