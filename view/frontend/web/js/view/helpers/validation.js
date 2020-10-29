define(
    [
        'jquery',
        'mage/translate',
        'popover'
    ],
    function($, __, popover) {
        'use strict';
        return {
            agreementRow: '.aip-agreement-link-row',
            agreementBoxSelector: '.aip-agreement-box',
            inputSelectors: '.aip-select, .aip-box',
            popoverSelector: '.popover',
            buttonErrorClass: 'aip-button-error',

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
             * Display the category view product options errors.
             */
            displayOptionsError: function(obj) {
                // Prepare variables
                var self = this;
                var button = $(obj.getButtonId());

                // Clear previous errors
                self.clearErrors(button);

                // Update the button state
                button.popover({
                    title : '',
                    content : __('Please select some options'),
                    autoPlace : false,
                    trigger : 'hover',
                    placement : 'right',
                    delay : 10
                });
                button.addClass(this.buttonErrorClass);
                button.trigger('mouseover');
            },

            /**
             * Clear UI error messages.
             */
            clearErrors: function(button) {
                button.removeClass(this.buttonErrorClass);
                $(this.popoverSelector).remove();
            },

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