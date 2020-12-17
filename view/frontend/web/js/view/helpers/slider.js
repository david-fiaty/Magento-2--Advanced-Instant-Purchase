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
        currentSlideSelector: '.slick-current',
        cancelButtonSelector: '.action-dismiss span',
        slideContainerSelector: '.nbn-slide-container',

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
            return $(this.slideContainerSelector).first();
        },

        /**
         * Show the AJAX loader.
         */
        showLoader: function () {
            this.getCurrentSlide().html(window.naxero.nbn.ui.loader);
        },

        /**
         * Add HTML to a container.
         */
        addHtml: function (target, html) {
            $(target).html(html);
            $(this.modalWrapperSelector).animate(
                {minHeight: $(target).height()  + 'px'}
                ,
                300
            );
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
            if (window.naxero.nbn.current.isSubView) {
                $(this.sliderSelector).slick('slickPrev');
                window.naxero.nbn.current.isSubView = false;
                $(this.cancelButtonSelector).text(__('Cancel'));
                $(this.sliderSelector).slick('unslick');
            } else {
                $(this.sliderSelector).slick('slickNext');
                $(this.cancelButtonSelector).text(__('Back'));
                window.naxero.nbn.current.isSubView = true;
            }
        }
    };
});
