/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'mage/url'
], function (Url) {
    'use strict';

    return {
        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Get a URL.
         */
        get: function (path) {
            var url = this.o.jsConfig.module.route + '/' + path;
            return Url.build(url);
        }
    }
});

