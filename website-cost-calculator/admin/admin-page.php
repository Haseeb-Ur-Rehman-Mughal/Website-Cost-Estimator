<?php
/**
 * Admin Settings Page Template
 * Author: Haseeb Ur Rehman Mughal
 * Website: https://ezyontech.com
 */

if (!defined('ABSPATH')) {
    exit;
}

$wcc = Website_Cost_Calculator::get_instance();
$default_options = $wcc->get_default_options();

// Handle form submission FIRST before getting options
$settings_saved = false;
$settings_reset = false;

if (isset($_POST['wcc_save_settings']) && isset($_POST['wcc_settings_nonce'])) {
    if (wp_verify_nonce($_POST['wcc_settings_nonce'], 'wcc_save_settings')) {
        
        // Get current options as base
        $options = get_option('wcc_options', $default_options);
        
        // Currency settings
        $options['currency_symbol'] = sanitize_text_field($_POST['currency_symbol'] ?? '$');
        $options['currency_position'] = sanitize_text_field($_POST['currency_position'] ?? 'before');
        
        // Styling settings
        $options['styling'] = array(
            'primary_color' => sanitize_hex_color($_POST['primary_color'] ?? '#6366F1'),
            'secondary_color' => sanitize_hex_color($_POST['secondary_color'] ?? '#10B981'),
            'accent_color' => sanitize_hex_color($_POST['accent_color'] ?? '#F59E0B'),
            'text_color' => sanitize_hex_color($_POST['text_color'] ?? '#1F2937'),
            'background_color' => sanitize_hex_color($_POST['background_color'] ?? '#F8FAFC'),
            'card_color' => sanitize_hex_color($_POST['card_color'] ?? '#FFFFFF'),
            'gradient_start' => sanitize_hex_color($_POST['gradient_start'] ?? '#6366F1'),
            'gradient_end' => sanitize_hex_color($_POST['gradient_end'] ?? '#8B5CF6'),
        );
        
        // Form settings
        $options['form_settings'] = array(
            'show_contact_form' => isset($_POST['show_contact_form']),
            'require_contact' => isset($_POST['require_contact']),
            'email_recipient' => sanitize_email($_POST['email_recipient'] ?? get_option('admin_email')),
            'success_message' => sanitize_textarea_field($_POST['success_message'] ?? 'Thank you! We will contact you shortly.'),
            'enable_pdf' => isset($_POST['enable_pdf']),
            'enable_save_quote' => isset($_POST['enable_save_quote']),
        );
        
        // Website types
        if (isset($_POST['website_types']) && is_array($_POST['website_types'])) {
            $website_types = array();
            foreach ($_POST['website_types'] as $type) {
                if (!empty($type['name'])) {
                    $website_types[] = array(
                        'name' => sanitize_text_field($type['name']),
                        'price' => floatval($type['price'] ?? 0),
                        'description' => sanitize_text_field($type['description'] ?? ''),
                        'icon' => sanitize_text_field($type['icon'] ?? 'globe'),
                    );
                }
            }
            if (!empty($website_types)) {
                $options['website_types'] = $website_types;
            }
        }
        
        // Page ranges
        if (isset($_POST['page_ranges']) && is_array($_POST['page_ranges'])) {
            $page_ranges = array();
            foreach ($_POST['page_ranges'] as $range) {
                if (!empty($range['name'])) {
                    $page_ranges[] = array(
                        'name' => sanitize_text_field($range['name']),
                        'price' => floatval($range['price'] ?? 0),
                    );
                }
            }
            if (!empty($page_ranges)) {
                $options['page_ranges'] = $page_ranges;
            }
        }
        
        // Design options
        if (isset($_POST['design_options']) && is_array($_POST['design_options'])) {
            $design_options = array();
            foreach ($_POST['design_options'] as $opt) {
                if (!empty($opt['name'])) {
                    $design_options[] = array(
                        'name' => sanitize_text_field($opt['name']),
                        'price' => floatval($opt['price'] ?? 0),
                        'description' => sanitize_text_field($opt['description'] ?? ''),
                        'icon' => sanitize_text_field($opt['icon'] ?? 'layout'),
                    );
                }
            }
            if (!empty($design_options)) {
                $options['design_options'] = $design_options;
            }
        }
        
        // Features
        if (isset($_POST['features']) && is_array($_POST['features'])) {
            $features = array();
            foreach ($_POST['features'] as $feature) {
                if (!empty($feature['name'])) {
                    $features[] = array(
                        'name' => sanitize_text_field($feature['name']),
                        'price' => floatval($feature['price'] ?? 0),
                        'description' => sanitize_text_field($feature['description'] ?? ''),
                        'icon' => sanitize_text_field($feature['icon'] ?? 'check-circle'),
                        'popular' => isset($feature['popular']),
                    );
                }
            }
            if (!empty($features)) {
                $options['features'] = $features;
            }
        }
        
        // E-commerce features
        if (isset($_POST['ecommerce_features']) && is_array($_POST['ecommerce_features'])) {
            $ecommerce_features = array();
            foreach ($_POST['ecommerce_features'] as $feature) {
                if (!empty($feature['name'])) {
                    $ecommerce_features[] = array(
                        'name' => sanitize_text_field($feature['name']),
                        'price' => floatval($feature['price'] ?? 0),
                        'description' => sanitize_text_field($feature['description'] ?? ''),
                        'icon' => sanitize_text_field($feature['icon'] ?? 'package'),
                    );
                }
            }
            if (!empty($ecommerce_features)) {
                $options['ecommerce_features'] = $ecommerce_features;
            }
        }
        
        // SEO & Marketing
        if (isset($_POST['seo_marketing']) && is_array($_POST['seo_marketing'])) {
            $seo_marketing = array();
            foreach ($_POST['seo_marketing'] as $item) {
                if (!empty($item['name'])) {
                    $seo_marketing[] = array(
                        'name' => sanitize_text_field($item['name']),
                        'price' => floatval($item['price'] ?? 0),
                        'description' => sanitize_text_field($item['description'] ?? ''),
                        'icon' => sanitize_text_field($item['icon'] ?? 'trending-up'),
                    );
                }
            }
            if (!empty($seo_marketing)) {
                $options['seo_marketing'] = $seo_marketing;
            }
        }
        
        // Maintenance plans
        if (isset($_POST['maintenance_plans']) && is_array($_POST['maintenance_plans'])) {
            $maintenance_plans = array();
            foreach ($_POST['maintenance_plans'] as $plan) {
                if (!empty($plan['name'])) {
                    $maintenance_plans[] = array(
                        'name' => sanitize_text_field($plan['name']),
                        'price' => floatval($plan['price'] ?? 0),
                        'description' => sanitize_text_field($plan['description'] ?? ''),
                        'icon' => sanitize_text_field($plan['icon'] ?? 'shield'),
                    );
                }
            }
            if (!empty($maintenance_plans)) {
                $options['maintenance_plans'] = $maintenance_plans;
            }
        }
        
        // Timeline options
        if (isset($_POST['timeline_options']) && is_array($_POST['timeline_options'])) {
            $timeline_options = array();
            foreach ($_POST['timeline_options'] as $timeline) {
                if (!empty($timeline['name'])) {
                    $timeline_options[] = array(
                        'name' => sanitize_text_field($timeline['name']),
                        'multiplier' => floatval($timeline['multiplier'] ?? 1),
                        'description' => sanitize_text_field($timeline['description'] ?? ''),
                        'icon' => sanitize_text_field($timeline['icon'] ?? 'clock'),
                    );
                }
            }
            if (!empty($timeline_options)) {
                $options['timeline_options'] = $timeline_options;
            }
        }
        
        // Hosting options
        if (isset($_POST['hosting_options']) && is_array($_POST['hosting_options'])) {
            $hosting_options = array();
            foreach ($_POST['hosting_options'] as $hosting) {
                if (!empty($hosting['name'])) {
                    $hosting_options[] = array(
                        'name' => sanitize_text_field($hosting['name']),
                        'price' => floatval($hosting['price'] ?? 0),
                        'monthly' => floatval($hosting['monthly'] ?? 0),
                        'description' => sanitize_text_field($hosting['description'] ?? ''),
                        'icon' => sanitize_text_field($hosting['icon'] ?? 'server'),
                    );
                }
            }
            if (!empty($hosting_options)) {
                $options['hosting_options'] = $hosting_options;
            }
        }
        
        // Save options
        $result = update_option('wcc_options', $options);
        $settings_saved = true;
    }
}

