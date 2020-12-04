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
    'Naxero_BuyNow/js/view/helpers/paths',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/modal',
    'Naxero_BuyNow/js/view/helpers/logger'
], function ($, __, NbnPaths, NbnSlider, NbnModal, NbnLogger) {
    'use strict';

    return {
        addCardFormUrl: 'card/formAdd',
        submitButtonSelector: '.nbn-submit',

        /**
         * Get the address form.
         */
        getCardForm: function (obj, e) {
            // Prepare the parameters
            var self = this;
            var params = {
                action: $(e.currentTarget).data('form')
            };

            // Update the modal button title
            $(this.submitButtonSelector).text(__('Save card'));

            // Get the card form
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(self.addCardFormUrl),
                data: params,
                success: function (data) {
                    NbnModal.addHtml(NbnSlider.nextSlideSelector, data.html);
                    $(self.submitButtonSelector).prop(
                        'disabled',
                        false
                    );
                },
                error: function (request, status, error) {
                    NbnLogger.log(
                        __('Error retrieving the card form data'),
                        error
                    );
                }
            });
        }
    }
});
