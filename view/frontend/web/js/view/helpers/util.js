/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/product'
], function ($, UrlBuilder, AipProduct) {
    'use strict';

    return {
        saveAddressUrl: 'customer/address/formPost',
        purchaseUrl: 'naxero-aip/ajax/order',
        addressFormSelector: '.form-address-edit',

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
        getCurrentForm: function(obj) {
            var form = obj.isSubView 
            ? $(this.addressFormSelector)
            : AipProduct.getProductForm(obj);

            return form;
        },

        /**
         * Get the current form.
         */
        getCurrentFormData: function(obj) {
            var form = obj.isSubView 
            ? this.getAddressFormData()
            : AipProduct.getProductFormData(obj);

            return form;
        },

        /**
         * Get the address form data.
         */
        getAddressFormData: function() {
            return $(this.addressFormSelector).serialize();
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
        },

        /**
         * Check if an object has a property.
         */
        has: function(target, path, value) {
            if (typeof target !== 'object' || target === null) { return false; }
                var parts = path.split('.');
                while(parts.length) {
                    var property = parts.shift();
                    if (!(target.hasOwnProperty(property))) {
                        return false;
                    }
                    target = target[property];
                }
                if (value) {
                    return target === value;
                }
                else {
                    return true;
                }
            }
        }
    }
);
