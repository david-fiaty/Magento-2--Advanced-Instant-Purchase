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
    'mage/translate'
],
function ($, __) {
    'use strict';
    return {
        regionSelector: '#region_id',
        agreementRow: '.nbn-agreement-link-row',
        agreementBoxSelector: '.nbn-agreement-box',
        agreementErrorMessageSelector: '#nbn-checkout-agreements .messages',

        /**
         * Additional form validation.
         */
        validate: function() {
            // Prepare the parameters
            var self = this;
            var errors = [];

            // Agreements validation
            if (window.naxero.nbn.current.popups.popup_enable_agreements) {
                // Reset the errors
                $(this.agreementErrorMessageSelector).hide();
                $(this.agreementRow).removeClass('error');

                // Perform the validation
                $(this.agreementRow).each(function() {
                    var input = $(this).find(self.agreementBoxSelector);
                    if (!input.is(':checked')) {
                        errors.push({
                            id: input.attr('id')
                        });
                    }
                });
            }

            // Show error message
            if (errors.length > 0) {
                $(this.agreementRow).addClass('error');
                $(this.agreementErrorMessageSelector).show();
                
                return false;
            }

            return true;
        },

        /**
         * Check the region state in address form.
         */
        checkRegionState: function () {
            var regionField = $(this.regionSelector);
            if (regionField.prop('disabled') === true) {
                regionField.addClass('nbn-region-hidden');
                regionField.removeClass('nbn-region-visible');
            } else {
                regionField.addClass('nbn-region-visible');
                regionField.removeClass('nbn-region-hidden');
            }
        }
    }
});