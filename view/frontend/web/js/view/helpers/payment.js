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
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, NbnPaths) {
    'use strict';

    return {
        addCardFormUrl: 'card/formAdd',
        submitButtonSelector: '.nbn-submit',

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
            // Prepare the parameters
            var params = {
                action: $(e.currentTarget).data('form')
            };

            // Update the modal button title
            $(this.submitButtonSelector).text(__('Save card'));

            // Get the card form
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(this.addCardFormUrl),
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
