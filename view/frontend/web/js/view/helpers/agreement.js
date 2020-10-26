define([
    'jquery',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/modal',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger'
], function ($, UrlBuilder, AipSlider, AipModal, AipLogger) {
    'use strict';

    return {
        agreementLinkSelector: '.aip-agreement-link',

        /**
         * Set the agrements events
         */
        build: function (obj) {
            if (obj.jsConfig.general.enable_agreements) {
                var self = this;
                $(self.agreementLinkSelector).on('click', function (e) {
                    self.getAgreement(e, obj);
                });
            }
        },

         /**
         * Get an agreement.
         */
        getAgreement: function (e, obj) {
            // Prepare the request parameters
            var self = this;
            var params = {
                action: $(e.currentTarget).data('role'),
                id: $(e.currentTarget).data('id')
            };

            // Toggle the view
            AipSlider.toggleView(obj, e);
            
            // Send the request
            $.ajax({
                type: 'POST',
                cache: false,
                url: UrlBuilder.build(obj.confirmUrl),
                data: params,
                success: function (data) {
                    AipModal.addHtml(AipSlider.nextSlideSelector, data.html);
                },
                error: function (request, status, error) {
                    AipLogger.log(
                        obj,
                        __('Error retrieving the terms and conditions data'),
                        error
                    );
                }
            });
        }
    };
});
