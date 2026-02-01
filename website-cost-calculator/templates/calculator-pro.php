<?php
/**
 * Pro Calculator Template - Premium Features
 * Copyright (C) 2026 Haseeb Ur Rehman Mughal / EzyOnTech
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define helper function BEFORE it's used
if (!function_exists('wcc_get_industry_icon')) {
    function wcc_get_industry_icon($icon) {
        $icon_map = array(
            'utensils' => 'coffee',
            'shopping-cart' => 'shopping-cart',
            'briefcase' => 'briefcase',
            'heartbeat' => 'heart',
            'home' => 'home',
            'rocket' => 'rocket',
            'heart' => 'heart',
            'graduation-cap' => 'book',
            'palette' => 'image',
            'cog' => 'settings',
        );
        return isset($icon_map[$icon]) ? $icon_map[$icon] : 'circle';
    }
}

$wcc = Website_Cost_Calculator::get_instance();
$default_options = $wcc->get_default_options();
$options = $wcc->get_options();

// Merge with defaults to ensure all keys exist
$options = wp_parse_args($options, $default_options);

// Ensure all required arrays exist
$options['industry_presets'] = isset($options['industry_presets']) && is_array($options['industry_presets']) ? $options['industry_presets'] : $default_options['industry_presets'];
$options['website_types'] = isset($options['website_types']) && is_array($options['website_types']) ? $options['website_types'] : $default_options['website_types'];
$options['page_ranges'] = isset($options['page_ranges']) && is_array($options['page_ranges']) ? $options['page_ranges'] : $default_options['page_ranges'];
$options['design_options'] = isset($options['design_options']) && is_array($options['design_options']) ? $options['design_options'] : $default_options['design_options'];
$options['content_options'] = isset($options['content_options']) && is_array($options['content_options']) ? $options['content_options'] : $default_options['content_options'];
$options['features'] = isset($options['features']) && is_array($options['features']) ? $options['features'] : $default_options['features'];
$options['ecommerce_features'] = isset($options['ecommerce_features']) && is_array($options['ecommerce_features']) ? $options['ecommerce_features'] : $default_options['ecommerce_features'];
$options['seo_marketing'] = isset($options['seo_marketing']) && is_array($options['seo_marketing']) ? $options['seo_marketing'] : $default_options['seo_marketing'];
$options['timeline_options'] = isset($options['timeline_options']) && is_array($options['timeline_options']) ? $options['timeline_options'] : $default_options['timeline_options'];
$options['hosting_options'] = isset($options['hosting_options']) && is_array($options['hosting_options']) ? $options['hosting_options'] : $default_options['hosting_options'];
$options['maintenance_plans'] = isset($options['maintenance_plans']) && is_array($options['maintenance_plans']) ? $options['maintenance_plans'] : $default_options['maintenance_plans'];
$options['form_settings'] = isset($options['form_settings']) && is_array($options['form_settings']) ? $options['form_settings'] : $default_options['form_settings'];

$currency = isset($options['currency_symbol']) ? $options['currency_symbol'] : '$';
$theme_class = isset($atts['theme']) ? 'wcc-theme-' . esc_attr($atts['theme']) : 'wcc-theme-default';
$compact_class = isset($atts['compact']) && $atts['compact'] === 'true' ? 'wcc-compact' : '';
$preselect_industry = isset($atts['industry']) ? esc_attr($atts['industry']) : '';
?>

<div class="wcc-calculator-pro <?php echo $theme_class . ' ' . $compact_class; ?>" id="wcc-calculator" data-preselect="<?php echo $preselect_industry; ?>">
    
    <!-- Header with Logo and Save/Load -->
    <div class="wcc-header">
        <div class="wcc-header-left">
            <h2 class="wcc-title">
                <i data-feather="calculator"></i>
                <?php _e('Website Cost Calculator', 'website-cost-calculator'); ?>
            </h2>
            <p class="wcc-subtitle"><?php _e('Get an instant estimate for your project', 'website-cost-calculator'); ?></p>
        </div>
        <div class="wcc-header-actions">
            <button type="button" class="wcc-action-btn" id="wcc-load-quote-btn" title="<?php _e('Load Saved Quote', 'website-cost-calculator'); ?>">
                <i data-feather="download"></i>
                <span><?php _e('Load', 'website-cost-calculator'); ?></span>
            </button>
            <button type="button" class="wcc-action-btn" id="wcc-save-quote-btn" title="<?php _e('Save Quote', 'website-cost-calculator'); ?>">
                <i data-feather="save"></i>
                <span><?php _e('Save', 'website-cost-calculator'); ?></span>
            </button>
        </div>
    </div>

    <!-- Enhanced Progress Bar -->
    <div class="wcc-progress-wrapper">
        <div class="wcc-progress-bar">
            <div class="wcc-progress-fill" id="wcc-progress-fill"></div>
        </div>
        <div class="wcc-progress-steps">
            <div class="wcc-progress-step active" data-step="1">
                <div class="wcc-step-icon">
                    <i data-feather="briefcase"></i>
                </div>
                <span class="wcc-step-label"><?php _e('Industry', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="2">
                <div class="wcc-step-icon">
                    <i data-feather="layout"></i>
                </div>
                <span class="wcc-step-label"><?php _e('Type & Size', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="3">
                <div class="wcc-step-icon">
                    <i data-feather="sliders"></i>
                </div>
                <span class="wcc-step-label"><?php _e('Design', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="4">
                <div class="wcc-step-icon">
                    <i data-feather="grid"></i>
                </div>
                <span class="wcc-step-label"><?php _e('Features', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="5">
                <div class="wcc-step-icon">
                    <i data-feather="package"></i>
                </div>
                <span class="wcc-step-label"><?php _e('Extras', 'website-cost-calculator'); ?></span>
            </div>
            <div class="wcc-progress-step" data-step="6">
                <div class="wcc-step-icon">
                    <i data-feather="file-text"></i>
                </div>
                <span class="wcc-step-label"><?php _e('Summary', 'website-cost-calculator'); ?></span>
            </div>
        </div>
    </div>

    <!-- Calculator Steps Container -->
    <div class="wcc-steps-container">
        
        <!-- Step 1: Industry Selection -->
        <div class="wcc-step active" id="wcc-step-1" data-step="1">
            <div class="wcc-step-header">
                <div class="wcc-step-badge"><?php _e('Step 1 of 6', 'website-cost-calculator'); ?></div>
                <h2><?php _e('What industry is your business in?', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Select your industry to get tailored recommendations and accurate pricing.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-industry-grid">
                <?php foreach ($options['industry_presets'] as $index => $industry): ?>
                <label class="wcc-industry-card">
                    <input type="radio" name="industry" value="<?php echo esc_attr($industry['id']); ?>" 
                           data-name="<?php echo esc_attr($industry['name']); ?>"
                           data-modifier="<?php echo esc_attr($industry['base_modifier']); ?>"
                           data-suggested-type="<?php echo esc_attr($industry['suggested_type'] ?? ''); ?>"
                           data-suggested-features="<?php echo esc_attr(json_encode($industry['suggested_features'] ?? [])); ?>">
                    <div class="wcc-industry-content">
                        <div class="wcc-industry-icon">
                            <i data-feather="<?php echo esc_attr(wcc_get_industry_icon($industry['icon'])); ?>"></i>
                        </div>
                        <h3><?php echo esc_html($industry['name']); ?></h3>
                        <p><?php echo esc_html($industry['description']); ?></p>
                    </div>
                    <div class="wcc-check-indicator">
                        <i data-feather="check"></i>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step 2: Website Type & Pages -->
        <div class="wcc-step" id="wcc-step-2" data-step="2">
            <div class="wcc-step-header">
                <div class="wcc-step-badge"><?php _e('Step 2 of 6', 'website-cost-calculator'); ?></div>
                <h2><?php _e('What type of website do you need?', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Choose the option that best matches your project requirements.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title">
                    <i data-feather="monitor"></i>
                    <?php _e('Website Type', 'website-cost-calculator'); ?>
                </h3>
                <div class="wcc-type-grid">
                    <?php foreach ($options['website_types'] as $index => $type): ?>
                    <label class="wcc-type-card <?php echo $index === 1 ? 'recommended' : ''; ?>">
                        <?php if ($index === 1): ?>
                        <div class="wcc-recommended-badge"><?php _e('Most Popular', 'website-cost-calculator'); ?></div>
                        <?php endif; ?>
                        <input type="radio" name="website_type" value="<?php echo $index; ?>" 
                               data-name="<?php echo esc_attr($type['name']); ?>" 
                               data-price="<?php echo esc_attr($type['price']); ?>">
                        <div class="wcc-type-icon">
                            <i data-feather="<?php echo esc_attr($type['icon'] ?? 'globe'); ?>"></i>
                        </div>
                        <h4><?php echo esc_html($type['name']); ?></h4>
                        <p><?php echo esc_html($type['description'] ?? ''); ?></p>
                        <div class="wcc-type-price">
                            <span class="wcc-from"><?php _e('From', 'website-cost-calculator'); ?></span>
                            <span class="wcc-amount"><?php echo esc_html($currency . number_format($type['price'], 0)); ?></span>
                        </div>
                        <div class="wcc-check-indicator">
                            <i data-feather="check"></i>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title">
                    <i data-feather="file-text"></i>
                    <?php _e('Number of Pages', 'website-cost-calculator'); ?>
                </h3>
                <div class="wcc-range-grid">
                    <?php foreach ($options['page_ranges'] as $index => $range): ?>
                    <label class="wcc-range-option">
                        <input type="radio" name="page_range" value="<?php echo $index; ?>" 
                               data-name="<?php echo esc_attr($range['name']); ?>" 
                               data-price="<?php echo esc_attr($range['price']); ?>">
                        <div class="wcc-range-content">
                            <span class="wcc-range-label"><?php echo esc_html($range['name']); ?></span>
                            <?php if ($range['price'] > 0): ?>
                            <span class="wcc-range-price">+<?php echo esc_html($currency . number_format($range['price'], 0)); ?></span>
                            <?php else: ?>
                            <span class="wcc-range-price wcc-included"><?php _e('Included', 'website-cost-calculator'); ?></span>
                            <?php endif; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 3: Design -->
        <div class="wcc-step" id="wcc-step-3" data-step="3">
            <div class="wcc-step-header">
                <div class="wcc-step-badge"><?php _e('Step 3 of 6', 'website-cost-calculator'); ?></div>
                <h2><?php _e('How should your website look?', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Choose a design approach that fits your brand and budget.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-design-options">
                <?php foreach ($options['design_options'] as $index => $design): ?>
                <label class="wcc-design-card <?php echo $index === 2 ? 'popular' : ''; ?>">
                    <?php if ($index === 2): ?>
                    <div class="wcc-popular-badge"><?php _e('Best Value', 'website-cost-calculator'); ?></div>
                    <?php endif; ?>
                    <input type="radio" name="design_option" value="<?php echo $index; ?>" 
                           data-name="<?php echo esc_attr($design['name']); ?>" 
                           data-price="<?php echo esc_attr($design['price']); ?>">
                    <div class="wcc-design-header">
                        <div class="wcc-design-icon">
                            <i data-feather="<?php echo esc_attr($design['icon'] ?? 'layout'); ?>"></i>
                        </div>
                        <div class="wcc-design-info">
                            <h4><?php echo esc_html($design['name']); ?></h4>
                            <p><?php echo esc_html($design['description'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="wcc-design-price">
                        <?php if ($design['price'] > 0): ?>
                        <span>+<?php echo esc_html($currency . number_format($design['price'], 0)); ?></span>
                        <?php else: ?>
                        <span class="wcc-included"><?php _e('Included', 'website-cost-calculator'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="wcc-check-indicator">
                        <i data-feather="check"></i>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            
            <div class="wcc-section">
                <h3 class="wcc-section-title">
                    <i data-feather="edit-2"></i>
                    <?php _e('Content & Media', 'website-cost-calculator'); ?>
                </h3>
                <div class="wcc-content-grid">
                    <?php foreach ($options['content_options'] as $index => $content): ?>
                    <label class="wcc-content-option">
                        <input type="checkbox" name="content_options[]" value="<?php echo $index; ?>" 
                               data-name="<?php echo esc_attr($content['name']); ?>" 
                               data-price="<?php echo esc_attr($content['price']); ?>">
                        <div class="wcc-checkbox-visual">
                            <i data-feather="check"></i>
                        </div>
                        <div class="wcc-content-info">
                            <span class="wcc-content-name"><?php echo esc_html($content['name']); ?></span>
                            <span class="wcc-content-desc"><?php echo esc_html($content['description'] ?? ''); ?></span>
                        </div>
                        <span class="wcc-content-price">
                            <?php if ($content['price'] > 0): ?>
                            +<?php echo esc_html($currency . number_format($content['price'], 0)); ?>
                            <?php else: ?>
                            <span class="wcc-free"><?php _e('Free', 'website-cost-calculator'); ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 4: Features -->
        <div class="wcc-step" id="wcc-step-4" data-step="4">
            <div class="wcc-step-header">
                <div class="wcc-step-badge"><?php _e('Step 4 of 6', 'website-cost-calculator'); ?></div>
                <h2><?php _e('What features do you need?', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Select the features to include in your website. Popular choices are highlighted.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-features-header">
                <button type="button" class="wcc-select-btn" id="wcc-select-popular"><?php _e('Select Popular', 'website-cost-calculator'); ?></button>
                <button type="button" class="wcc-select-btn wcc-outline" id="wcc-clear-features"><?php _e('Clear All', 'website-cost-calculator'); ?></button>
            </div>
            
            <div class="wcc-features-grid">
                <?php foreach ($options['features'] as $index => $feature): ?>
                <label class="wcc-feature-card <?php echo ($feature['popular'] ?? false) ? 'popular' : ''; ?>">
                    <?php if ($feature['popular'] ?? false): ?>
                    <div class="wcc-popular-tag"><?php _e('Popular', 'website-cost-calculator'); ?></div>
                    <?php endif; ?>
                    <input type="checkbox" name="features[]" value="<?php echo $index; ?>" 
                           data-name="<?php echo esc_attr($feature['name']); ?>" 
                           data-price="<?php echo esc_attr($feature['price']); ?>"
                           data-popular="<?php echo ($feature['popular'] ?? false) ? '1' : '0'; ?>">
                    <div class="wcc-feature-icon">
                        <i data-feather="<?php echo esc_attr($feature['icon'] ?? 'check-circle'); ?>"></i>
                    </div>
                    <div class="wcc-feature-info">
                        <h4><?php echo esc_html($feature['name']); ?></h4>
                        <p><?php echo esc_html($feature['description'] ?? ''); ?></p>
                    </div>
                    <div class="wcc-feature-price">
                        +<?php echo esc_html($currency . number_format($feature['price'], 0)); ?>
                    </div>
                    <div class="wcc-feature-check">
                        <i data-feather="check"></i>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            
            <div class="wcc-section wcc-ecommerce-section" id="wcc-ecommerce-section" style="display: none;">
                <h3 class="wcc-section-title">
                    <i data-feather="shopping-cart"></i>
                    <?php _e('E-commerce Features', 'website-cost-calculator'); ?>
                </h3>
                <div class="wcc-features-grid">
                    <?php foreach ($options['ecommerce_features'] as $index => $feature): ?>
                    <label class="wcc-feature-card">
                        <input type="checkbox" name="ecommerce_features[]" value="<?php echo $index; ?>" 
                               data-name="<?php echo esc_attr($feature['name']); ?>" 
                               data-price="<?php echo esc_attr($feature['price']); ?>">
                        <div class="wcc-feature-icon">
                            <i data-feather="<?php echo esc_attr($feature['icon'] ?? 'package'); ?>"></i>
                        </div>
                        <div class="wcc-feature-info">
                            <h4><?php echo esc_html($feature['name']); ?></h4>
                            <p><?php echo esc_html($feature['description'] ?? ''); ?></p>
                        </div>
                        <div class="wcc-feature-price">
                            +<?php echo esc_html($currency . number_format($feature['price'], 0)); ?>
                        </div>
                        <div class="wcc-feature-check">
                            <i data-feather="check"></i>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 5: Extras (SEO, Hosting, Maintenance, Timeline) -->
        <div class="wcc-step" id="wcc-step-5" data-step="5">
            <div class="wcc-step-header">
                <div class="wcc-step-badge"><?php _e('Step 5 of 6', 'website-cost-calculator'); ?></div>
                <h2><?php _e('Additional Services & Timeline', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Enhance your website with marketing, hosting, and ongoing support.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-extras-layout">
                <div class="wcc-extras-left">
                    <div class="wcc-section">
                        <h3 class="wcc-section-title">
                            <i data-feather="trending-up"></i>
                            <?php _e('SEO & Marketing', 'website-cost-calculator'); ?>
                        </h3>
                        <div class="wcc-extras-grid">
                            <?php foreach ($options['seo_marketing'] as $index => $item): ?>
                            <label class="wcc-extra-option">
                                <input type="checkbox" name="seo_marketing[]" value="<?php echo $index; ?>" 
                                       data-name="<?php echo esc_attr($item['name']); ?>" 
                                       data-price="<?php echo esc_attr($item['price']); ?>">
                                <div class="wcc-checkbox-visual">
                                    <i data-feather="check"></i>
                                </div>
                                <div class="wcc-extra-info">
                                    <span class="wcc-extra-name"><?php echo esc_html($item['name']); ?></span>
                                    <span class="wcc-extra-desc"><?php echo esc_html($item['description'] ?? ''); ?></span>
                                </div>
                                <span class="wcc-extra-price">+<?php echo esc_html($currency . number_format($item['price'], 0)); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="wcc-extras-right">
                    <div class="wcc-section">
                        <h3 class="wcc-section-title">
                            <i data-feather="clock"></i>
                            <?php _e('Project Timeline', 'website-cost-calculator'); ?>
                        </h3>
                        <div class="wcc-timeline-options">
                            <?php foreach ($options['timeline_options'] as $index => $timeline): ?>
                            <label class="wcc-timeline-option <?php echo $timeline['multiplier'] == 1 ? 'recommended' : ''; ?>">
                                <input type="radio" name="timeline" value="<?php echo $index; ?>" 
                                       data-name="<?php echo esc_attr($timeline['name']); ?>" 
                                       data-multiplier="<?php echo esc_attr($timeline['multiplier']); ?>"
                                       <?php echo $timeline['multiplier'] == 1 ? 'checked' : ''; ?>>
                                <div class="wcc-timeline-icon">
                                    <i data-feather="<?php echo esc_attr($timeline['icon'] ?? 'clock'); ?>"></i>
                                </div>
                                <div class="wcc-timeline-info">
                                    <span class="wcc-timeline-name"><?php echo esc_html($timeline['name']); ?></span>
                                    <span class="wcc-timeline-desc"><?php echo esc_html($timeline['description'] ?? ''); ?></span>
                                </div>
                                <?php if ($timeline['multiplier'] != 1): ?>
                                <span class="wcc-timeline-modifier <?php echo $timeline['multiplier'] < 1 ? 'discount' : 'rush'; ?>">
                                    <?php 
                                    $diff = ($timeline['multiplier'] - 1) * 100;
                                    echo ($diff > 0 ? '+' : '') . number_format($diff, 0) . '%';
                                    ?>
                                </span>
                                <?php else: ?>
                                <span class="wcc-timeline-modifier standard"><?php _e('Standard', 'website-cost-calculator'); ?></span>
                                <?php endif; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="wcc-section">
                        <h3 class="wcc-section-title">
                            <i data-feather="server"></i>
                            <?php _e('Hosting', 'website-cost-calculator'); ?>
                        </h3>
                        <div class="wcc-hosting-options">
                            <?php foreach ($options['hosting_options'] as $index => $hosting): ?>
                            <label class="wcc-hosting-option">
                                <input type="radio" name="hosting" value="<?php echo $index; ?>" 
                                       data-name="<?php echo esc_attr($hosting['name']); ?>" 
                                       data-monthly="<?php echo esc_attr($hosting['monthly']); ?>"
                                       <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <div class="wcc-hosting-info">
                                    <span class="wcc-hosting-name"><?php echo esc_html($hosting['name']); ?></span>
                                </div>
                                <?php if ($hosting['monthly'] > 0): ?>
                                <span class="wcc-hosting-price"><?php echo esc_html($currency . number_format($hosting['monthly'], 0)); ?>/mo</span>
                                <?php else: ?>
                                <span class="wcc-hosting-price wcc-free"><?php echo $index === 0 ? '-' : __('Setup Only', 'website-cost-calculator'); ?></span>
                                <?php endif; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="wcc-section">
                        <h3 class="wcc-section-title">
                            <i data-feather="shield"></i>
                            <?php _e('Maintenance & Support', 'website-cost-calculator'); ?>
                        </h3>
                        <div class="wcc-maintenance-options">
                            <?php foreach ($options['maintenance_plans'] as $index => $plan): ?>
                            <label class="wcc-maintenance-option">
                                <input type="radio" name="maintenance" value="<?php echo $index; ?>" 
                                       data-name="<?php echo esc_attr($plan['name']); ?>" 
                                       data-price="<?php echo esc_attr($plan['price']); ?>"
                                       <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <div class="wcc-maintenance-info">
                                    <span class="wcc-maintenance-name"><?php echo esc_html($plan['name']); ?></span>
                                    <span class="wcc-maintenance-desc"><?php echo esc_html($plan['description'] ?? ''); ?></span>
                                </div>
                                <?php if ($plan['price'] > 0): ?>
                                <span class="wcc-maintenance-price"><?php echo esc_html($currency . number_format($plan['price'], 0)); ?>/mo</span>
                                <?php else: ?>
                                <span class="wcc-maintenance-price wcc-free">-</span>
                                <?php endif; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Summary & Quote -->
        <div class="wcc-step" id="wcc-step-6" data-step="6">
            <div class="wcc-step-header">
                <div class="wcc-step-badge"><?php _e('Final Step', 'website-cost-calculator'); ?></div>
                <h2><?php _e('Your Personalized Quote', 'website-cost-calculator'); ?></h2>
                <p><?php _e('Review your selections and get your detailed estimate.', 'website-cost-calculator'); ?></p>
            </div>
            
            <div class="wcc-summary-layout">
                <div class="wcc-summary-main">
                    <!-- Price Comparison Cards -->
                    <div class="wcc-price-comparison" id="wcc-price-comparison">
                        <div class="wcc-price-tier wcc-tier-budget">
                            <div class="wcc-tier-header">
                                <span class="wcc-tier-label"><?php _e('Budget', 'website-cost-calculator'); ?></span>
                                <i data-feather="dollar-sign"></i>
                            </div>
                            <div class="wcc-tier-price" id="wcc-price-low"><?php echo $currency; ?>0</div>
                            <p class="wcc-tier-desc"><?php _e('Minimal features, basic design', 'website-cost-calculator'); ?></p>
                        </div>
                        <div class="wcc-price-tier wcc-tier-standard active">
                            <div class="wcc-tier-badge"><?php _e('Your Quote', 'website-cost-calculator'); ?></div>
                            <div class="wcc-tier-header">
                                <span class="wcc-tier-label"><?php _e('Your Selection', 'website-cost-calculator'); ?></span>
                                <i data-feather="star"></i>
                            </div>
                            <div class="wcc-tier-price" id="wcc-price-mid"><?php echo $currency; ?>0</div>
                            <p class="wcc-tier-desc"><?php _e('Based on your selections', 'website-cost-calculator'); ?></p>
                        </div>
                        <div class="wcc-price-tier wcc-tier-premium">
                            <div class="wcc-tier-header">
                                <span class="wcc-tier-label"><?php _e('Premium', 'website-cost-calculator'); ?></span>
                                <i data-feather="award"></i>
                            </div>
                            <div class="wcc-tier-price" id="wcc-price-high"><?php echo $currency; ?>0</div>
                            <p class="wcc-tier-desc"><?php _e('All features, premium quality', 'website-cost-calculator'); ?></p>
                        </div>
                    </div>
                    
                    <!-- Cost Breakdown Chart -->
                    <div class="wcc-breakdown-section">
                        <h3>
                            <i data-feather="pie-chart"></i>
                            <?php _e('Cost Breakdown', 'website-cost-calculator'); ?>
                        </h3>
                        <div class="wcc-chart-container">
                            <canvas id="wcc-breakdown-chart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Detailed Summary -->
                    <div class="wcc-detailed-summary">
                        <h3>
                            <i data-feather="list"></i>
                            <?php _e('Detailed Summary', 'website-cost-calculator'); ?>
                        </h3>
                        <div class="wcc-summary-items" id="wcc-summary-items">
                            <!-- Populated by JavaScript -->
                        </div>
                        
                        <div class="wcc-summary-totals">
                            <div class="wcc-total-row wcc-subtotal">
                                <span><?php _e('Subtotal', 'website-cost-calculator'); ?></span>
                                <span id="wcc-subtotal"><?php echo $currency; ?>0</span>
                            </div>
                            <div class="wcc-total-row wcc-timeline-adj" id="wcc-timeline-row" style="display: none;">
                                <span><?php _e('Timeline Adjustment', 'website-cost-calculator'); ?></span>
                                <span id="wcc-timeline-cost"><?php echo $currency; ?>0</span>
                            </div>
                            <div class="wcc-total-row wcc-grand-total">
                                <span><?php _e('Total Investment', 'website-cost-calculator'); ?></span>
                                <span id="wcc-grand-total"><?php echo $currency; ?>0</span>
                            </div>
                            <div class="wcc-total-row wcc-monthly" id="wcc-monthly-row">
                                <span><?php _e('Monthly Recurring', 'website-cost-calculator'); ?></span>
                                <span id="wcc-monthly-total"><?php echo $currency; ?>0/mo</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="wcc-summary-actions">
                        <button type="button" class="wcc-btn wcc-btn-outline" id="wcc-download-pdf">
                            <i data-feather="download"></i>
                            <?php _e('Download PDF', 'website-cost-calculator'); ?>
                        </button>
                        <button type="button" class="wcc-btn wcc-btn-outline" id="wcc-share-quote">
                            <i data-feather="share-2"></i>
                            <?php _e('Share Quote', 'website-cost-calculator'); ?>
                        </button>
                        <button type="button" class="wcc-btn wcc-btn-outline" id="wcc-print-quote">
                            <i data-feather="printer"></i>
                            <?php _e('Print', 'website-cost-calculator'); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Contact Form Sidebar -->
                <?php if ($options['form_settings']['show_contact_form'] ?? true): ?>
                <div class="wcc-summary-sidebar">
                    <div class="wcc-contact-card">
                        <div class="wcc-contact-header">
                            <i data-feather="send"></i>
                            <h3><?php _e('Get Your Quote', 'website-cost-calculator'); ?></h3>
                            <p><?php _e('Enter your details for a detailed proposal', 'website-cost-calculator'); ?></p>
                        </div>
                        
                        <form class="wcc-contact-form" id="wcc-contact-form">
                            <div class="wcc-form-field">
                                <label for="wcc-name">
                                    <?php _e('Full Name', 'website-cost-calculator'); ?>
                                    <span class="required">*</span>
                                </label>
                                <div class="wcc-input-wrapper">
                                    <i data-feather="user"></i>
                                    <input type="text" id="wcc-name" name="name" required placeholder="<?php _e('John Smith', 'website-cost-calculator'); ?>">
                                </div>
                            </div>
                            
                            <div class="wcc-form-field">
                                <label for="wcc-email">
                                    <?php _e('Email Address', 'website-cost-calculator'); ?>
                                    <span class="required">*</span>
                                </label>
                                <div class="wcc-input-wrapper">
                                    <i data-feather="mail"></i>
                                    <input type="email" id="wcc-email" name="email" required placeholder="<?php _e('john@company.com', 'website-cost-calculator'); ?>">
                                </div>
                            </div>
                            
                            <div class="wcc-form-row">
                                <div class="wcc-form-field">
                                    <label for="wcc-phone"><?php _e('Phone', 'website-cost-calculator'); ?></label>
                                    <div class="wcc-input-wrapper">
                                        <i data-feather="phone"></i>
                                        <input type="tel" id="wcc-phone" name="phone" placeholder="<?php _e('(555) 123-4567', 'website-cost-calculator'); ?>">
                                    </div>
                                </div>
                                <div class="wcc-form-field">
                                    <label for="wcc-company"><?php _e('Company', 'website-cost-calculator'); ?></label>
                                    <div class="wcc-input-wrapper">
                                        <i data-feather="briefcase"></i>
                                        <input type="text" id="wcc-company" name="company" placeholder="<?php _e('Company Inc.', 'website-cost-calculator'); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="wcc-form-field">
                                <label for="wcc-notes"><?php _e('Project Details', 'website-cost-calculator'); ?></label>
                                <textarea id="wcc-notes" name="notes" rows="3" placeholder="<?php _e('Tell us more about your project goals and requirements...', 'website-cost-calculator'); ?>"></textarea>
                            </div>
                            
                            <button type="submit" class="wcc-submit-btn" id="wcc-submit-btn">
                                <span class="wcc-btn-text">
                                    <i data-feather="send"></i>
                                    <?php _e('Get My Free Quote', 'website-cost-calculator'); ?>
                                </span>
                                <span class="wcc-btn-loading" style="display: none;">
                                    <span class="wcc-spinner"></span>
                                    <?php _e('Sending...', 'website-cost-calculator'); ?>
                                </span>
                            </button>
                            
                            <p class="wcc-form-note">
                                <i data-feather="lock"></i>
                                <?php _e('Your information is secure and will never be shared.', 'website-cost-calculator'); ?>
                            </p>
                        </form>
                        
                        <div class="wcc-form-success" id="wcc-form-success" style="display: none;">
                            <div class="wcc-success-animation">
                                <svg class="wcc-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="wcc-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                                    <path class="wcc-checkmark-check" fill="none" d="m14.1 27.2 7.1 7.2 16.7-16.8"/>
                                </svg>
                            </div>
                            <h4><?php _e('Quote Submitted!', 'website-cost-calculator'); ?></h4>
                            <p id="wcc-success-message"></p>
                            <button type="button" class="wcc-btn wcc-btn-outline" id="wcc-start-over">
                                <i data-feather="refresh-cw"></i>
                                <?php _e('Start New Quote', 'website-cost-calculator'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="wcc-navigation">
        <button type="button" class="wcc-nav-btn wcc-prev-btn" id="wcc-prev-btn" style="display: none;">
            <i data-feather="arrow-left"></i>
            <span><?php _e('Previous', 'website-cost-calculator'); ?></span>
        </button>
        <button type="button" class="wcc-nav-btn wcc-next-btn" id="wcc-next-btn">
            <span><?php _e('Next Step', 'website-cost-calculator'); ?></span>
            <i data-feather="arrow-right"></i>
        </button>
    </div>

    <!-- Floating Price Widget -->
    <div class="wcc-floating-widget" id="wcc-floating-widget">
        <div class="wcc-widget-content">
            <div class="wcc-widget-label"><?php _e('Estimated Total', 'website-cost-calculator'); ?></div>
            <div class="wcc-widget-price" id="wcc-floating-price"><?php echo $currency; ?>0</div>
            <div class="wcc-widget-monthly" id="wcc-floating-monthly"><?php _e('+ $0/mo', 'website-cost-calculator'); ?></div>
        </div>
        <button type="button" class="wcc-widget-toggle" id="wcc-widget-toggle">
            <i data-feather="chevron-up"></i>
        </button>
    </div>

    <!-- Modals -->
    <div class="wcc-modal" id="wcc-save-modal">
        <div class="wcc-modal-overlay"></div>
        <div class="wcc-modal-content">
            <button type="button" class="wcc-modal-close"><i data-feather="x"></i></button>
            <h3><?php _e('Save Your Quote', 'website-cost-calculator'); ?></h3>
            <p><?php _e('Your quote has been saved! Use this code to access it later:', 'website-cost-calculator'); ?></p>
            <div class="wcc-quote-code" id="wcc-saved-code"></div>
            <button type="button" class="wcc-btn wcc-btn-primary" id="wcc-copy-code">
                <i data-feather="copy"></i>
                <?php _e('Copy Code', 'website-cost-calculator'); ?>
            </button>
        </div>
    </div>
    
    <div class="wcc-modal" id="wcc-load-modal">
        <div class="wcc-modal-overlay"></div>
        <div class="wcc-modal-content">
            <button type="button" class="wcc-modal-close"><i data-feather="x"></i></button>
            <h3><?php _e('Load Saved Quote', 'website-cost-calculator'); ?></h3>
            <p><?php _e('Enter your quote code to restore your saved selections:', 'website-cost-calculator'); ?></p>
            <div class="wcc-form-field">
                <input type="text" id="wcc-load-code-input" placeholder="<?php _e('Enter 8-character code', 'website-cost-calculator'); ?>" maxlength="8">
            </div>
            <button type="button" class="wcc-btn wcc-btn-primary" id="wcc-load-code-btn">
                <i data-feather="download"></i>
                <?php _e('Load Quote', 'website-cost-calculator'); ?>
            </button>
        </div>
    </div>
    
    <div class="wcc-modal" id="wcc-share-modal">
        <div class="wcc-modal-overlay"></div>
        <div class="wcc-modal-content">
            <button type="button" class="wcc-modal-close"><i data-feather="x"></i></button>
            <h3><?php _e('Share Your Quote', 'website-cost-calculator'); ?></h3>
            <p><?php _e('Share this link with others:', 'website-cost-calculator'); ?></p>
            <div class="wcc-share-link">
                <input type="text" id="wcc-share-url" readonly>
                <button type="button" id="wcc-copy-link"><i data-feather="copy"></i></button>
            </div>
            <div class="wcc-share-buttons">
                <button type="button" class="wcc-share-btn wcc-share-email" id="wcc-share-email">
                    <i data-feather="mail"></i> Email
                </button>
                <button type="button" class="wcc-share-btn wcc-share-twitter" id="wcc-share-twitter">
                    <i data-feather="twitter"></i> Twitter
                </button>
                <button type="button" class="wcc-share-btn wcc-share-linkedin" id="wcc-share-linkedin">
                    <i data-feather="linkedin"></i> LinkedIn
                </button>
            </div>
        </div>
    </div>
</div>
