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
        'aiiDisplay',
        'mage/translate',
    ],
    function ($, __, AAIdisplay) {
        'use strict';

        return {
            test: function () {
                alert('core js test');

                AAIdisplay.test();
                
                return this;
            }
        };
    }
);
