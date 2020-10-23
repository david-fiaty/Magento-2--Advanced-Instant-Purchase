define(
    [
        'jquery',
        'mage/translate',
        'Naxero_AdvancedInstantPurchase/js/view/helpers/template',
        'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
        'Naxero_AdvancedInstantPurchase/js/view/helpers/logger'
    ],
    function ($, __, AipTemplate, AipUtil, AipLogger) {
        'use strict';

        return {
            /**
             * Set the page HTML header.
             */
            setHeader: function(obj) {
                // Append the CSS
                this.loadHeader(obj);

                // Set the CSS loaded flag
                window.naxero = {
                    aip: {
                        css: true
                    }
                };
            },
        
            /**
             * Load the page HTML header.
             */
            loadHeader: function(obj) {
                if (!this.isHeaderLoaded()) {
                    // Build the headers= data
                    var params = {
                        data: {
                            css_path: obj.jsConfig.ui.css
                        }
                    };

                    // Add the header declarations
                    $('head').append(AipTemplate.getHeader(params));

                    // Log the event
                    AipLogger.log(
                        obj,
                        __('Loaded the HTML page header declarations'),
                        params
                    );
                }
            },

            /**
             * Check if the HTML page header is loaded.
             */
            isHeaderLoaded: function() {
                return AipUtil.has(window, 'naxero.aip.css', true);
            }
        };
    }
);