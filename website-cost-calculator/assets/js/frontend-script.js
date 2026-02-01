/**
 * Website Cost Calculator Pro - Frontend JavaScript
 * Advanced features: PDF generation, save/load quotes, charts, animations
 */

(function($) {
    'use strict';

    const WCCCalculator = {
        currentStep: 1,
        totalSteps: 6,
        breakdownChart: null,
        selections: {
            industry: null,
            websiteType: null,
            pageRange: null,
            designOption: null,
            contentOptions: [],
            features: [],
            ecommerceFeatures: [],
            seoMarketing: [],
            hosting: null,
            timeline: null,
            maintenance: null
        },
        costs: {
            base: 0,
            pages: 0,
            design: 0,
            content: 0,
            features: 0,
            ecommerce: 0,
            seo: 0,
            timeline: 0,
        },
        monthly: {
            hosting: 0,
            maintenance: 0
        },
        currency: wccFrontend.currencySymbol || '$',
        currencyPosition: wccFrontend.currencyPosition || 'before',
        rangeVariance: wccFrontend.rangeVariance || 20,

        /**
         * Initialize the calculator
         */
        init: function() {
            this.bindEvents();
            this.updateProgress();
            this.initFeatherIcons();
            this.updateFloatingWidget();
            this.checkPreselect();
        },

        /**
         * Initialize Feather Icons
         */
        initFeatherIcons: function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        },

        /**
         * Check for preselected industry
         */
        checkPreselect: function() {
            const preselect = $('#wcc-calculator').data('preselect');
            if (preselect) {
                $(`input[name="industry"][value="${preselect}"]`).prop('checked', true).trigger('change');
            }
        },

        /**
         * Bind all event handlers
         */
        bindEvents: function() {
            const self = this;

            // Navigation
            $('#wcc-next-btn').on('click', () => this.nextStep());
            $('#wcc-prev-btn').on('click', () => this.prevStep());
            $('.wcc-progress-step').on('click', function() {
                const step = parseInt($(this).data('step'));
                if (step < self.currentStep) {
                    self.goToStep(step);
                }
            });

            // Industry selection
            $('input[name="industry"]').on('change', function() {
                self.handleIndustryChange($(this));
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

            // Content options
            $('input[name="content_options[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'contentOptions', 'content');
            });

            // Feature checkboxes
            $('input[name="features[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'features', 'features');
            });

            // E-commerce features
            $('input[name="ecommerce_features[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'ecommerceFeatures', 'ecommerce');
            });

            // SEO/Marketing
            $('input[name="seo_marketing[]"]').on('change', function() {
                self.handleCheckboxChange($(this), 'seoMarketing', 'seo');
            });

            // Timeline selection
            $('input[name="timeline"]').on('change', function() {
                self.handleTimelineChange($(this));
            });

            // Hosting selection
            $('input[name="hosting"]').on('change', function() {
                self.handleHostingChange($(this));
            });

            // Maintenance selection
            $('input[name="maintenance"]').on('change', function() {
                self.handleMaintenanceChange($(this));
            });

            // Select popular features
            $('#wcc-select-popular').on('click', () => this.selectPopularFeatures());
            $('#wcc-clear-features').on('click', () => this.clearFeatures());

            // Form submission
            $('#wcc-contact-form').on('submit', function(e) {
                e.preventDefault();
                self.handleFormSubmit($(this));
            });

            // Start over
            $('#wcc-start-over').on('click', () => this.startOver());

            // Save/Load quote
            $('#wcc-save-quote-btn').on('click', () => this.saveQuote());
            $('#wcc-load-quote-btn').on('click', () => this.showLoadModal());
            $('#wcc-load-code-btn').on('click', () => this.loadQuote());

            // PDF download
            $('#wcc-download-pdf').on('click', () => this.generatePDF());

            // Share quote
            $('#wcc-share-quote').on('click', () => this.shareQuote());

            // Print quote
            $('#wcc-print-quote').on('click', () => window.print());

            // Modal handling
            $('.wcc-modal-overlay, .wcc-modal-close').on('click', function() {
                $(this).closest('.wcc-modal').removeClass('active');
            });

            // Copy buttons
            $('#wcc-copy-code').on('click', () => this.copyToClipboard('#wcc-saved-code'));
            $('#wcc-copy-link').on('click', () => this.copyToClipboard('#wcc-share-url'));

            // Share buttons
            $('#wcc-share-email').on('click', () => this.shareViaEmail());
            $('#wcc-share-twitter').on('click', () => this.shareViaTwitter());
            $('#wcc-share-linkedin').on('click', () => this.shareViaLinkedIn());

            // Floating widget toggle
            $('#wcc-widget-toggle').on('click', function() {
                $('#wcc-floating-widget').toggleClass('collapsed');
            });

            // Initialize default timeline
            $('input[name="timeline"]:checked').trigger('change');
        },

        /**
         * Handle industry change
         */
        handleIndustryChange: function($input) {
            const id = $input.val();
            const name = $input.data('name');
            const modifier = parseFloat($input.data('modifier')) || 1;

            this.selections.industry = {
                id: id,
                name: name,
                modifier: modifier
            };

            // Visual update
            $input.closest('.wcc-industry-grid').find('.wcc-industry-card').removeClass('selected');
            $input.closest('.wcc-industry-card').addClass('selected');

            this.updateCalculations();
            this.initFeatherIcons();
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

            // Show/hide e-commerce section
            const isEcommerce = name.toLowerCase().includes('commerce') || 
                               name.toLowerCase().includes('store');
            
            if (isEcommerce) {
                $('#wcc-ecommerce-section').slideDown(300);
            } else {
                $('#wcc-ecommerce-section').slideUp(300);
                $('input[name="ecommerce_features[]"]').prop('checked', false);
                this.selections.ecommerceFeatures = [];
                this.costs.ecommerce = 0;
            }

            // Visual update
            $input.closest('.wcc-type-grid').find('.wcc-type-card').removeClass('selected');
            $input.closest('.wcc-type-card').addClass('selected');

            this.updateCalculations();
        },

        /**
         * Handle radio changes
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

            // Visual update
            $input.closest('.wcc-range-grid, .wcc-design-options').find('label').removeClass('selected');
            $input.closest('label').addClass('selected');

            this.updateCalculations();
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

            // Visual update
            $input.closest('.wcc-timeline-options').find('.wcc-timeline-option').removeClass('selected');
            $input.closest('.wcc-timeline-option').addClass('selected');

            this.updateCalculations();
        },

        /**
         * Handle hosting change
         */
        handleHostingChange: function($input) {
            const name = $input.data('name');
            const monthlyPrice = parseFloat($input.data('monthly')) || 0;

            this.selections.hosting = {
                value: $input.val(),
                name: name,
                monthly: monthlyPrice
            };
            this.monthly.hosting = monthlyPrice;

            // Visual update
            $input.closest('.wcc-hosting-options').find('.wcc-hosting-option').removeClass('selected');
            $input.closest('.wcc-hosting-option').addClass('selected');

            this.updateCalculations();
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
            this.monthly.maintenance = price;

            // Visual update
            $input.closest('.wcc-maintenance-options').find('.wcc-maintenance-option').removeClass('selected');
            $input.closest('.wcc-maintenance-option').addClass('selected');

            this.updateCalculations();
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
                $input.closest('label').addClass('selected');
            } else {
                this.selections[selectionKey] = this.selections[selectionKey].filter(
                    item => item.value !== value
                );
                $input.closest('label').removeClass('selected');
            }

            this.costs[costKey] = this.selections[selectionKey].reduce(
                (total, item) => total + item.price, 0
            );

            this.updateCalculations();
        },

        /**
         * Select popular features
         */
        selectPopularFeatures: function() {
            $('input[name="features[]"][data-popular="1"]').each((i, el) => {
                if (!$(el).is(':checked')) {
                    $(el).prop('checked', true).trigger('change');
                }
            });
        },

        /**
         * Clear all features
         */
        clearFeatures: function() {
            $('input[name="features[]"]').each((i, el) => {
                if ($(el).is(':checked')) {
                    $(el).prop('checked', false).trigger('change');
                }
            });
        },

        /**
         * Calculate totals
         */
        updateCalculations: function() {
            // Calculate subtotal
            let subtotal = this.costs.base + 
                          this.costs.pages + 
                          this.costs.design + 
                          this.costs.content +
                          this.costs.features + 
                          this.costs.ecommerce + 
                          this.costs.seo;

            // Apply industry modifier
            if (this.selections.industry) {
                subtotal *= this.selections.industry.modifier;
            }

            // Apply timeline multiplier
            const multiplier = this.selections.timeline ? this.selections.timeline.multiplier : 1;
            const timelineCost = subtotal * (multiplier - 1);
            
            // Calculate grand total
            const grandTotal = subtotal + timelineCost;

            // Calculate monthly
            const monthlyTotal = this.monthly.hosting + this.monthly.maintenance;

            // Store values
            this.subtotal = subtotal;
            this.timelineCost = timelineCost;
            this.grandTotal = grandTotal;
            this.monthlyTotal = monthlyTotal;

            // Calculate price range
            this.priceLow = Math.round(grandTotal * (1 - this.rangeVariance / 100));
            this.priceHigh = Math.round(grandTotal * (1 + this.rangeVariance / 100));

            this.updateFloatingWidget();
            this.initFeatherIcons();
        },

        /**
         * Update floating price widget
         */
        updateFloatingWidget: function() {
            const total = this.grandTotal || 0;
            const monthly = this.monthlyTotal || 0;
            
            $('#wcc-floating-price').text(this.formatPrice(total));
            $('#wcc-floating-monthly').text('+ ' + this.formatPrice(monthly) + '/mo');

            // Animate the price change
            $('#wcc-floating-price').addClass('price-updated');
            setTimeout(() => $('#wcc-floating-price').removeClass('price-updated'), 300);
        },

        /**
         * Format price with currency
         */
        formatPrice: function(amount) {
            const formatted = Math.round(amount).toLocaleString('en-US');
            return this.currencyPosition === 'after' 
                ? formatted + this.currency 
                : this.currency + formatted;
        },

        /**
         * Navigate to next step
         */
        nextStep: function() {
            if (!this.validateCurrentStep()) return;

            if (this.currentStep < this.totalSteps) {
                this.goToStep(this.currentStep + 1);
            }
        },

        /**
         * Navigate to previous step
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
            $('#wcc-step-' + this.currentStep).removeClass('active');
            this.currentStep = step;
            $('#wcc-step-' + this.currentStep).addClass('active');

            this.updateProgress();
            this.updateNavigation();

            if (this.currentStep === this.totalSteps) {
                this.updateSummary();
                this.initBreakdownChart();
            }

            // Scroll to top
            $('html, body').animate({
                scrollTop: $('#wcc-calculator').offset().top - 30
            }, 300);

            this.initFeatherIcons();
        },

        /**
         * Validate current step
         */
        validateCurrentStep: function() {
            let isValid = true;
            let errorMessage = '';

            switch (this.currentStep) {
                case 1:
                    if (!this.selections.industry) {
                        isValid = false;
                        errorMessage = 'Please select your industry.';
                    }
                    break;
                case 2:
                    if (!this.selections.websiteType) {
                        isValid = false;
                        errorMessage = 'Please select a website type.';
                    }
                    break;
            }

            if (!isValid) {
                this.showNotification(errorMessage, 'error');
            }

            return isValid;
        },

        /**
         * Show notification
         */
        showNotification: function(message, type = 'info') {
            $('.wcc-notification').remove();
            
            const $notification = $(`
                <div class="wcc-notification wcc-notification-${type}">
                    <i data-feather="${type === 'error' ? 'alert-circle' : 'check-circle'}"></i>
                    <span>${message}</span>
                </div>
            `);
            
            $('#wcc-step-' + this.currentStep + ' .wcc-step-header').after($notification);
            this.initFeatherIcons();

            setTimeout(() => $notification.fadeOut(300, function() { $(this).remove(); }), 4000);
        },

        /**
         * Update progress bar
         */
        updateProgress: function() {
            const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            $('#wcc-progress-fill').css('width', progress + '%');

            $('.wcc-progress-step').each((i, el) => {
                const step = parseInt($(el).data('step'));
                $(el).removeClass('active completed');

                if (step < this.currentStep) {
                    $(el).addClass('completed');
                } else if (step === this.currentStep) {
                    $(el).addClass('active');
                }
            });

            this.initFeatherIcons();
        },

        /**
         * Update navigation buttons
         */
        updateNavigation: function() {
            $('#wcc-prev-btn').toggle(this.currentStep > 1);
            $('#wcc-next-btn').toggle(this.currentStep < this.totalSteps);
        },

        /**
         * Update summary page
         */
        updateSummary: function() {
            const $container = $('#wcc-summary-items');
            $container.empty();

            // Add each selection category
            if (this.selections.industry) {
                this.addSummaryGroup($container, 'Industry', [
                    { name: this.selections.industry.name, price: 0 }
                ]);
            }

            if (this.selections.websiteType) {
                this.addSummaryGroup($container, 'Website Type', [
                    { name: this.selections.websiteType.name, price: this.selections.websiteType.price }
                ]);
            }

            if (this.selections.pageRange) {
                this.addSummaryGroup($container, 'Number of Pages', [
                    { name: this.selections.pageRange.name, price: this.selections.pageRange.price }
                ]);
            }

            if (this.selections.designOption) {
                this.addSummaryGroup($container, 'Design', [
                    { name: this.selections.designOption.name, price: this.selections.designOption.price }
                ]);
            }

            if (this.selections.contentOptions.length > 0) {
                this.addSummaryGroup($container, 'Content & Media', this.selections.contentOptions);
            }

            if (this.selections.features.length > 0) {
                this.addSummaryGroup($container, 'Features', this.selections.features);
            }

            if (this.selections.ecommerceFeatures.length > 0) {
                this.addSummaryGroup($container, 'E-commerce', this.selections.ecommerceFeatures);
            }

            if (this.selections.seoMarketing.length > 0) {
                this.addSummaryGroup($container, 'SEO & Marketing', this.selections.seoMarketing);
            }

            if (this.selections.timeline) {
                this.addSummaryGroup($container, 'Timeline', [
                    { name: this.selections.timeline.name, price: 0, note: this.selections.timeline.multiplier !== 1 ? 
                        ((this.selections.timeline.multiplier - 1) * 100).toFixed(0) + '% adjustment' : 'Standard' }
                ]);
            }

            // Update price comparison
            $('#wcc-price-low').text(this.formatPrice(this.priceLow));
            $('#wcc-price-mid').text(this.formatPrice(this.grandTotal));
            $('#wcc-price-high').text(this.formatPrice(this.priceHigh));

            // Update totals
            $('#wcc-subtotal').text(this.formatPrice(this.subtotal));
            
            if (this.timelineCost !== 0) {
                $('#wcc-timeline-row').show();
                const sign = this.timelineCost >= 0 ? '+' : '';
                $('#wcc-timeline-cost').text(sign + this.formatPrice(this.timelineCost));
            } else {
                $('#wcc-timeline-row').hide();
            }

            $('#wcc-grand-total').text(this.formatPrice(this.grandTotal));
            $('#wcc-monthly-total').text(this.formatPrice(this.monthlyTotal) + '/mo');

            this.initFeatherIcons();
        },

        /**
         * Add summary group
         */
        addSummaryGroup: function($container, title, items) {
            const $group = $(`
                <div class="wcc-summary-group">
                    <h4 class="wcc-summary-group-title">${title}</h4>
                    <div class="wcc-summary-group-items"></div>
                </div>
            `);

            items.forEach(item => {
                const priceDisplay = item.price > 0 ? this.formatPrice(item.price) : (item.note || 'Included');
                $group.find('.wcc-summary-group-items').append(`
                    <div class="wcc-summary-item">
                        <span class="wcc-item-name">${item.name}</span>
                        <span class="wcc-item-price">${priceDisplay}</span>
                    </div>
                `);
            });

            $container.append($group);
        },

        /**
         * Initialize breakdown chart
         */
        initBreakdownChart: function() {
            const ctx = document.getElementById('wcc-breakdown-chart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.breakdownChart) {
                this.breakdownChart.destroy();
            }

            const data = {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#6366F1', '#10B981', '#F59E0B', '#EF4444', 
                        '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
                    ],
                    borderWidth: 0,
                    spacing: 2
                }]
            };

            // Add data points
            if (this.costs.base > 0) {
                data.labels.push('Base Website');
                data.datasets[0].data.push(this.costs.base);
            }
            if (this.costs.pages > 0) {
                data.labels.push('Pages');
                data.datasets[0].data.push(this.costs.pages);
            }
            if (this.costs.design > 0) {
                data.labels.push('Design');
                data.datasets[0].data.push(this.costs.design);
            }
            if (this.costs.content > 0) {
                data.labels.push('Content');
                data.datasets[0].data.push(this.costs.content);
            }
            if (this.costs.features > 0) {
                data.labels.push('Features');
                data.datasets[0].data.push(this.costs.features);
            }
            if (this.costs.ecommerce > 0) {
                data.labels.push('E-commerce');
                data.datasets[0].data.push(this.costs.ecommerce);
            }
            if (this.costs.seo > 0) {
                data.labels.push('SEO & Marketing');
                data.datasets[0].data.push(this.costs.seo);
            }

            if (data.labels.length === 0) {
                data.labels.push('No selections');
                data.datasets[0].data.push(1);
            }

            this.breakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${this.formatPrice(value)} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        },

        /**
         * Handle form submission
         */
        handleFormSubmit: function($form) {
            const self = this;
            const $submitBtn = $('#wcc-submit-btn');
            const $btnText = $submitBtn.find('.wcc-btn-text');
            const $btnLoading = $submitBtn.find('.wcc-btn-loading');

            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();

            const formData = {
                action: 'wcc_submit_quote',
                nonce: wccFrontend.nonce,
                name: $('#wcc-name').val(),
                email: $('#wcc-email').val(),
                phone: $('#wcc-phone').val(),
                company: $('#wcc-company').val(),
                industry: this.selections.industry?.name || '',
                notes: $('#wcc-notes').val(),
                selections: this.getSelectionsText(),
                breakdown: JSON.stringify(this.costs),
                subtotal: this.subtotal || 0,
                total_cost: this.grandTotal || 0,
                monthly_cost: this.monthlyTotal || 0
            };

            $.ajax({
                url: wccFrontend.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $form.hide();
                        $('#wcc-success-message').text(response.data.message);
                        $('#wcc-form-success').fadeIn(300);
                        self.initFeatherIcons();
                    } else {
                        self.showNotification(response.data.message || 'An error occurred.', 'error');
                        $submitBtn.prop('disabled', false);
                        $btnText.show();
                        $btnLoading.hide();
                    }
                },
                error: function() {
                    self.showNotification('An error occurred. Please try again.', 'error');
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            });
        },

        /**
         * Get selections as text
         */
        getSelectionsText: function() {
            let text = '';

            if (this.selections.industry) text += `Industry: ${this.selections.industry.name}\n`;
            if (this.selections.websiteType) text += `Website Type: ${this.selections.websiteType.name}\n`;
            if (this.selections.pageRange) text += `Pages: ${this.selections.pageRange.name}\n`;
            if (this.selections.designOption) text += `Design: ${this.selections.designOption.name}\n`;
            
            if (this.selections.contentOptions.length > 0) {
                text += `Content: ${this.selections.contentOptions.map(f => f.name).join(', ')}\n`;
            }
            if (this.selections.features.length > 0) {
                text += `Features: ${this.selections.features.map(f => f.name).join(', ')}\n`;
            }
            if (this.selections.ecommerceFeatures.length > 0) {
                text += `E-commerce: ${this.selections.ecommerceFeatures.map(f => f.name).join(', ')}\n`;
            }
            if (this.selections.seoMarketing.length > 0) {
                text += `SEO/Marketing: ${this.selections.seoMarketing.map(f => f.name).join(', ')}\n`;
            }
            if (this.selections.hosting) text += `Hosting: ${this.selections.hosting.name}\n`;
            if (this.selections.timeline) text += `Timeline: ${this.selections.timeline.name}\n`;
            if (this.selections.maintenance) text += `Maintenance: ${this.selections.maintenance.name}\n`;

            return text;
        },

        /**
         * Save quote
         */
        saveQuote: function() {
            const self = this;
            const quoteData = JSON.stringify({
                selections: this.selections,
                costs: this.costs,
                monthly: this.monthly,
                totals: {
                    subtotal: this.subtotal,
                    grandTotal: this.grandTotal,
                    monthlyTotal: this.monthlyTotal
                }
            });

            $.ajax({
                url: wccFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wcc_save_quote',
                    nonce: wccFrontend.nonce,
                    quote_data: quoteData
                },
                success: function(response) {
                    if (response.success) {
                        $('#wcc-saved-code').text(response.data.quote_code);
                        $('#wcc-save-modal').addClass('active');
                        self.initFeatherIcons();
                    } else {
                        self.showNotification('Failed to save quote.', 'error');
                    }
                }
            });
        },

        /**
         * Show load modal
         */
        showLoadModal: function() {
            $('#wcc-load-modal').addClass('active');
            $('#wcc-load-code-input').val('').focus();
            this.initFeatherIcons();
        },

        /**
         * Load quote
         */
        loadQuote: function() {
            const self = this;
            const code = $('#wcc-load-code-input').val().toUpperCase();

            if (code.length !== 8) {
                self.showNotification('Please enter a valid 8-character code.', 'error');
                return;
            }

            $.ajax({
                url: wccFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wcc_load_quote',
                    nonce: wccFrontend.nonce,
                    quote_code: code
                },
                success: function(response) {
                    if (response.success) {
                        const data = JSON.parse(response.data.quote_data);
                        self.restoreQuote(data);
                        $('#wcc-load-modal').removeClass('active');
                        self.showNotification('Quote loaded successfully!', 'success');
                    } else {
                        self.showNotification(response.data.message, 'error');
                    }
                }
            });
        },

        /**
         * Restore quote data
         */
        restoreQuote: function(data) {
            // Restore selections by triggering change events
            if (data.selections.industry) {
                $(`input[name="industry"][value="${data.selections.industry.id}"]`)
                    .prop('checked', true).trigger('change');
            }
            if (data.selections.websiteType) {
                $(`input[name="website_type"][value="${data.selections.websiteType.value}"]`)
                    .prop('checked', true).trigger('change');
            }
            // Add more restoration as needed

            this.goToStep(this.totalSteps);
        },

        /**
         * Generate PDF
         */
        generatePDF: function() {
            if (typeof jspdf === 'undefined') {
                this.showNotification('PDF generation is loading...', 'info');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const self = this;

            // Title
            doc.setFontSize(24);
            doc.setTextColor(99, 102, 241);
            doc.text('Website Project Quote', 20, 25);

            // Date
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text(`Generated: ${new Date().toLocaleDateString()}`, 20, 35);

            // Selections
            doc.setFontSize(14);
            doc.setTextColor(0);
            doc.text('Project Details', 20, 50);

            let y = 60;
            doc.setFontSize(11);

            const selections = this.getSelectionsText().split('\n');
            selections.forEach(line => {
                if (line.trim()) {
                    doc.text(line, 25, y);
                    y += 7;
                }
            });

            // Totals
            y += 10;
            doc.setFontSize(14);
            doc.text('Investment Summary', 20, y);
            y += 12;

            doc.setFontSize(12);
            doc.text(`Subtotal: ${this.formatPrice(this.subtotal)}`, 25, y);
            y += 8;
            
            if (this.timelineCost !== 0) {
                doc.text(`Timeline Adjustment: ${this.formatPrice(this.timelineCost)}`, 25, y);
                y += 8;
            }

            doc.setFontSize(14);
            doc.setTextColor(99, 102, 241);
            doc.text(`Total Investment: ${this.formatPrice(this.grandTotal)}`, 25, y);
            y += 8;

            doc.setFontSize(11);
            doc.setTextColor(100);
            doc.text(`Monthly Recurring: ${this.formatPrice(this.monthlyTotal)}/mo`, 25, y);

            // Footer
            doc.setFontSize(9);
            doc.setTextColor(150);
            doc.text('This is an estimate. Final pricing may vary based on specific requirements.', 20, 280);

            doc.save('website-quote.pdf');
            this.showNotification('PDF downloaded successfully!', 'success');
        },

        /**
         * Share quote
         */
        shareQuote: function() {
            const self = this;
            
            // First save the quote to get a code
            const quoteData = JSON.stringify({
                selections: this.selections,
                costs: this.costs,
                totals: { subtotal: this.subtotal, grandTotal: this.grandTotal }
            });

            $.ajax({
                url: wccFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wcc_save_quote',
                    nonce: wccFrontend.nonce,
                    quote_data: quoteData
                },
                success: function(response) {
                    if (response.success) {
                        const shareUrl = window.location.href.split('?')[0] + '?quote=' + response.data.quote_code;
                        $('#wcc-share-url').val(shareUrl);
                        $('#wcc-share-modal').addClass('active');
                        self.initFeatherIcons();
                    }
                }
            });
        },

        /**
         * Copy to clipboard
         */
        copyToClipboard: function(selector) {
            const text = $(selector).is('input') ? $(selector).val() : $(selector).text();
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification('Copied to clipboard!', 'success');
            });
        },

        /**
         * Share via email
         */
        shareViaEmail: function() {
            const subject = encodeURIComponent('My Website Project Quote');
            const body = encodeURIComponent(`Check out my website project quote: ${$('#wcc-share-url').val()}`);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        },

        /**
         * Share via Twitter
         */
        shareViaTwitter: function() {
            const text = encodeURIComponent('I just got a quote for my new website!');
            const url = encodeURIComponent($('#wcc-share-url').val());
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
        },

        /**
         * Share via LinkedIn
         */
        shareViaLinkedIn: function() {
            const url = encodeURIComponent($('#wcc-share-url').val());
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
        },

        /**
         * Start over
         */
        startOver: function() {
            location.reload();
        }
    };

    // Initialize
    $(document).ready(function() {
        if ($('#wcc-calculator').length) {
            WCCCalculator.init();
        }
    });

})(jQuery);
