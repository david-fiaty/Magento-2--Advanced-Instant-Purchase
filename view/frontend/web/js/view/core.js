var helpers = [
    'agreement',
    'button',
    'header',
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

function getHelpers() {
    var paths = [];
    var prefix = 'Naxero_AdvancedInstantPurchase/js/view/helpers/';
    for (let i = 0; i < helpers.length; i++) {
        paths.push(prefix + helpers[i]);
    }

    return paths;
};

define(getHelpers(), function(agreement, button, header, logger, login, message, modal, product, select, slider, spinner, template, tree, util, validation, view) {
    'use strict';

    return {
        init: function(obj) {
            this.obj = obj;
            for (let i = 0; i < helpers.length; i++) {
                this[helpers[i]] = eval(helpers[i]);
            }
        }
    };
});