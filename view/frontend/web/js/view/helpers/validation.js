define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            aipConfig: window.advancedInstantPurchase,
            agreementRow: '.aip-agreement-link-row',
            inputSelectors: '.aip-select, .aip-box',

            /**
             * Additional form validation.
             */
            validate: function(noUiUpdate) {
                // Prepare the parameters
                var errors = [];
                noUiUpdate = noUiUpdate ? noUiUpdate : false;

                // Agreements validation
                if (this.aipConfig.general.enable_agreements) {
                    $(this.agreementRow).removeClass('error');
                    $(this.agreementRow).each(function() {
                        var input = $(this).find('.aip-agreement-box');
                        if (!input.is(':checked')) {
                            errors.push({
                                id: input.attr('id')
                            });
                        }
                    });
                }

                // Fields validation
                $(this.inputSelectors).each(function() {
                    if ($(this).val().length == 0) {
                        errors.push({
                            id: input.attr('id')
                        });
                    }
                });

                // UI update
                if (!noUiUpdate) {
                    $(errors).each(function(i, error) {
                        $('#' + error.id).addClass('error');
                    });
                }

                return errors.length == 0;
            },

            checkRegionState: function() {
                if ($('#region_id').prop('disabled') === true) {
                    $('#region_id').addClass('aip-region-hidden');
                    $('#region_id').removeClass('aip-region-visible');
                }
                else {
                    $('#region_id').addClass('aip-region-visible');
                    $('#region_id').removeClass('aip-region-hidden');
                }
            },

            checkOptions: function(obj, e) {
                var errors = [];
                if (obj.isListView()) {
                    $(e.currentTarget)
                    .parents('.product-item')
                    .find('input[name^="super_attribute"]')
                    .each(function() {
                        var val = $(this).val();
                        if (!val || val === 'undefined' || val.length == 0) {
                            errors.push($(this).attr('name'));
                        }
                    });
                }

                return errors;
            }
        }
    }
);