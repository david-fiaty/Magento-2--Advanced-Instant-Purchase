define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            validate: function () {
                return $('#agreement-1').is(":checked");
            }
        }
    }
);