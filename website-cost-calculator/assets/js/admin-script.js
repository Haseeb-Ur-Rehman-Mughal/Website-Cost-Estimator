/**
 * Website Cost Calculator Pro - Admin JavaScript
 * Author: Haseeb Ur Rehman Mughal
 * Website: https://ezyontech.com
 */

(function($) {
    'use strict';

    const WCCAdmin = {
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.initTabs();
            this.initRepeaters();
            this.initQuoteDetails();
            this.initFormValidation();
        },

        /**
         * Initialize tab navigation
         */
        initTabs: function() {
            const self = this;
            
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                
                const tabId = $(this).data('tab');
                
                // Update tab buttons
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Update tab content
                $('.wcc-tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
                
                // Update URL hash without scrolling
                if (history.pushState) {
                    history.pushState(null, null, '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            // Check for hash on load
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const $tab = $('.nav-tab[data-tab="' + hash + '"]');
                if ($tab.length) {
                    $tab.trigger('click');
                }
            }
        },

        /**
         * Initialize repeater fields
         */
        initRepeaters: function() {
            const self = this;

            // Add item button
            $(document).on('click', '.wcc-add-item', function() {
                const targetId = $(this).data('target');
                const fieldName = $(this).data('name');
                const fieldType = $(this).data('field') || 'price';
                
                self.addRepeaterItem(targetId, fieldName, fieldType);
            });

            // Remove item button (delegated)
            $(document).on('click', '.wcc-remove-item', function() {
                const $item = $(this).closest('.wcc-repeater-item');
                const $container = $item.closest('.wcc-repeater');
                
                // Don't remove if it's the last item
                if ($container.find('.wcc-repeater-item').length > 1) {
                    $item.slideUp(200, function() {
                        $(this).remove();
                        // Re-index the remaining items
                        self.reindexRepeater($container);
                    });
                } else {
                    alert('You must have at least one item.');
                }
            });
        },

        /**
         * Add a new repeater item
         */
        addRepeaterItem: function(targetId, fieldName, fieldType) {
            const $container = $('#' + targetId);
            const index = Date.now(); // Use timestamp for unique index
            
            let secondFieldName = 'price';
            let secondFieldPlaceholder = 'Price';
            let secondFieldMin = '0';
            
            if (fieldType === 'multiplier') {
                secondFieldName = 'multiplier';
                secondFieldPlaceholder = 'Multiplier';
                secondFieldMin = '0';
            } else if (fieldType === 'monthly') {
                secondFieldName = 'monthly';
                secondFieldPlaceholder = 'Monthly Price';
            }

            const $newItem = $(`
                <div class="wcc-repeater-item" style="display: none;">
                    <input type="text" name="${fieldName}[${index}][name]" value="" placeholder="Name" class="regular-text">
                    <input type="number" name="${fieldName}[${index}][${secondFieldName}]" value="" placeholder="${secondFieldPlaceholder}" class="small-text" step="0.01" min="${secondFieldMin}">
                    <button type="button" class="button wcc-remove-item">Remove</button>
                </div>
            `);

            $container.append($newItem);
            $newItem.slideDown(200);
            
            // Focus on the new name input
            $newItem.find('input[type="text"]').focus();
        },

        /**
         * Re-index repeater items after removal
         */
        reindexRepeater: function($container) {
            $container.find('.wcc-repeater-item').each(function(index) {
                $(this).find('input, select, textarea').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        },

        /**
         * Initialize quote details toggle
         */
        initQuoteDetails: function() {
            $(document).on('click', '.wcc-view-details', function() {
                const quoteId = $(this).data('quote-id');
                const $details = $('#quote-details-' + quoteId);
                
                // Toggle visibility
                if ($details.is(':visible')) {
                    $details.slideUp(200);
                    $(this).text('View');
                } else {
                    // Close other open details
                    $('.wcc-quote-details:visible').slideUp(200);
                    $('.wcc-view-details').text('View');
                    
                    // Open this one
                    $details.slideDown(200);
                    $(this).text('Hide');
                }
            });
        },

        /**
         * Initialize form validation
         */
        initFormValidation: function() {
            $('#wcc-settings-form').on('submit', function(e) {
                // Basic validation could be added here
                // For now, just let the form submit
                return true;
            });
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        WCCAdmin.init();
    });

})(jQuery);
