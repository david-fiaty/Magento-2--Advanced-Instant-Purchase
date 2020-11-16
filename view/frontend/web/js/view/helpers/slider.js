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
    'slick',
], function ($, __, slick) {
    'use strict';

    return {
        sliderSelector: '#nbn-slider',
        nextSlideSelector: '#nbn-next-slide-container',
        cancelButtonSelector: '.action-dismiss span',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Create a login popup.
         */
        build: function () {
            $(this.sliderSelector).slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: false,
                speed: 500,
                arrows: false
            });
        },

        /**
         * Get the current slide.
         */
        getCurrentSlide: function () {
            var slide = (this.o.isSubView)
            ? this.nextSlideSelector
            : this.o.popupContentSelector;

            return $(slide);
        },

        /**
         * Show the AJAX loader.
         */
        showLoader: function () {
            this.getCurrentSlide().html(this.o.loader);
        },

        /**
         * Handles the view switch.
         */
        toggleView: function (e) {
            // Handle the event
            e = e || null;
            if (e) {
                e.preventDefault();
            }

            // Handle the toggle logic
            this.showLoader();
            if (this.o.isSubView) {
                $(this.sliderSelector).slick('slickPrev');
                this.o.isSubView = false;
                $(this.cancelButtonSelector).text(__('Cancel'));
                $(this.sliderSelector).slick('unslick');
            } else {
                $(this.sliderSelector).slick('slickNext');
                $(this.cancelButtonSelector).text(__('Back'));
                this.o.isSubView = true;
            }
        }
    };
});
