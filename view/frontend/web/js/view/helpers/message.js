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
    'Naxero_BuyNow/js/view/helpers/slider'
], function ($, NbnSlider) {
    'use strict';

    return {
        cancelButtonSelector: '.action-close',

        /**
         * Clear all visible errors.
         */
        clearErrors: function (slide) {
            slide.find('.messages').remove();
            slide.find('input').removeClass('mage-error');
            slide.find('div.mage-error').remove();
        },

        /**
         * Check the AJAX response.
         */
        checkResponse: function (data, e) {
            var cssClass;
            if (data.success === false) {
                // Add the main message
                cssClass = 'mage-error';
                this.show('error', data.messages.main);

                // Add the field messages
                if (data.messages.fields.length > 0) {
                    for (var i = 0; i < data.messages.fields.length; i++) {
                        let err = data.messages.fields[i];
                        let item = "input[name='" + err.id + "'],";
                        item += " input[name='" + err.id + "[]']";
                        $('<div generated="true" class="' + cssClass + '">' + err.txt + '</div>').insertAfter(item);
                        $(item).addClass(cssClass);
                    }
                }
            } else if (data.hasOwnProperty('response')) {
                $(this.cancelButtonSelector).trigger('click');
            } else {
                this.show('success', data.messages.main);
            }
        },

        /**
         * Show the error messages.
         */
        show: function (type, str) {
            var slide = NbnSlider.getCurrentSlide();
            this.clearErrors(slide);
            slide.prepend(NbnLoader);
            slide.find('.message').addClass(type);
            slide.find('.message-text').text(str);
            slide.find('.messages').show();
        }
    };
});
