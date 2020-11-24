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

/**
 * Helpers array.
 */
var helpers = [
    'agreement',
    'logger',
    'login',
    'message',
    'widget',
    'modal',
    'product',
    'select',
    'slider',
    'spinner',
    'template',
    'tree',
    'util',
    'validation',
    'view',
    'paths',
    'address',
    'payment'
];

/**
 * Helper file loader.
 */
function getHelperFiles()
{
    var paths = [];
    var prefix = 'Naxero_BuyNow/js/view/helpers/';
    for (let i = 0; i < helpers.length; i++) {
        paths.push(prefix + helpers[i]);
    }

    return paths;
};

/**
 * Core component.
 */
define(getHelperFiles(), function (agreement, logger, login, message, widget, modal, product, select, slider, spinner, template, tree, util, validation, view, paths, address, payment) {
    'use strict';

    return {
        /**
         * Initialise the helpers.
         */
        init: function (obj) {
            for (let i = 0; i < helpers.length; i++) {
                this[helpers[i]] = eval(helpers[i]).init(obj);
            }

            return this;
        }
    };
});