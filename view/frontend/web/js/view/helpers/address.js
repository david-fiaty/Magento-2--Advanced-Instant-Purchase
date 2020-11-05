/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'mage/url'
], function ($, __, Url) {
    'use strict';

    return {
        addressFormSelector: '.form-address-edit',
        saveAddressUrl: 'customer/address/formPost',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Get the address form data.
         */
        getAddressFormData: function () {
            return $(this.addressFormSelector).serialize();
        },

        /**
         * Get the address form.
         */
        getAddressForm: function (obj, e) {
            var self = this;
            var params = {
                action: $(e.currentTarget).data('form')
            };
            $.ajax({
                type: 'POST',
                cache: false,
                url: Url.build(this.saveAddressUrl),
                data: params,
                success: function (data) {
                    obj.o.modal.addHtml(obj.o.slider.nextSlideSelector, data.html);
                    $(obj.o.button.submitButtonSelector).prop(
                        'disabled',
                        false
                    );
                },
                error: function (request, status, error) {
                    obj.o.logger.log(
                        __('Error retrieving the form data'),
                        error
                    );
                }
            });
        }
    }
});
