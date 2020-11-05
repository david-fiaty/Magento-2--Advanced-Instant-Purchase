/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, NbnPaths) {
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
            // Prepare teh parameters
            var params = {
                action: $(e.currentTarget).data('form')
            };

            // Update the modal button title
            $('.nbn-submit').text(__('Save'));


            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(this.addressFormUrl),
                data: params,
                success: function (data) {
                    obj.o.modal.addHtml(obj.o.slider.nextSlideSelector, data.html);
                },
                error: function (request, status, error) {
                    obj.o.logger.log(
                        __('Error retrieving the address form data'),
                        error
                    );
                }
            });
        }
    }
});
