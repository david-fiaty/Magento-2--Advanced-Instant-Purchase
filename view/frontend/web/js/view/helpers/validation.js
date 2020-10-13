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
            aipConfig: window.advancedInstantPurchase,
            agreementRow: '.aip-agreement-link-row',
            agreementBoxSelector: '.aip-agreement-box',
            inputSelectors: '.aip-select, .aip-box',
            attributeErrorSelector: '.aip-attribute-error',
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
             * Checkf if a product has options errors.
             */
            hasOptionError: function(obj) {
                return this.validateOptions(obj).length > 0;
            },

            /**
             * Check the category view product options.
             */
            validateOptions: function(obj) {
                // Errors array
                var errors = [];

                // Find existing options
                var productAttributes = AipProduct.getOptions(obj.jsConfig.buttonSelector);

                // If there are attributes, check errors
                var errors = this.checkOptionsErrors(
                    obj.jsConfig.buttonSelector,
                    productAttributes
                );
        
                return errors;
            },

            /**
             * Check the category view product options errors.
             */
            checkOptionsErrors: function(buttonId, productAttributes) {
                var errors = [];
                if (productAttributes.length > 0) {
                    var errors = this.getOptionsErrors(productAttributes);
                    this.displayOptionsErrors(buttonId, errors);
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
            displayOptionsErrors: function(buttonId, errors) {
                if (errors.length > 0) {
                    // Prepare variables
                    var self = this;
                    var productContainer = $(buttonId).closest(AipProduct.productContainerSelector);
                    var button = productContainer.find('.aip-button');

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

                    // Update the missing options state
                    for (var i = 0; i < errors.length; i++) {
                        var attributeContainer = productContainer
                        .find('[attribute-id="' + errors[i].id + '"]');
                        attributeContainer.css('position', 'relative');
                        attributeContainer.append('<div class="aip-attribute-error"><div>&#10006;</div></div>');
                        attributeContainer.find(this.attributeErrorSelector).popover({
                            title : '',
                            content : __('Required option'),
                            autoPlace : false,
                            trigger : 'hover',
                            placement : 'right',
                            delay : 10
                        });
                    }

                    // Add the show/hide error events on product hover
                    $(document).on('mouseover focusin', AipProduct.productContainerSelector, function() {
                        $(this).find(self.attributeErrorSelector).show();
                    });

                    $(document).on('mouseout focusout', AipProduct.productContainerSelector, function() {
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