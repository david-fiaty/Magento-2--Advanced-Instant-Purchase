define(
    [
        'jquery',
        'Naxero_AdvancedInstantPurchase/js/view/helpers/template',
        'Naxero_AdvancedInstantPurchase/js/view/helpers/util'
    ],
    function ($, AipTemplate, AipUtil) {
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
                    $('head').append(AipTemplate.getHeader(
                        {
                            data: {
                                css_path: obj.jsConfig.ui.css
                            }
                        }
                    ));
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