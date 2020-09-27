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
            agreementBoxSelector: '.aip-agreement-box',
            inputSelectors: '.aip-select, .aip-box',
            attributeErrorSelector: '.aip-attribute-error',
            productContainerSelector: '.product-item',
            popoverSelector: '.popover',
            buttonErrorClass: 'aip-button-error',

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
                if (obj.isListView() && this.aipConfig.display.product_list) {
                    // Find existing options
                    var productAttributes = $(e.currentTarget)
                    .parents(this.productContainerSelector)
                    .find('input[name^="super_attribute"]');

                    // If there are attributes
                    if (productAttributes.length > 0) {
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

                        // Handle errors
                        this.displayOptionsErrors(errors, e);
                    }
                }

                return errors;
            },

            /**
             * Display the category view product options.
             */
            displayOptionsErrors: function(errors, e) {
                // Prepare variables
                var self = this;
                var productContainer = $(e.currentTarget).closest(this.productContainerSelector);
                var button = productContainer.find('.aip-button');

                // Clear previous errors
                this.clearErrors(button);

                // Process existing errors
                if (errors.length > 0) {
                    // Update the button state
                    button.popover({
                        title : '',
                        content : __('Please select the required options'),
                        autoPlace : false,
                        trigger : 'hover',
                        placement : 'right',
                        delay : 10
                    });
                    button.addClass(this.buttonErrorClass);
                    button.trigger('mouseover');

                    // Update the missing options state
                    for (var i = 0; i < errors.length; i++) {
                        var attributeContainer = productContainer
                        .find('[attribute-id="' + errors[i].id + '"]');
                        attributeContainer.css('position', 'relative');
                        attributeContainer.append('<span class="aip-attribute-error">&#10006;</span>');
                        attributeContainer.find(this.attributeErrorSelector).popover({
                            title : '',
                            content : __('Required option'),
                            autoPlace : false,
                            trigger : 'hover',
                            placement : 'bottom',
                            delay : 10
                        });
                    }

                    // Add the show/hide error events on product hover
                    $(document).on('mouseover focusin', this.productContainerSelector, function() {
                        $(this).find(self.attributeErrorSelector).show();
                    });

                    $(document).on('mouseout focusout', this.productContainerSelector, function() {
                        $(this).find(self.attributeErrorSelector).hide();
                    });
                }
            },

            /**
             * Clear UI error messages.
             */
            clearErrors: function(button) {
                button.removeClass(this.buttonErrorClass);
                $(this.attributeErrorSelector).remove();
                $(this.popoverSelector).remove();
            }
        }
    }
);