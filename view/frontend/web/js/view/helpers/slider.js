define([
    'jquery',
    'mage/translate',
    'slick',
], function ($, __, slick) {
    'use strict';

    return {
        sliderSelector: '#aip-slider',
        nextSlideSelector: '#aip-next-slide-container',

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
                $('.action-dismiss span').text(__('Cancel'));
                $(this.sliderSelector).slick('unslick');
            } else {
                $(this.sliderSelector).slick('slickNext');
                $('.action-dismiss span').text(__('Back'));
                this.o.isSubView = true;
            }
        }
    };
});
