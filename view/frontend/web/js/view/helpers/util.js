/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/url'
], function ($, UrlBuilder) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
        saveAddressUrl: 'customer/address/formPost',
        purchaseUrl: 'instantpurchase/button/placeOrder',
        productFormSelector: '#product_addtocart_form',

        /**
         * Get the modal confirmation URL.
         */
        getConfirmUrl: function(isSubView) {
            var url = isSubView ? this.saveAddressUrl : this.purchaseUrl;
            return UrlBuilder.build(url);
        },

        /**
         * Get the current form.
         */
        getCurrentForm: function(isSubView) {
            var form = isSubView ? '.form-address-edit' : this.productFormSelector;
            return $(form);
        },

        /**
         * Get a card option public hash.
         */
        getOptionPublicHash: function(val) {
            return val.split('*~*')[0];
        },

        /**
         * Format a card icon.
         */
        formatIcon: function(state) {
            if (!state.id || !state.element.parentElement.className.includes('aip-payment-method-select')) {
                return state.text;
            }
            var iconUrl = state.element.value.split('*~*')[1];
            var iconHtml = $(
                '<span class="aip-card-icon">'
                + '<img src="' + iconUrl + '">'
                + state.text + '</span>'
            );

            return iconHtml;
        }
    };
});
