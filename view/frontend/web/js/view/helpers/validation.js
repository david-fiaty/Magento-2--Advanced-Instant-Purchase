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

 define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            agreementRow: '.nbn-agreement-link-row',
            agreementBoxSelector: '.nbn-agreement-box',
            inputSelectors: '.nbn-select, .nbn-box',

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
            validate: function () {
                // Prepare the parameters
                var errors = [];

                // Agreements validation
                if (this.o.jsConfig.general.enable_agreements) {
                    $(this.agreementRow).removeClass('error');
                    $(this.agreementRow).each(function () {
                        var input = $(this).find(this.agreementBoxSelector);
                        if (!input.is(':checked')) {
                            errors.push({
                                id: input.attr('id')
                            });
                        }
                    });
                }

                // Fields validation
                $(this.inputSelectors).each(function () {
                    var val = $(this).val();
                    if (val && val.length == 0) {
                        errors.push({
                            id: input.attr('id')
                        });
                    }
                });

                return errors.length == 0;
            },

            /**
             * Check the region state in address form.
             */
            checkRegionState: function () {
                if ($('#region_id').prop('disabled') === true) {
                    $('#region_id').addClass('nbn-region-hidden');
                    $('#region_id').removeClass('nbn-region-visible');
                } else {
                    $('#region_id').addClass('nbn-region-visible');
                    $('#region_id').removeClass('nbn-region-hidden');
                }
            }
        }
    }
);