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

 define([], function () {
    'use strict';

    return {
        logCount: 1,
        logTitleCss: 'font-weight: bold; color: blue;',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Log data to the browser console.
         */
        log: function (msg, data) {
            // Default data value
            data = data || null;

            // Handle the logging display
            if (this.isConsoleLoggingEnabled()) {
                this.logToConsole(msg, data);
            }
        },

        /**
         * Handle the data console logging logic.
         */
        logToConsole: function (msg, data) {
            // Log title
            console.log(
                this.getLogTitle(),
                this.logTitleCss
            );
                
            // Log the event message
            console.log('|-- ' + msg)

            // Log event data
            console.log(data);

            // Log count
            this.logCount++;
        },
 
        /**
         * Check if console logging is enabled.
         */
        isConsoleLoggingEnabled: function () {
            return this.o.jsConfig.general.debug_enabled
            && this.o.jsConfig.general.console_logging_enabled;
        },

        /**
         * Get a log title.
         */
        getLogTitle: function () {
            return '%c[' + this.logCount + '][' + this.o.jsConfig.module.title + ']';
        },

        /**
         * Get the target button for UI logging.
         */
        getButtonSelector: function () {
            return '#' + this.logViewerButtonClass + '-' + this.o.jsConfig.product.id;
        }
        };
 });
