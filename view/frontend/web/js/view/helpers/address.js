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
        addressFormSelector: '.form-address-edit',
        addressFormUrl: 'address/formAdd',
        saveAddressUrl: 'address/formPost',
        submitButtonSelector: '.nbn-submit',

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
            $(this.submitButtonSelector).text(__('Save address'));

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
