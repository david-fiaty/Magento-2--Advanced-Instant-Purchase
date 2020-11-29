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

    return {
        /**
         * Build a product quantity box.
         */  
        build: function(boxId) {
            // Prepare variables
            var quantityField = $('#' + boxId + ' .nbn-qty');
            var min = parseInt(quantityField.attr('min'));
            var max = parseInt(quantityField.attr('max'));
            var step = parseInt(quantityField.attr('step'));

            // Value change event
            quantityField.on('change', function (e) {
                if ($(e.currentTarget).hasClass('nbn-qty')) {
                    var currentValue = parseInt($(this).val());
                    var condition = currentValue && currentValue !== 'Nan' && currentValue > 0;
                    var newValue = condition ? currentValue : min;
                    $(this).val(newValue);
                }
            });

            // Minus button event
            $('#' + boxId + ' .nbn-qty-minus').on('click touch', function () {
                var quantity = parseInt(quantityField.val());
                var newQuantity = quantity - step;
                var condition = newQuantity && newQuantity !== 'Nan' && newQuantity >= min;
                newQuantity = condition ? newQuantity : min;
                quantityField.val(newQuantity);
            });

            // Plus button event
            $('#' + boxId + ' .nbn-qty-plus').on('click touch', function () {
                var quantity = parseInt(quantityField.val());
                var newQuantity = quantity + step;
                var condition = newQuantity && newQuantity !== 'Nan' && newQuantity <= max;
                newQuantity = condition ? newQuantity : max;
                quantityField.val(newQuantity);
            });
        }
    }
});
