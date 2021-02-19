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
        /**
         * Build a product gallery.
         */
        build: function () {
            $('.nbn-gallery-images').slick({
                slidesToShow: 1,
                infinite: true,
                speed: 500,
                dots: true
            });
        }
    };
});
