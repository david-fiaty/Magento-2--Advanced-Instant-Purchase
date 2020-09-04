/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/template',
    'mage/url',
    'text!Naxero_AdvancedInstantPurchase/template/loader.html',
], function ($, MageTemplate, UrlBuilder, LoaderTemplate) {
    'use strict';

    return {
        saveAddressUrl: 'customer/address/formPost',
        purchaseUrl: 'instantpurchase/button/placeOrder',
        productFormSelector: '#product_addtocart_form',
        showLoader: function(obj) {
            obj.getCurrentSlide().html(
                MageTemplate(LoaderTemplate)({})
            );
        },

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
            if (!state.id || !state.element.parentElement.className.includes('aii-payment-method-select')) {
                return state.text;
            }
            var iconUrl = state.element.value.split('*~*')[1];
            var iconHtml = $(
                '<span class="aii-card-icon">'
                + '<img src="' + iconUrl + '">'
                + state.text + '</span>'
            );

            return iconHtml;
        }
    };
});
