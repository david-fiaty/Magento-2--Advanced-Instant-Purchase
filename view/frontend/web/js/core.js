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
        'mage/translate',
        'Magento_Customer/js/customer-data'
    ],
    function ($, __, CustomerData) {
        'use strict';

        const AII_SECTION_NAME = 'advancedInstantPurchase';
        const CART_SECTION_NAME = 'cart';

        return {
            log: function(data) {
                var config = this.getConfig();
                if (config.general.debug_enabled && config.general.console_logging_enabled) {
                    console.log(data);
                }
            },

            getConfig: function() {
                var cartData = CustomerData.get(CART_SECTION_NAME)();

                if (cartData && cartData.hasOwnProperty(AII_SECTION_NAME)) {
                    return cartData[AII_SECTION_NAME];
                }

                return {};
            },

        };
    }
);
