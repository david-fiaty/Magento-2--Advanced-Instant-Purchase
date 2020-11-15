/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 define([
    'jquery',
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, NbnSlider, NbnLogger, NbnPaths) {
    'use strict';

    return {
        agreementLinkSelector: '.nbn-agreement-link',
        agreementsUrl: 'order/agreements',

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
                $(self.agreementLinkSelector).on('click touch', function (e) {
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
                id: $(e.currentTarget).data('id')
            };

            // Toggle the view
            NbnSlider.toggleView(e);
            
            // Send the request
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(this.agreementsUrl),
                data: params,
                success: function (data) {
                    self.o.modal.addHtml(NbnSlider.nextSlideSelector, data.html);
                },
                error: function (request, status, error) {
                    NbnLogger.log(
                        __('Error retrieving the terms and conditions data'),
                        error
                    );
                }
            });
        }
    };
});
