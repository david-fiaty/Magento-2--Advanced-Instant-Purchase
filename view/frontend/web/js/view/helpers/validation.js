define(
    [
        'jquery',
        'mage/translate',
        'popover'
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
            },

            /**
             * Check the category view product options.
             */
            checkOptions: function(obj, e) {
                // Error array
                var errors = [];

                // Check all options fields
                if (obj.isListView()) {
                    $(e.currentTarget)
                    .parents('.product-item')
                    .find('input[name^="super_attribute"]')
                    .each(function() {
                        var val = $(this).val();
                        if (!val || val === 'undefined' || val.length == 0) {
                            var name = $(this).attr('name');
                            errors.push({
                                id: name.match(/\d+/)[0],
                                name: name
                            });
                        }
                    });

                    // Handle errors
                    this.displayOptionsErrors(errors, e);
                }

                return errors;
            },

            /**
             * Display the category view product options.
             */
            displayOptionsErrors: function(errors, e) {
                // Clear previous errors
                var button = $(e.currentTarget).closest('.product-item').find('.aip-button');
                button.removeClass('aip-button-error');

                // Process existing errors
                if (errors.length > 0) {
                    button.popover({
                        title : '',
                        content : __('Please select the required options'),
                        autoPlace : false,
                        trigger : 'hover',
                        placement : 'right',
                        delay : 10
                    });
                    button.addClass('aip-button-error');
                    button.trigger('mouseover');

                    // Add the error class
                    /*
                    var popoverContent = $(e.currentTarget).closest('.product-item').find('.popover__content');
                    popoverContent.addClass('popover__content__error');

                    // Remove the error class button focusout event
                    var button = $(e.currentTarget).closest('.product-item').find('.aip-button');
                    popoverContent.removeClass('primary');
                    button.on('focusout', function() {
                        popoverContent.removeClass('popover__content__error aip-button-error');
                    });
                    */
                }
            }
        }
    }
);