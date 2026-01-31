/**
 * Website Cost Calculator - Admin JavaScript
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
        },

        /**
         * Initialize tab navigation
         */
        initTabs: function() {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                
                const tabId = $(this).data('tab');
                
                // Update tab buttons
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Update tab content
                $('.wcc-tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
                
                // Update URL hash
                window.location.hash = tabId;
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
            $('.wcc-add-item').on('click', function() {
                const targetId = $(this).data('target');
                const fieldName = $(this).data('name');
                const isMultiplier = $(this).data('field') === 'multiplier';
                
                self.addRepeaterItem(targetId, fieldName, isMultiplier);
            });

            // Remove item button (delegated)
            $(document).on('click', '.wcc-remove-item', function() {
                const $item = $(this).closest('.wcc-repeater-item');
                
                // Animate removal
                $item.slideUp(200, function() {
                    $(this).remove();
                });
            });
        },

        /**
         * Add a new repeater item
         */
        addRepeaterItem: function(targetId, fieldName, isMultiplier) {
            const $container = $('#' + targetId);
            const index = $container.find('.wcc-repeater-item').length;
            
            let secondField = isMultiplier 
                ? `<input type="number" name="${fieldName}[${index}][multiplier]" value="1" placeholder="Multiplier" class="small-text" step="0.01" min="1">`
                : `<input type="number" name="${fieldName}[${index}][price]" value="" placeholder="Price" class="small-text" step="0.01">`;

            const $newItem = $(`
                <div class="wcc-repeater-item" style="display: none;">
                    <input type="text" name="${fieldName}[${index}][name]" value="" placeholder="Name" class="regular-text">
                    ${secondField}
                    <button type="button" class="button wcc-remove-item">Remove</button>
                </div>
            `);

            $container.append($newItem);
            $newItem.slideDown(200);
            
            // Focus on the new name input
            $newItem.find('input[type="text"]').focus();
        },

        /**
         * Initialize quote details toggle
         */
        initQuoteDetails: function() {
            $('.wcc-view-details').on('click', function() {
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
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        WCCAdmin.init();
    });

})(jQuery);
