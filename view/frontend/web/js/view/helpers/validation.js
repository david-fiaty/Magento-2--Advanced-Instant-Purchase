define(
    [
        'jquery',
        'mage/translate'
    ],
    function($, __) {
        'use strict';
        return {
            agreementRow: '.aip-agreement-link-row',
            agreementBoxSelector: '.aip-agreement-box',
            inputSelectors: '.aip-select, .aip-box',

            /**
             * Additional form validation.
             */
            validate: function(obj) {
                // Prepare the parameters
                var errors = [];

                // Agreements validation
                if (obj.jsConfig.general.enable_agreements) {
                    $(this.agreementRow).removeClass('error');
                    $(this.agreementRow).each(function() {
                        var input = $(this).find(this.agreementBoxSelector);
                        if (!input.is(':checked')) {
                            errors.push({
                                id: input.attr('id')
                            });
                        }
                    });
                }

                // Fields validation
                $(this.inputSelectors).each(function() {
                    var val = $(this).val();
                    if (val && val.length == 0) {
                        errors.push({
                            id: input.attr('id')
                        });
                    }
                });

                return errors.length == 0;
            },

            /**
             * Update the button state.
             */
            /*
            updateButtonState: function(obj) {
                var errors = this.validateOptions(obj, true);
                var disabled = !(errors.length == 0);
                $(obj.getButtonId()).prop('disabled', disabled);
            },
            */

            /**
             * Check the region state in address form.
             */
            checkRegionState: function() {
                if ($('#region_id').prop('disabled') === true) {
                    $('#region_id').addClass('aip-region-hidden');
                    $('#region_id').removeClass('aip-region-visible');
                } else {
                    $('#region_id').addClass('aip-region-visible');
                    $('#region_id').removeClass('aip-region-hidden');
                }
            }
        }
    }
);