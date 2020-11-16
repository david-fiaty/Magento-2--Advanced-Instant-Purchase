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
    'Naxero_BuyNow/js/view/helpers/template'
],
function ($, __, NbnTemplate) {
    'use strict';
    return {
        regionSelector: '#region_id',
        agreementRow: '.nbn-agreement-link-row',
        agreementBoxSelector: '.nbn-agreement-box',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Additional form validation.
         */
        validate: function() {
            var params = {
                data: {
                    message: 'hello'
                }
            };

            console.log(NbnTemplate.getMessage(params));
            
            return false;
            
            // Prepare the parameters
            var errors = [];

            // Agreements validation
            /*
            if (this.aipConfig.general.enable_agreements) {
                $(this.agreementRow).removeClass('error');
                $(this.agreementRow).each(function() {
                    var input = $(this).find(this.agreementBoxSelector);
                    if (!input.is(':checked')) {
                        errors.push({
                            id: input.attr('id')
                        });
                    }
                });
            }
            */
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