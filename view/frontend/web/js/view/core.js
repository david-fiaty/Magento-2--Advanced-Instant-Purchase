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

define(['uiComponent'], function (Component) {
    'use strict';

    return Component.extend({
        /**
         * Default parameters.
         */
        defaults: {
            config: {}
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.config = window.naxero.buynow.config;
        },

        /**
         * Load the config.
         */
        getConfig: function () {
           return this.config;
        }
    });
});
