<?php
/**
 * Frontend Calculator Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$wcc = Website_Cost_Calculator::get_instance();
$options = $wcc->get_options();
$currency = $options['currency_symbol'] ?? '$';
$theme_class = isset($atts['theme']) ? 'wcc-theme-' . esc_attr($atts['theme']) : 'wcc-theme-default';
$compact_class = isset($atts['compact']) && $atts['compact'] === 'true' ? 'wcc-compact' : '';
?>

<div class="wcc-calculator <?php echo $theme_class . ' ' . $compact_class; ?>" id="wcc-calculator">
    <!-- Progress Bar -->
    <div class="wcc-progress">
        <div class="wcc-progress-bar">
            <div class="wcc-progress-fill" id="wcc-progress-fill"></div>
        </div>
        <div class="wcc-progress-steps">
            <div class="wcc-progress-step active" data-step="1">
                <span class="wcc-step-number">1</span>
                <span class="wcc-step-label"><?php _e('Website Type', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="2">
                <span class="wcc-step-number">2</span>
                <span class="wcc-step-label"><?php _e('Pages & Design', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="3">
                <span class="wcc-step-number">3</span>
                <span class="wcc-step-label"><?php _e('Features', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="4">
                <span class="wcc-step-number">4</span>
                <span class="wcc-step-label"><?php _e('Extras', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="5">
                <span class="wcc-step-number">5</span>
                <span class="wcc-step-label"><?php _e('Summary', 'website-cost-calculator'); ?></span>
            </div>
        </div>
    </div>

    <!-- Calculator Steps -->
    <div class="wcc-steps-container">
        
        <!-- Step 1: Website Type -->
        <div class="wcc-step active" id="wcc-step-1" data-step="1">
            <div class="wcc-step-header">
                <h2><?php _e('What type of website do you need?', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Select the option that best describes your project.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-options wcc-options-cards">
                <?php foreach ($options['website_types'] as $index => $type): ?>
                <label class="wcc-card wcc-radio-card">
                    <input type="radio" name="website_type" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($type['name']); ?>" data-price="<?php echo esc_attr($type['price']); ?>">
                    <div class="wcc-card-content">
                        <div class="wcc-card-icon">
                            <?php echo $wcc->get_website_type_icon($type['name']); ?>
                        </div>
                        <h3 class="wcc-card-title"><?php echo esc_html($type['name']); ?></h3>
                        <span class="wcc-card-price"><?php echo esc_html($currency . number_format($type['price'], 0)); ?></span>
                    </div>
                    <div class="wcc-card-check">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step 2: Pages & Design -->
        <div class="wcc-step" id="wcc-step-2" data-step="2">
            <div class="wcc-step-header">
                <h2><?php _e('Pages & Design', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Choose the size and design approach for your website.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title"><?php _e('How many pages do you need?', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-buttons">
                    <?php foreach ($options['page_ranges'] as $index => $range): ?>
                    <label class="wcc-button-option">
                        <input type="radio" name="page_range" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($range['name']); ?>" data-price="<?php echo esc_attr($range['price']); ?>">
                        <span class="wcc-button-content">
                            <span class="wcc-button-label"><?php echo esc_html($range['name']); ?></span>
                            <?php if ($range['price'] > 0): ?>
                            <span class="wcc-button-price">+<?php echo esc_html($currency . number_format($range['price'], 0)); ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title"><?php _e('Design Approach', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-buttons">
                    <?php foreach ($options['design_options'] as $index => $opt): ?>
                    <label class="wcc-button-option wcc-button-wide">
                        <input type="radio" name="design_option" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($opt['name']); ?>" data-price="<?php echo esc_attr($opt['price']); ?>">
                        <span class="wcc-button-content">
                            <span class="wcc-button-label"><?php echo esc_html($opt['name']); ?></span>
                            <?php if ($opt['price'] > 0): ?>
                            <span class="wcc-button-price">+<?php echo esc_html($currency . number_format($opt['price'], 0)); ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 3: Features -->
        <div class="wcc-step" id="wcc-step-3" data-step="3">
            <div class="wcc-step-header">
                <h2><?php _e('Select Features', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Choose the features you need for your website.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title"><?php _e('Website Features', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-checklist">
                    <?php foreach ($options['features'] as $index => $feature): ?>
                    <label class="wcc-checkbox-option">
                        <input type="checkbox" name="features[]" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($feature['name']); ?>" data-price="<?php echo esc_attr($feature['price']); ?>">
                        <span class="wcc-checkbox-box">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                        </span>
                        <span class="wcc-checkbox-content">
                            <span class="wcc-checkbox-label"><?php echo esc_html($feature['name']); ?></span>
                            <span class="wcc-checkbox-price">+<?php echo esc_html($currency . number_format($feature['price'], 0)); ?></span>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="wcc-section wcc-ecommerce-section" id="wcc-ecommerce-section" style="display: none;">
                <h3 class="wcc-section-title"><?php _e('E-commerce Features', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-checklist">
                    <?php foreach ($options['ecommerce_features'] as $index => $feature): ?>
                    <label class="wcc-checkbox-option">
                        <input type="checkbox" name="ecommerce_features[]" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($feature['name']); ?>" data-price="<?php echo esc_attr($feature['price']); ?>">
                        <span class="wcc-checkbox-box">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                        </span>
                        <span class="wcc-checkbox-content">
                            <span class="wcc-checkbox-label"><?php echo esc_html($feature['name']); ?></span>
                            <span class="wcc-checkbox-price">+<?php echo esc_html($currency . number_format($feature['price'], 0)); ?></span>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 4: Extras -->
        <div class="wcc-step" id="wcc-step-4" data-step="4">
            <div class="wcc-step-header">
                <h2><?php _e('Additional Options', 'website-cost-calculator'); ?></h2>
                <p><?php _e('SEO, marketing, timeline, and ongoing support.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title"><?php _e('SEO & Marketing', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-checklist">
                    <?php foreach ($options['seo_marketing'] as $index => $item): ?>
                    <label class="wcc-checkbox-option">
                        <input type="checkbox" name="seo_marketing[]" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($item['name']); ?>" data-price="<?php echo esc_attr($item['price']); ?>">
                        <span class="wcc-checkbox-box">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                        </span>
                        <span class="wcc-checkbox-content">
                            <span class="wcc-checkbox-label"><?php echo esc_html($item['name']); ?></span>
                            <span class="wcc-checkbox-price">+<?php echo esc_html($currency . number_format($item['price'], 0)); ?></span>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title"><?php _e('Project Timeline', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-buttons">
                    <?php foreach ($options['timeline_options'] as $index => $timeline): ?>
                    <label class="wcc-button-option wcc-button-wide">
                        <input type="radio" name="timeline" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($timeline['name']); ?>" data-multiplier="<?php echo esc_attr($timeline['multiplier']); ?>" <?php echo $timeline['multiplier'] == 1 ? 'checked' : ''; ?>>
                        <span class="wcc-button-content">
                            <span class="wcc-button-label"><?php echo esc_html($timeline['name']); ?></span>
                            <?php if ($timeline['multiplier'] > 1): ?>
                            <span class="wcc-button-price wcc-button-multiplier">+<?php echo (($timeline['multiplier'] - 1) * 100); ?>%</span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title"><?php _e('Maintenance & Support', 'website-cost-calculator'); ?></h3>
                <div class="wcc-options wcc-options-buttons">
                    <?php foreach ($options['maintenance_plans'] as $index => $plan): ?>
                    <label class="wcc-button-option wcc-button-wide">
                        <input type="radio" name="maintenance" value="<?php echo $index; ?>" data-name="<?php echo esc_attr($plan['name']); ?>" data-price="<?php echo esc_attr($plan['price']); ?>" <?php echo $plan['price'] == 0 ? 'checked' : ''; ?>>
                        <span class="wcc-button-content">
                            <span class="wcc-button-label"><?php echo esc_html($plan['name']); ?></span>
                            <?php if ($plan['price'] > 0): ?>
                            <span class="wcc-button-price"><?php echo esc_html($currency . number_format($plan['price'], 0)); ?>/mo</span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 5: Summary & Contact -->
        <div class="wcc-step" id="wcc-step-5" data-step="5">
            <div class="wcc-step-header">
                <h2><?php _e('Your Quote Summary', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Review your selections and get your personalized quote.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-summary-container">
                <div class="wcc-summary-left">
                    <div class="wcc-summary-card">
                        <h3><?php _e('Your Selections', 'website-cost-calculator'); ?></h3>
                        <div class="wcc-summary-items" id="wcc-summary-items">
                            <!-- Populated by JavaScript -->
                        </div>
                        <div class="wcc-summary-total">
                            <div class="wcc-summary-subtotal">
                                <span><?php _e('Subtotal:', 'website-cost-calculator'); ?></span>
                                <span id="wcc-subtotal"><?php echo $currency; ?>0</span>
                            </div>
                            <div class="wcc-summary-timeline-cost" id="wcc-timeline-cost-row" style="display: none;">
                                <span><?php _e('Rush Fee:', 'website-cost-calculator'); ?></span>
                                <span id="wcc-timeline-cost"><?php echo $currency; ?>0</span>
                            </div>
                            <div class="wcc-summary-grand-total">
                                <span><?php _e('Estimated Total:', 'website-cost-calculator'); ?></span>
                                <span id="wcc-grand-total"><?php echo $currency; ?>0</span>
                            </div>
                            <div class="wcc-summary-monthly" id="wcc-monthly-row" style="display: none;">
                                <span><?php _e('Monthly Maintenance:', 'website-cost-calculator'); ?></span>
                                <span id="wcc-monthly-cost"><?php echo $currency; ?>0/mo</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($options['form_settings']['show_contact_form'] ?? true): ?>
                <div class="wcc-summary-right">
                    <div class="wcc-contact-card">
                        <h3><?php _e('Get Your Detailed Quote', 'website-cost-calculator'); ?></h3>
                        <p><?php _e('Enter your details below and we\'ll send you a comprehensive proposal.', 'website-cost-calculator'); ?></p>
                        
                        <form class="wcc-contact-form" id="wcc-contact-form">
                            <div class="wcc-form-row">
                                <div class="wcc-form-field">
                                    <label for="wcc-name"><?php _e('Full Name', 'website-cost-calculator'); ?> <span class="required">*</span></label>
                                    <input type="text" id="wcc-name" name="name" required>
                                </div>
                            </div>
                            
                            <div class="wcc-form-row">
                                <div class="wcc-form-field">
                                    <label for="wcc-email"><?php _e('Email Address', 'website-cost-calculator'); ?> <span class="required">*</span></label>
                                    <input type="email" id="wcc-email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="wcc-form-row wcc-form-row-half">
                                <div class="wcc-form-field">
                                    <label for="wcc-phone"><?php _e('Phone Number', 'website-cost-calculator'); ?></label>
                                    <input type="tel" id="wcc-phone" name="phone">
                                </div>
                                <div class="wcc-form-field">
                                    <label for="wcc-company"><?php _e('Company Name', 'website-cost-calculator'); ?></label>
                                    <input type="text" id="wcc-company" name="company">
                                </div>
                            </div>
                            
                            <div class="wcc-form-row">
                                <div class="wcc-form-field">
                                    <label for="wcc-notes"><?php _e('Additional Details', 'website-cost-calculator'); ?></label>
                                    <textarea id="wcc-notes" name="notes" rows="3" placeholder="<?php _e('Tell us more about your project...', 'website-cost-calculator'); ?>"></textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="wcc-submit-btn" id="wcc-submit-btn">
                                <span class="wcc-btn-text"><?php _e('Request My Quote', 'website-cost-calculator'); ?></span>
                                <span class="wcc-btn-loading" style="display: none;">
                                    <svg class="wcc-spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-dashoffset="12"/></svg>
                                    <?php _e('Sending...', 'website-cost-calculator'); ?>
                                </span>
                            </button>
                        </form>
                        
                        <div class="wcc-form-success" id="wcc-form-success" style="display: none;">
                            <div class="wcc-success-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            </div>
                            <h4><?php _e('Thank You!', 'website-cost-calculator'); ?></h4>
                            <p id="wcc-success-message"></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="wcc-navigation">
        <button type="button" class="wcc-nav-btn wcc-prev-btn" id="wcc-prev-btn" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12l4.58-4.59z"/></svg>
            <?php _e('Previous', 'website-cost-calculator'); ?>
        </button>
        <button type="button" class="wcc-nav-btn wcc-next-btn" id="wcc-next-btn">
            <?php _e('Next', 'website-cost-calculator'); ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12l-4.58 4.59z"/></svg>
        </button>
    </div>

    <!-- Floating Price Display -->
    <div class="wcc-floating-price" id="wcc-floating-price">
        <span class="wcc-floating-label"><?php _e('Est. Total:', 'website-cost-calculator'); ?></span>
        <span class="wcc-floating-amount" id="wcc-floating-amount"><?php echo $currency; ?>0</span>
    </div>
</div>
