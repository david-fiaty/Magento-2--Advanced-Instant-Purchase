/**
 * Naxero.com
 * Professional ecommerce integrations for Magento
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright Naxero.com
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

define(
    [
        'jquery',
        'Magento_Customer/js/model/customer',
        'aiiDisplay',
        'mage/translate',
    ],
    function ($, Customer, AAIDisplay, __) {
        'use strict';

        return {
            test: function () {

                var test = JSON.parse(window.localStorage.getItem('mage-cache-storage'))
                console.log('core.js');
                console.log(test);

                AAIDisplay.test();

                return test;
            }
        };
    }
);
