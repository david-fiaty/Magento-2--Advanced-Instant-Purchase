/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return {
        
        /**
         * Create popUp window for provided element
         *
         * @param {HTMLElement} element
         */
        createPopUp: function (elementSelector) {
            var options = {
                'type': 'popup',
                'modalClass': 'popup-authentication',
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout, .aip-login-popup',
                'buttons': []
            };

            this.modalWindow = elementSelector;
            modal(options, $(this.modalWindow));
            $(this.modalWindow).modal('openModal').trigger('contentUpdated');
        }
    };
});
