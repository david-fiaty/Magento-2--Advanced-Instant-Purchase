define([
    'jquery',
    'mage/url',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/modal',
    'Naxero_BuyNow/js/view/helpers/logger'
], function($, UrlBuilder, AipSlider, AipModal, AipLogger) {
    'use strict';

    return {
        agreementLinkSelector: '.aip-agreement-link',

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
        },

        /**
         * Set the agrements events.
         */
        build: function() {
            if (this.o.jsConfig.general.enable_agreements) {
                var self = this;
                $(self.agreementLinkSelector).on('click', function(e) {
                    self.getAgreement(e);
                });
            }
        },

         /**
         * Get an agreement.
         */
        getAgreement: function(e) {
            // Prepare the request parameters
            var self = this;
            var params = {
                action: $(e.currentTarget).data('role'),
                id: $(e.currentTarget).data('id')
            };

            // Toggle the view
            AipSlider.toggleView(e);
            
            // Send the request
            $.ajax({
                type: 'POST',
                cache: false,
                url: self.o.url.getConfirmationUrl(),
                data: params,
                success: function(data) {
                    AipModal.addHtml(AipSlider.nextSlideSelector, data.html);
                },
                error: function(request, status, error) {
                    AipLogger.log(
                        __('Error retrieving the terms and conditions data'),
                        error
                    );
                }
            });
        }
    };
});
