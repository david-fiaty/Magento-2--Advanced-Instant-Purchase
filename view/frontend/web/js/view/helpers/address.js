/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, Url, AipPaths) {
    'use strict';

    return {
        addressFormSelector: '.form-address-edit',
        addressFormUrl: 'address/formAdd',
        saveAddressUrl: 'address/formPost',

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
            var params = {
                action: $(e.currentTarget).data('form')
            };
            $.ajax({
                type: 'POST',
                cache: false,
                url: AipPaths.get(this.addressFormUrl),
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
