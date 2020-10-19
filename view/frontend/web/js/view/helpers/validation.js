define(
    [
        'jquery',
        'mage/translate',
        'Naxero_AdvancedInstantPurchase/js/view/helpers/product',
        'popover'
    ],
    function ($, __, AipProduct) {
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
            validate: function(obj, noUiUpdate) {
                // Prepare the parameters
                var errors = [];
                noUiUpdate = noUiUpdate ? noUiUpdate : false;

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

                // UI update
                if (!noUiUpdate) {
                    $(errors).each(function(i, error) {
                        $('#' + error.id).addClass('error');
                    });
                }

                return errors.length == 0;
            },

            /**
             * Initialize the product options validation.
             */
            initOptionsValidation: function(obj) {
                var self = this;
                var errors = [];
                var productAttributes = AipProduct.getOptions(obj);
                var condition1 = productAttributes.length > 0;
                var condition2 = obj.jsConfig.buttons.state_disabled == 1;
                if (condition1 && condition2) {
                    productAttributes.each(function() {
                        $(this).on('change', function() {
                            self.updateButtonState(obj);
                        });
                    });
                }
            },

            /**
             * Update the button state.
             */
            updateButtonState: function(obj) {
                var errors = this.validateOptions(obj, true);
                var disabled = !(errors.length == 0);
                $(obj.jsConfig.buttonSelector).prop('disabled', disabled);
            },

            /**
             * Check if a product has options errors.
             */
            hasOptionError: function(obj) {
                return this.validateOptions(obj, true).length > 0;
            },

            /**
             * Validate the category view product options.
             */
            validateOptions: function(obj, noUiUpdate) {
                // UI updates
                noUiUpdate = noUiUpdate ? noUiUpdate : false;

                // Errors array
                var errors = [];

                // Find existing options
                var productAttributes = AipProduct.getOptions(obj);
                
                // If there are attributes, check errors
                var errors = this.checkOptionsErrors(productAttributes);

                // Display errors if needed
                if (!noUiUpdate) {
                    this.displayOptionsErrors(obj, errors);
                }

                return errors;
            },

            /**
             * Check the category view product options errors.
             */
            checkOptionsErrors: function(productAttributes) {
                var errors = [];
                if (productAttributes.length > 0) {
                    return this.getOptionsErrors(productAttributes);
                }

                return errors;
            },

            /**
             * Get the category view product options errors.
             */
            getOptionsErrors: function(productAttributes) {
                var errors = [];
                productAttributes.each(function() {
                    var val = $(this).val();
                    if (!val || val === 'undefined' || val.length == 0) {
                        var name = $(this).attr('name');
                        errors.push({
                            id: name.match(/\d+/)[0],
                            name: name
                        });
                    }
                });

                return errors;
            },

            /**
             * Display the category view product options.
             */
            displayOptionsErrors: function(obj, errors) {
                if (errors.length > 0) {
                    // Prepare variables
                    var self = this;
                    var button = $(obj.jsConfig.buttonSelector);

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
                }
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
                }
                else {
                    $('#region_id').addClass('aip-region-visible');
                    $('#region_id').removeClass('aip-region-hidden');
                }
            }
        }
    }
);