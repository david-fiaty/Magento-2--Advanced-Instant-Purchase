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
    'Naxero_BuyNow/js/view/helpers/logger'
], function ($, __, NbnPaths, NbnLogger) {
    'use strict';

    return {
        addressFormSelector: '.form-address-edit',
        addressFormUrl: 'address/formAdd',
        saveAddressUrl: 'address/formPost',
        submitButtonSelector: '.nbn-submit',

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
            $(this.submitButtonSelector).text(__('Save address'));

            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(this.addressFormUrl),
                data: params,
                success: function (data) {
                    obj.slider.addHtml(obj.slider.currentSlideSelector, data.html);
                },
                error: function (request, status, error) {
                    NbnLogger.log(
                        __('Error retrieving the address form data'),
                        error
                    );
                }
            });
        }
    }
});
