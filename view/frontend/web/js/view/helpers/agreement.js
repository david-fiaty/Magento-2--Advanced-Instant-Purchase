define([
    'jquery',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/modal',
    'Naxero_BuyNow/js/view/helpers/logger'
], function ($, BnSlider, BnModal, BnLogger) {
    'use strict';

    return {
        agreementLinkSelector: '.nbn-agreement-link',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Set the agrements events.
         */
        build: function () {
            if (this.o.jsConfig.general.enable_agreements) {
                var self = this;
                $(self.agreementLinkSelector).on('click', function (e) {
                    self.getAgreement(e);
                });
            }
        },

         /**
         * Get an agreement.
         */
        getAgreement: function (e) {
            // Prepare the request parameters
            var self = this;
            var params = {
                action: $(e.currentTarget).data('role'),
                id: $(e.currentTarget).data('id')
            };

            // Toggle the view
            BnSlider.toggleView(e);
            
            // Send the request
            $.ajax({
                type: 'POST',
                cache: false,
                url: this.o.paths.get(this.o.confirmationUrl),
                data: params,
                success: function (data) {
                    BnModal.addHtml(BnSlider.nextSlideSelector, data.html);
                },
                error: function (request, status, error) {
                    BnLogger.log(
                        __('Error retrieving the terms and conditions data'),
                        error
                    );
                }
            });
        }
    };
});
