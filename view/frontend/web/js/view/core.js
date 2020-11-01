/**
 * Helpers array.
 */
var helpers = [
    'agreement',
    'button',
    'logger',
    'login',
    'message',
    'modal',
    'product',
    'select',
    'slider',
    'spinner',
    'template',
    'tree',
    'util',
    'validation',
    'view'
];

/**
 * Helper file loader.
 */
function getHelperFiles() {
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
define(getHelperFiles(), function(agreement, button, logger, login, message, modal, product, select, slider, spinner, template, tree, util, validation, view) {
    'use strict';

    return {
        /**
         * Initialise the helpers.
         */
        init: function(obj) {
            for (let i = 0; i < helpers.length; i++) {
                this[helpers[i]] = eval(helpers[i]).init(obj);
            }

            return this;
        }
    };
});