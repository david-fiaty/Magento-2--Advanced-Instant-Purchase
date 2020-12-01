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
    'mage/url'
], function (Url) {
    'use strict';

    return {
        /**
         * Get a URL.
         */
        get: function (path) {
            var url = this.o.config.module.route + '/' + path;
            return Url.build(url);
        }
    }
});


