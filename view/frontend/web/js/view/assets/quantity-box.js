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
        build: function() {
            // Refresh quantity increments
            this.refresh();

            // Update event
            var self = this;
            $(document).on('updated_wc_div', function () {
                self.refresh();
            }),

            // Click event
            $(document).on('click', '.plus, .minus', function () {
                var a = $(this).closest('.quantity').find('.qty'),
                    b = parseFloat(a.val()),
                    c = parseFloat(a.attr('max')),
                    d = parseFloat(a.attr('min')),
                    e = a.attr('step');
                (b && '' !== b && 'NaN' !== b) || (b = 0),
                    ('' !== c && 'NaN' !== c) || (c = ''),
                    ('' !== d && 'NaN' !== d) || (d = 0),
                    ('any' !== e &&
                        '' !== e &&
                        void 0 !== e &&
                        'NaN' !== parseFloat(e)) ||
                        (e = 1),
                    $(this).is('.plus')
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
            $('div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)')
            .each(function (a, b) {
                var c = $(b);
                c.addClass('buttons_added');
                c.children().first().before('<input type="button" value="-" class="minus" />');
                c.children().last().after('<input type="button" value="+" class="plus" />');
            });
        }
    }
});
