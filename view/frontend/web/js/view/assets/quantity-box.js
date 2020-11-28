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
    'domReady!'
], function ($) {
    'use strict';

    // Extend the decimals prototype
    String.prototype.getDecimals || (String.prototype.getDecimals = function () {
        var a = this,
            b = ('' + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        return b
            ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0))
            : 0;
    });

    return {
        /**
         * Build a product quantity box.
         */  
        build: function(boxId) {
            // Refresh quantity increments
            this.refresh();

            // Update event
            var self = this;
            $(document).on('updated_wc_div', function () {
                self.refresh();
            });

            // Prepare the quantity button selectors
            var selectors = '#' + boxId + ' ' + '.nbn-qty-minus';
            selectors += ', ';
            selectors += '#' + boxId + ' ' + '.nbn-qty-plus';

            // Click event
            $(selectors).on('click touch', function () {
                var a = $(this).closest('.nbn-qty-box').find('.nbn-qty');
                var b = self.prepareValue(a.val(), 0);
                var c = self.prepareValue(a.attr('max'), '');
                var d = self.prepareValue(a.attr('min'), 0);
                var e = self.prepareStepValue(a.attr('step'), 1);
                $(this).is('.nbn-qty-plus')
                    ? c && b >= c
                        ? a.val(c)
                        : a.val((b + parseFloat(e)).toFixed(e.getDecimals()))
                    : d && b <= d
                    ? a.val(d)
                    : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())),
                a.trigger('change');
            });
        },

        /**
         * Refresh the quantity increments.
         */  
        refresh: function () {
            $('div.nbn-qty-box:not(.nbn-qty-buttons-added), td.nbn-qty-box:not(.nbn-qty-buttons-added)')
            .each(function (a, b) {
                var c = $(b);
                c.addClass('nbn-qty-buttons-added');
                c.children().first().before('<input type="button" value="-" class="nbn-qty-minus" />');
                c.children().last().after('<input type="button" value="+" class="nbn-qty-plus" />');
            });
        },

        /**
         * Prepare a quantity box value.
         */  
        prepareValue: function (val, defaultValue) {
            val = parseFloat(val);
            return val && val !== '' && val !== 'NaN'
            ? val : defaultValue;
        },

        /**
         * Prepare a quantity box step value.
         */  
        prepareStepValue: function (val, defaultValue) {
            return val !== 'any' && val !== '' && val !== void 0 && parseFloat(val) !== 'NaN'
            ? val : defaultValue;
        }
    }
});
