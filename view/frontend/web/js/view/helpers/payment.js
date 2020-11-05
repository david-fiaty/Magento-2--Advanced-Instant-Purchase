/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, AipPaths) {
    'use strict';

    return {
        addCardFormUrl: 'card/formAdd',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Get the address form.
         */
        getCardForm: function (obj, e) {
            var params = {
                action: $(e.currentTarget).data('form')
            };
            $.ajax({
                type: 'POST',
                cache: false,
                url: AipPaths.get(this.addCardFormUrl),
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
                        __('Error retrieving the card form data'),
                        error
                    );
                }
            });
        }
    }
});