// Handle reset to defaults
if (isset($_POST['wcc_reset_defaults']) && isset($_POST['wcc_settings_nonce'])) {
    if (wp_verify_nonce($_POST['wcc_settings_nonce'], 'wcc_save_settings')) {
        delete_option('wcc_options');
        update_option('wcc_options', $default_options);
        $settings_reset = true;
    }
}

// NOW get the options (after any save/reset)
$options = get_option('wcc_options', $default_options);

// Merge with defaults to ensure all keys exist
$options = wp_parse_args($options, $default_options);
?>

<div class="wrap wcc-admin-wrap">
    <h1>
        <?php _e('Website Cost Calculator Settings', 'website-cost-calculator'); ?>
        <span class="wcc-version">v<?php echo WCC_VERSION; ?></span>
    </h1>
    
    <?php if ($settings_saved): ?>
    <div class="notice notice-success is-dismissible">
        <p><strong><?php _e('Settings saved successfully!', 'website-cost-calculator'); ?></strong></p>
    </div>
    <?php endif; ?>
    
    <?php if ($settings_reset): ?>
    <div class="notice notice-info is-dismissible">
        <p><strong><?php _e('Settings have been reset to defaults.', 'website-cost-calculator'); ?></strong></p>
    </div>
    <?php endif; ?>
    
    <div class="wcc-admin-header">
        <p>
            <?php _e('Configure your website cost calculator. Use the shortcode', 'website-cost-calculator'); ?> 
            <code>[website_cost_calculator]</code> 
            <?php _e('to display the calculator on any page or post.', 'website-cost-calculator'); ?>
        </p>
        <p class="wcc-author-info">
            <?php _e('Developed by', 'website-cost-calculator'); ?> 
            <a href="https://ezyontech.com" target="_blank"><strong>Haseeb Ur Rehman Mughal</strong></a> | 
            <a href="https://ezyontech.com" target="_blank">ezyontech.com</a>
        </p>
    </div>
    
    <form method="post" action="" class="wcc-settings-form" id="wcc-settings-form">
        <?php wp_nonce_field('wcc_save_settings', 'wcc_settings_nonce'); ?>
        
        <div class="wcc-admin-tabs">
            <nav class="nav-tab-wrapper">
                <a href="#general" class="nav-tab nav-tab-active" data-tab="general"><?php _e('General', 'website-cost-calculator'); ?></a>
                <a href="#pricing" class="nav-tab" data-tab="pricing"><?php _e('Pricing Options', 'website-cost-calculator'); ?></a>
                <a href="#features" class="nav-tab" data-tab="features"><?php _e('Features', 'website-cost-calculator'); ?></a>
                <a href="#styling" class="nav-tab" data-tab="styling"><?php _e('Styling', 'website-cost-calculator'); ?></a>
                <a href="#form" class="nav-tab" data-tab="form"><?php _e('Contact Form', 'website-cost-calculator'); ?></a>
            </nav>
            
            <!-- General Tab -->
            <div id="general" class="wcc-tab-content active">
                <h2><?php _e('General Settings', 'website-cost-calculator'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="currency_symbol"><?php _e('Currency Symbol', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr($options['currency_symbol'] ?? '$'); ?>" class="regular-text" style="width: 80px;">
                            <p class="description"><?php _e('Enter your currency symbol (e.g., $, €, £, ₹)', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="currency_position"><?php _e('Currency Position', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <select id="currency_position" name="currency_position">
                                <option value="before" <?php selected($options['currency_position'] ?? 'before', 'before'); ?>><?php _e('Before amount ($100)', 'website-cost-calculator'); ?></option>
                                <option value="after" <?php selected($options['currency_position'] ?? 'before', 'after'); ?>><?php _e('After amount (100$)', 'website-cost-calculator'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Pricing Options Tab -->
            <div id="pricing" class="wcc-tab-content">
                <h2><?php _e('Website Types', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Configure the base prices for different website types.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="website-types-repeater">
                    <?php 
                    $website_types = $options['website_types'] ?? $default_options['website_types'];
                    foreach ($website_types as $index => $type): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="website_types[<?php echo $index; ?>][name]" value="<?php echo esc_attr($type['name']); ?>" placeholder="<?php _e('Type Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="website_types[<?php echo $index; ?>][price]" value="<?php echo esc_attr($type['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="website-types-repeater" data-name="website_types"><?php _e('Add Website Type', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Page Ranges', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Additional pricing based on number of pages.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="page-ranges-repeater">
                    <?php 
                    $page_ranges = $options['page_ranges'] ?? $default_options['page_ranges'];
                    foreach ($page_ranges as $index => $range): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="page_ranges[<?php echo $index; ?>][name]" value="<?php echo esc_attr($range['name']); ?>" placeholder="<?php _e('Range Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="page_ranges[<?php echo $index; ?>][price]" value="<?php echo esc_attr($range['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="page-ranges-repeater" data-name="page_ranges"><?php _e('Add Page Range', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Design Options', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Pricing for different design approaches.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="design-options-repeater">
                    <?php 
                    $design_options = $options['design_options'] ?? $default_options['design_options'];
                    foreach ($design_options as $index => $opt): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="design_options[<?php echo $index; ?>][name]" value="<?php echo esc_attr($opt['name']); ?>" placeholder="<?php _e('Option Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="design_options[<?php echo $index; ?>][price]" value="<?php echo esc_attr($opt['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="design-options-repeater" data-name="design_options"><?php _e('Add Design Option', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Timeline Options', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Rush pricing multipliers. Standard timeline should have a multiplier of 1. Use 0.95 for 5% discount, 1.25 for 25% rush fee, etc.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="timeline-options-repeater">
                    <?php 
                    $timeline_options = $options['timeline_options'] ?? $default_options['timeline_options'];
                    foreach ($timeline_options as $index => $timeline): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="timeline_options[<?php echo $index; ?>][name]" value="<?php echo esc_attr($timeline['name']); ?>" placeholder="<?php _e('Timeline Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="timeline_options[<?php echo $index; ?>][multiplier]" value="<?php echo esc_attr($timeline['multiplier']); ?>" placeholder="<?php _e('Multiplier', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="timeline-options-repeater" data-name="timeline_options" data-field="multiplier"><?php _e('Add Timeline Option', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Hosting Options', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Monthly hosting options.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="hosting-options-repeater">
                    <?php 
                    $hosting_options = $options['hosting_options'] ?? $default_options['hosting_options'];
                    foreach ($hosting_options as $index => $hosting): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="hosting_options[<?php echo $index; ?>][name]" value="<?php echo esc_attr($hosting['name']); ?>" placeholder="<?php _e('Hosting Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="hosting_options[<?php echo $index; ?>][monthly]" value="<?php echo esc_attr($hosting['monthly'] ?? 0); ?>" placeholder="<?php _e('Monthly Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="hosting-options-repeater" data-name="hosting_options" data-field="monthly"><?php _e('Add Hosting Option', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Maintenance Plans', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Monthly maintenance plan options.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="maintenance-plans-repeater">
                    <?php 
                    $maintenance_plans = $options['maintenance_plans'] ?? $default_options['maintenance_plans'];
                    foreach ($maintenance_plans as $index => $plan): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="maintenance_plans[<?php echo $index; ?>][name]" value="<?php echo esc_attr($plan['name']); ?>" placeholder="<?php _e('Plan Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="maintenance_plans[<?php echo $index; ?>][price]" value="<?php echo esc_attr($plan['price']); ?>" placeholder="<?php _e('Monthly Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="maintenance-plans-repeater" data-name="maintenance_plans"><?php _e('Add Maintenance Plan', 'website-cost-calculator'); ?></button>
            </div>
            
            <!-- Features Tab -->
            <div id="features" class="wcc-tab-content">
                <h2><?php _e('Website Features', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Add-on features that users can select.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="features-repeater">
                    <?php 
                    $features = $options['features'] ?? $default_options['features'];
                    foreach ($features as $index => $feature): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="features[<?php echo $index; ?>][name]" value="<?php echo esc_attr($feature['name']); ?>" placeholder="<?php _e('Feature Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="features[<?php echo $index; ?>][price]" value="<?php echo esc_attr($feature['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="features-repeater" data-name="features"><?php _e('Add Feature', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('E-commerce Features', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Features specific to e-commerce websites.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="ecommerce-features-repeater">
                    <?php 
                    $ecommerce_features = $options['ecommerce_features'] ?? $default_options['ecommerce_features'];
                    foreach ($ecommerce_features as $index => $feature): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="ecommerce_features[<?php echo $index; ?>][name]" value="<?php echo esc_attr($feature['name']); ?>" placeholder="<?php _e('Feature Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="ecommerce_features[<?php echo $index; ?>][price]" value="<?php echo esc_attr($feature['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="ecommerce-features-repeater" data-name="ecommerce_features"><?php _e('Add E-commerce Feature', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('SEO & Marketing', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('SEO and marketing add-ons.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="seo-marketing-repeater">
                    <?php 
                    $seo_marketing = $options['seo_marketing'] ?? $default_options['seo_marketing'];
                    foreach ($seo_marketing as $index => $item): 
                    ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="seo_marketing[<?php echo $index; ?>][name]" value="<?php echo esc_attr($item['name']); ?>" placeholder="<?php _e('Item Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="seo_marketing[<?php echo $index; ?>][price]" value="<?php echo esc_attr($item['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="0">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="seo-marketing-repeater" data-name="seo_marketing"><?php _e('Add SEO/Marketing Item', 'website-cost-calculator'); ?></button>
            </div>
            
            <!-- Styling Tab -->
            <div id="styling" class="wcc-tab-content">
                <h2><?php _e('Calculator Styling', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Customize the appearance of your calculator.', 'website-cost-calculator'); ?></p>
                
                <?php $styling = $options['styling'] ?? $default_options['styling']; ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="primary_color"><?php _e('Primary Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="primary_color" name="primary_color" value="<?php echo esc_attr($styling['primary_color'] ?? '#6366F1'); ?>">
                            <p class="description"><?php _e('Main buttons and active elements', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="secondary_color"><?php _e('Secondary Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="secondary_color" name="secondary_color" value="<?php echo esc_attr($styling['secondary_color'] ?? '#10B981'); ?>">
                            <p class="description"><?php _e('Success states and highlights', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="accent_color"><?php _e('Accent Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="accent_color" name="accent_color" value="<?php echo esc_attr($styling['accent_color'] ?? '#F59E0B'); ?>">
                            <p class="description"><?php _e('Price displays and important info', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="text_color"><?php _e('Text Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="text_color" name="text_color" value="<?php echo esc_attr($styling['text_color'] ?? '#1F2937'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="background_color"><?php _e('Background Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="background_color" name="background_color" value="<?php echo esc_attr($styling['background_color'] ?? '#F8FAFC'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gradient_start"><?php _e('Header Gradient Start', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="gradient_start" name="gradient_start" value="<?php echo esc_attr($styling['gradient_start'] ?? '#6366F1'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gradient_end"><?php _e('Header Gradient End', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="gradient_end" name="gradient_end" value="<?php echo esc_attr($styling['gradient_end'] ?? '#8B5CF6'); ?>">
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Contact Form Tab -->
            <div id="form" class="wcc-tab-content">
                <h2><?php _e('Contact Form Settings', 'website-cost-calculator'); ?></h2>
                
                <?php $form_settings = $options['form_settings'] ?? $default_options['form_settings']; ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Show Contact Form', 'website-cost-calculator'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_contact_form" value="1" <?php checked($form_settings['show_contact_form'] ?? true); ?>>
                                <?php _e('Display contact form to collect user information', 'website-cost-calculator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Require Contact Info', 'website-cost-calculator'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="require_contact" value="1" <?php checked($form_settings['require_contact'] ?? false); ?>>
                                <?php _e('Require users to fill contact form before seeing final quote', 'website-cost-calculator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enable PDF Download', 'website-cost-calculator'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_pdf" value="1" <?php checked($form_settings['enable_pdf'] ?? true); ?>>
                                <?php _e('Allow users to download PDF quotes', 'website-cost-calculator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enable Save Quote', 'website-cost-calculator'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_save_quote" value="1" <?php checked($form_settings['enable_save_quote'] ?? true); ?>>
                                <?php _e('Allow users to save and share quotes', 'website-cost-calculator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="email_recipient"><?php _e('Email Recipient', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="email" id="email_recipient" name="email_recipient" value="<?php echo esc_attr($form_settings['email_recipient'] ?? get_option('admin_email')); ?>" class="regular-text">
                            <p class="description"><?php _e('Email address to receive quote requests', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="success_message"><?php _e('Success Message', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <textarea id="success_message" name="success_message" rows="3" class="large-text"><?php echo esc_textarea($form_settings['success_message'] ?? 'Thank you! Your personalized quote has been submitted. We\'ll contact you within 24 hours with a detailed proposal.'); ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <p class="submit">
            <input type="submit" name="wcc_save_settings" class="button button-primary button-large" value="<?php _e('Save Settings', 'website-cost-calculator'); ?>">
            <input type="submit" name="wcc_reset_defaults" class="button button-secondary" value="<?php _e('Reset to Defaults', 'website-cost-calculator'); ?>" onclick="return confirm('<?php _e('Are you sure you want to reset all settings to defaults? This cannot be undone.', 'website-cost-calculator'); ?>');">
        </p>
    </form>
    
    <div class="wcc-admin-footer">
        <p>
            <strong><?php _e('Website Cost Calculator Pro', 'website-cost-calculator'); ?></strong> v<?php echo WCC_VERSION; ?> | 
            <?php _e('Developed by', 'website-cost-calculator'); ?> <a href="https://ezyontech.com" target="_blank">Haseeb Ur Rehman Mughal</a> | 
            <a href="https://ezyontech.com" target="_blank">ezyontech.com</a>
        </p>
    </div>
</div>

<style>
.wcc-version {
    font-size: 12px;
    background: #6366F1;
    color: #fff;
    padding: 3px 8px;
    border-radius: 4px;
    margin-left: 10px;
    vertical-align: middle;
}
.wcc-author-info {
    margin-top: 10px;
    font-size: 13px;
    color: #666;
}
.wcc-author-info a {
    color: #6366F1;
    text-decoration: none;
}
.wcc-admin-footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    color: #666;
    font-size: 13px;
}
.wcc-admin-footer a {
    color: #6366F1;
    text-decoration: none;
}
</style>
