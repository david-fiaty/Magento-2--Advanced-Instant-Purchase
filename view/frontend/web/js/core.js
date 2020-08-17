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
        'uiComponent',
        'Magento_Customer/js/customer-data'
    ],
    function ($, __, Component, CustomerData) {
        'use strict';

        const AII_SECTION_NAME = 'advancedInstantPurchase';

        return Component.extend({
            defaults: {
                aiiConfig: {},
            },

            /** @inheritdoc */
            initialize: function() {
                var aiiConfig = CustomerData.get(AII_SECTION_NAME);
                this._super();
                this.setConfigData(aiiConfig());
                aiiConfig.subscribe(this.setConfigData, this);
            },

            /**
             * Log data to the browser console.
             *
             * @param {Object} data
             */
            log: function(data) {
                if (this.aiiConfig.general.debug_enabled && this.aiiConfig.general.console_logging_enabled) {
                    console.log(data);
                }
            },

            /**
             * Set the config data.
             *
             * @param {Object} data
             */
            setConfigData: function (data) {
                this.aiiConfig = data;
            }  
        });
    }
);
