/**
 * Website Cost Calculator - Frontend JavaScript
 */

(function($) {
    'use strict';

    // Calculator state
    const WCCCalculator = {
        currentStep: 1,
        totalSteps: 5,
        selections: {
            websiteType: null,
            pageRange: null,
            designOption: null,
            features: [],
            ecommerceFeatures: [],
            seoMarketing: [],
            timeline: null,
            maintenance: null
        },
        costs: {
            base: 0,
            pages: 0,
            design: 0,
            features: 0,
            ecommerce: 0,
            seo: 0,
            timeline: 0,
            maintenance: 0
        },
        currency: wccFrontend.currencySymbol || '$',
        currencyPosition: wccFrontend.currencyPosition || 'before',

        /**
         * Initialize the calculator
         */
        init: function() {
            this.bindEvents();
            this.updateProgress();
            this.updateFloatingPrice();
        },

        /**
         * Bind all event handlers
         */
        bindEvents: function() {
            const self = this;

            // Navigation buttons
            $('#wcc-next-btn').on('click', function() {
                self.nextStep();
            });

            $('#wcc-prev-btn').on('click', function() {
                self.prevStep();
            });

            // Progress step clicks
            $('.wcc-progress-step').on('click', function() {
                const step = parseInt($(this).data('step'));
                if (step < self.currentStep) {
                    self.goToStep(step);
                }
            });

            // Website type selection
            $('input[name="website_type"]').on('change', function() {
                self.handleWebsiteTypeChange($(this));
            });

            // Page range selection
            $('input[name="page_range"]').on('change', function() {
                self.handleRadioChange($(this), 'pageRange', 'pages');
            });

            // Design option selection
            $('input[name="design_option"]').on('change', function() {
                self.handleRadioChange($(this), 'designOption', 'design');
            });

            // Timeline selection
            $('input[name="timeline"]').on('change', function() {
                self.handleTimelineChange($(this));
            });

            // Maintenance selection
            $('input[name="maintenance"]').on('change', function() {
                self.handleMaintenanceChange($(this));
            });

            // Feature checkboxes
            $('input[name="features[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'features', 'features');
            });

            // E-commerce feature checkboxes
            $('input[name="ecommerce_features[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'ecommerceFeatures', 'ecommerce');
            });

            // SEO/Marketing checkboxes
            $('input[name="seo_marketing[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'seoMarketing', 'seo');
            });

            // Contact form submission
            $('#wcc-contact-form').on('submit', function(e) {
                e.preventDefault();
                self.handleFormSubmit($(this));
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (e.key === 'Enter' && self.currentStep < self.totalSteps) {
                    // Don't trigger on form inputs
                    if (!$(e.target).is('input, textarea, button')) {
                        self.nextStep();
                    }
                }
            });
        },

        /**
         * Handle website type change
         */
        handleWebsiteTypeChange: function($input) {
            const name = $input.data('name');
            const price = parseFloat($input.data('price')) || 0;

            this.selections.websiteType = {
                value: $input.val(),
                name: name,
                price: price
            };
            this.costs.base = price;

            // Show/hide e-commerce section based on selection
            const isEcommerce = name.toLowerCase().includes('e-commerce') || 
                               name.toLowerCase().includes('ecommerce') ||
                               name.toLowerCase().includes('store');
            
            if (isEcommerce) {
                $('#wcc-ecommerce-section').slideDown(300);
            } else {
                $('#wcc-ecommerce-section').slideUp(300);
                // Clear e-commerce selections
                $('input[name="ecommerce_features[]"]').prop('checked', false);
                this.selections.ecommerceFeatures = [];
                this.costs.ecommerce = 0;
            }

            this.updateCalculations();
            this.updateCardSelection($input);
        },

        /**
         * Handle radio button changes
         */
        handleRadioChange: function($input, selectionKey, costKey) {
            const name = $input.data('name');
            const price = parseFloat($input.data('price')) || 0;

            this.selections[selectionKey] = {
                value: $input.val(),
                name: name,
                price: price
            };
            this.costs[costKey] = price;

            this.updateCalculations();
            this.updateButtonSelection($input);
        },

        /**
         * Handle timeline change
         */
        handleTimelineChange: function($input) {
            const name = $input.data('name');
            const multiplier = parseFloat($input.data('multiplier')) || 1;

            this.selections.timeline = {
                value: $input.val(),
                name: name,
                multiplier: multiplier
            };

            this.updateCalculations();
            this.updateButtonSelection($input);
        },

        /**
         * Handle maintenance change
         */
        handleMaintenanceChange: function($input) {
            const name = $input.data('name');
            const price = parseFloat($input.data('price')) || 0;

            this.selections.maintenance = {
                value: $input.val(),
                name: name,
                price: price
            };
            this.costs.maintenance = price;

            this.updateCalculations();
            this.updateButtonSelection($input);
        },

        /**
         * Handle checkbox changes
         */
        handleCheckboxChange: function($input, selectionKey, costKey) {
            const name = $input.data('name');
            const price = parseFloat($input.data('price')) || 0;
            const value = $input.val();

            if ($input.is(':checked')) {
                this.selections[selectionKey].push({
                    value: value,
                    name: name,
                    price: price
                });
            } else {
                this.selections[selectionKey] = this.selections[selectionKey].filter(
                    item => item.value !== value
                );
            }

            // Calculate total for this category
            this.costs[costKey] = this.selections[selectionKey].reduce(
                (total, item) => total + item.price, 0
            );

            this.updateCalculations();
            this.updateCheckboxSelection($input);
        },

        /**
         * Update card selection visual
         */
        updateCardSelection: function($input) {
            $input.closest('.wcc-options-cards').find('.wcc-card').removeClass('selected');
            $input.closest('.wcc-card').addClass('selected');
        },

        /**
         * Update button selection visual
         */
        updateButtonSelection: function($input) {
            $input.closest('.wcc-options-buttons').find('.wcc-button-option').removeClass('selected');
            $input.closest('.wcc-button-option').addClass('selected');
        },

        /**
         * Update checkbox selection visual
         */
        updateCheckboxSelection: function($input) {
            if ($input.is(':checked')) {
                $input.closest('.wcc-checkbox-option').addClass('selected');
            } else {
                $input.closest('.wcc-checkbox-option').removeClass('selected');
            }
        },

        /**
         * Calculate and update all totals
         */
        updateCalculations: function() {
            // Calculate subtotal (before timeline multiplier)
            let subtotal = this.costs.base + 
                          this.costs.pages + 
                          this.costs.design + 
                          this.costs.features + 
                          this.costs.ecommerce + 
                          this.costs.seo;

            // Apply timeline multiplier
            const multiplier = this.selections.timeline ? this.selections.timeline.multiplier : 1;
            const timelineCost = subtotal * (multiplier - 1);
            
            // Calculate grand total
            const grandTotal = subtotal + timelineCost;

            // Store for summary
            this.costs.timeline = timelineCost;
            this.subtotal = subtotal;
            this.grandTotal = grandTotal;

            // Update floating price
            this.updateFloatingPrice();
        },

        /**
         * Update the floating price display
         */
        updateFloatingPrice: function() {
            const total = this.grandTotal || 0;
            $('#wcc-floating-amount').text(this.formatPrice(total));
        },

        /**
         * Format price with currency
         */
        formatPrice: function(amount) {
            const formatted = amount.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            if (this.currencyPosition === 'after') {
                return formatted + this.currency;
            }
            return this.currency + formatted;
        },

        /**
         * Go to next step
         */
        nextStep: function() {
            if (!this.validateCurrentStep()) {
                return;
            }

            if (this.currentStep < this.totalSteps) {
                this.goToStep(this.currentStep + 1);
            }
        },

        /**
         * Go to previous step
         */
        prevStep: function() {
            if (this.currentStep > 1) {
                this.goToStep(this.currentStep - 1);
            }
        },

        /**
         * Go to specific step
         */
        goToStep: function(step) {
            // Hide current step
            $('#wcc-step-' + this.currentStep).removeClass('active');

            // Show new step
            this.currentStep = step;
            $('#wcc-step-' + this.currentStep).addClass('active');

            // Update progress
            this.updateProgress();

            // Update navigation buttons
            this.updateNavigation();

            // If on summary step, update summary
            if (this.currentStep === this.totalSteps) {
                this.updateSummary();
            }

            // Scroll to top of calculator
            $('html, body').animate({
                scrollTop: $('#wcc-calculator').offset().top - 50
            }, 300);
        },

        /**
         * Validate current step
         */
        validateCurrentStep: function() {
            let isValid = true;
            let errorMessage = '';

            switch (this.currentStep) {
                case 1:
                    if (!this.selections.websiteType) {
                        isValid = false;
                        errorMessage = 'Please select a website type.';
                    }
                    break;
                case 2:
                    // Page range and design are optional but recommended
                    break;
                case 3:
                    // Features are optional
                    break;
                case 4:
                    // Extras are optional
                    break;
            }

            if (!isValid) {
                this.showError(errorMessage);
            }

            return isValid;
        },

        /**
         * Show error message
         */
        showError: function(message) {
            // Remove existing error
            $('.wcc-error-message').remove();

            // Add error message
            const $error = $('<div class="wcc-error-message">' + message + '</div>');
            $('#wcc-step-' + this.currentStep + ' .wcc-step-header').after($error);

            // Shake animation
            $error.addClass('shake');

            // Remove after 3 seconds
            setTimeout(function() {
                $error.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },

        /**
         * Update progress bar and steps
         */
        updateProgress: function() {
            // Update progress bar fill
            const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            $('#wcc-progress-fill').css('width', progress + '%');

            // Update step indicators
            $('.wcc-progress-step').each(function() {
                const step = parseInt($(this).data('step'));
                $(this).removeClass('active completed');

                if (step < WCCCalculator.currentStep) {
                    $(this).addClass('completed');
                } else if (step === WCCCalculator.currentStep) {
                    $(this).addClass('active');
                }
            });
        },

        /**
         * Update navigation buttons
         */
        updateNavigation: function() {
            // Previous button
            if (this.currentStep > 1) {
                $('#wcc-prev-btn').show();
            } else {
                $('#wcc-prev-btn').hide();
            }

            // Next button
            if (this.currentStep < this.totalSteps) {
                $('#wcc-next-btn').show();
            } else {
                $('#wcc-next-btn').hide();
            }
        },

        /**
         * Update summary display
         */
        updateSummary: function() {
            const $summaryItems = $('#wcc-summary-items');
            $summaryItems.empty();

            // Add website type
            if (this.selections.websiteType) {
                this.addSummaryItem($summaryItems, 'Website Type', 
                    this.selections.websiteType.name, 
                    this.selections.websiteType.price);
            }

            // Add page range
            if (this.selections.pageRange && this.selections.pageRange.price > 0) {
                this.addSummaryItem($summaryItems, 'Number of Pages', 
                    this.selections.pageRange.name, 
                    this.selections.pageRange.price);
            }

            // Add design option
            if (this.selections.designOption && this.selections.designOption.price > 0) {
                this.addSummaryItem($summaryItems, 'Design', 
                    this.selections.designOption.name, 
                    this.selections.designOption.price);
            }

            // Add features
            if (this.selections.features.length > 0) {
                const featuresNames = this.selections.features.map(f => f.name).join(', ');
                this.addSummaryItem($summaryItems, 'Features', 
                    featuresNames, 
                    this.costs.features);
            }

            // Add e-commerce features
            if (this.selections.ecommerceFeatures.length > 0) {
                const ecomNames = this.selections.ecommerceFeatures.map(f => f.name).join(', ');
                this.addSummaryItem($summaryItems, 'E-commerce Features', 
                    ecomNames, 
                    this.costs.ecommerce);
            }

            // Add SEO/Marketing
            if (this.selections.seoMarketing.length > 0) {
                const seoNames = this.selections.seoMarketing.map(f => f.name).join(', ');
                this.addSummaryItem($summaryItems, 'SEO & Marketing', 
                    seoNames, 
                    this.costs.seo);
            }

            // Update totals
            $('#wcc-subtotal').text(this.formatPrice(this.subtotal || 0));

            // Timeline cost
            if (this.costs.timeline > 0) {
                $('#wcc-timeline-cost-row').show();
                $('#wcc-timeline-cost').text('+' + this.formatPrice(this.costs.timeline));
            } else {
                $('#wcc-timeline-cost-row').hide();
            }

            // Grand total
            $('#wcc-grand-total').text(this.formatPrice(this.grandTotal || 0));

            // Monthly maintenance
            if (this.costs.maintenance > 0) {
                $('#wcc-monthly-row').show();
                $('#wcc-monthly-cost').text(this.formatPrice(this.costs.maintenance) + '/mo');
            } else {
                $('#wcc-monthly-row').hide();
            }
        },

        /**
         * Add a summary item
         */
        addSummaryItem: function($container, category, description, price) {
            const $item = $(`
                <div class="wcc-summary-item">
                    <div class="wcc-summary-item-info">
                        <span class="wcc-summary-category">${category}</span>
                        <span class="wcc-summary-description">${description}</span>
                    </div>
                    <span class="wcc-summary-price">${this.formatPrice(price)}</span>
                </div>
            `);
            $container.append($item);
        },

        /**
         * Get selections as text for email
         */
        getSelectionsText: function() {
            let text = '';

            if (this.selections.websiteType) {
                text += 'Website Type: ' + this.selections.websiteType.name + '\n';
            }

            if (this.selections.pageRange) {
                text += 'Pages: ' + this.selections.pageRange.name + '\n';
            }

            if (this.selections.designOption) {
                text += 'Design: ' + this.selections.designOption.name + '\n';
            }

            if (this.selections.features.length > 0) {
                text += 'Features: ' + this.selections.features.map(f => f.name).join(', ') + '\n';
            }

            if (this.selections.ecommerceFeatures.length > 0) {
                text += 'E-commerce: ' + this.selections.ecommerceFeatures.map(f => f.name).join(', ') + '\n';
            }

            if (this.selections.seoMarketing.length > 0) {
                text += 'SEO/Marketing: ' + this.selections.seoMarketing.map(f => f.name).join(', ') + '\n';
            }

            if (this.selections.timeline) {
                text += 'Timeline: ' + this.selections.timeline.name + '\n';
            }

            if (this.selections.maintenance) {
                text += 'Maintenance: ' + this.selections.maintenance.name + '\n';
            }

            return text;
        },

        /**
         * Handle form submission
         */
        handleFormSubmit: function($form) {
            const self = this;
            const $submitBtn = $('#wcc-submit-btn');
            const $btnText = $submitBtn.find('.wcc-btn-text');
            const $btnLoading = $submitBtn.find('.wcc-btn-loading');

            // Show loading state
            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();

            // Gather form data
            const formData = {
                action: 'wcc_submit_quote',
                nonce: wccFrontend.nonce,
                name: $('#wcc-name').val(),
                email: $('#wcc-email').val(),
                phone: $('#wcc-phone').val(),
                company: $('#wcc-company').val(),
                notes: $('#wcc-notes').val(),
                selections: this.getSelectionsText(),
                total_cost: this.grandTotal || 0
            };

            // Submit via AJAX
            $.ajax({
                url: wccFrontend.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Hide form, show success
                        $form.hide();
                        $('#wcc-success-message').text(response.data.message);
                        $('#wcc-form-success').fadeIn(300);
                    } else {
                        self.showFormError(response.data.message || 'An error occurred. Please try again.');
                        // Reset button
                        $submitBtn.prop('disabled', false);
                        $btnText.show();
                        $btnLoading.hide();
                    }
                },
                error: function() {
                    self.showFormError('An error occurred. Please try again.');
                    // Reset button
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            });
        },

        /**
         * Show form error
         */
        showFormError: function(message) {
            // Remove existing error
            $('.wcc-form-error').remove();

            // Add error message
            const $error = $('<div class="wcc-form-error">' + message + '</div>');
            $('#wcc-contact-form').prepend($error);

            // Remove after 5 seconds
            setTimeout(function() {
                $error.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        if ($('#wcc-calculator').length) {
            WCCCalculator.init();
        }
    });

})(jQuery);
