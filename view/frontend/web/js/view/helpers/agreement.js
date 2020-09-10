define([
    'jquery',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider'
], function ($, UrlBuilder, AipSlider) {
    'use strict';

    return {
        aipConfig: window.advancedInstantPurchase,
        agreementLinkSelector: '.aip-agreement-link',

        /**
         * Set the agrements events
         */
        build: function(obj) {
            if (this.aipConfig.general.enable_agreements) {
                var self = this;
                $(self.agreementLinkSelector).on('click', function(e) {
                    self.getAgreement(e, obj);
                });
            }
        },

         /**
         * Get an agreement.
         */
        getAgreement: function(e, obj) {
            // Prepare the request parameters
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
                    $(AipSlider.nextSlideSelector).html(data.html);
                },
                error: function (request, status, error) {
                    obj.log(error);
                }
            });
        }
    };
});
