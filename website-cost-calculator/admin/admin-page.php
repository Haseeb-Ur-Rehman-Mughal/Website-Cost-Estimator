<?php
/**
 * Admin Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$wcc = Website_Cost_Calculator::get_instance();
$options = $wcc->get_options();
$default_options = $wcc->get_default_options();

// Handle form submission
if (isset($_POST['wcc_save_settings']) && wp_verify_nonce($_POST['wcc_settings_nonce'], 'wcc_save_settings')) {
    // Currency settings
    $options['currency_symbol'] = sanitize_text_field($_POST['currency_symbol'] ?? '$');
    $options['currency_position'] = sanitize_text_field($_POST['currency_position'] ?? 'before');
    
    // Styling settings
    $options['styling'] = array(
        'primary_color' => sanitize_hex_color($_POST['primary_color'] ?? '#4F46E5'),
        'secondary_color' => sanitize_hex_color($_POST['secondary_color'] ?? '#10B981'),
        'accent_color' => sanitize_hex_color($_POST['accent_color'] ?? '#F59E0B'),
        'text_color' => sanitize_hex_color($_POST['text_color'] ?? '#1F2937'),
        'background_color' => sanitize_hex_color($_POST['background_color'] ?? '#F9FAFB'),
    );
    
    // Form settings
    $options['form_settings'] = array(
        'show_contact_form' => isset($_POST['show_contact_form']),
        'require_contact' => isset($_POST['require_contact']),
        'email_recipient' => sanitize_email($_POST['email_recipient'] ?? get_option('admin_email')),
        'success_message' => sanitize_textarea_field($_POST['success_message'] ?? ''),
    );
    
    // Website types
    if (isset($_POST['website_types'])) {
        $website_types = array();
        foreach ($_POST['website_types'] as $type) {
            if (!empty($type['name'])) {
                $website_types[] = array(
                    'name' => sanitize_text_field($type['name']),
                    'price' => floatval($type['price']),
                );
            }
        }
        $options['website_types'] = $website_types;
    }
    
    // Page ranges
    if (isset($_POST['page_ranges'])) {
        $page_ranges = array();
        foreach ($_POST['page_ranges'] as $range) {
            if (!empty($range['name'])) {
                $page_ranges[] = array(
                    'name' => sanitize_text_field($range['name']),
                    'price' => floatval($range['price']),
                );
            }
        }
        $options['page_ranges'] = $page_ranges;
    }
    
    // Design options
    if (isset($_POST['design_options'])) {
        $design_options = array();
        foreach ($_POST['design_options'] as $opt) {
            if (!empty($opt['name'])) {
                $design_options[] = array(
                    'name' => sanitize_text_field($opt['name']),
                    'price' => floatval($opt['price']),
                );
            }
        }
        $options['design_options'] = $design_options;
    }
    
    // Features
    if (isset($_POST['features'])) {
        $features = array();
        foreach ($_POST['features'] as $feature) {
            if (!empty($feature['name'])) {
                $features[] = array(
                    'name' => sanitize_text_field($feature['name']),
                    'price' => floatval($feature['price']),
                );
            }
        }
        $options['features'] = $features;
    }
    
    // E-commerce features
    if (isset($_POST['ecommerce_features'])) {
        $ecommerce_features = array();
        foreach ($_POST['ecommerce_features'] as $feature) {
            if (!empty($feature['name'])) {
                $ecommerce_features[] = array(
                    'name' => sanitize_text_field($feature['name']),
                    'price' => floatval($feature['price']),
                );
            }
        }
        $options['ecommerce_features'] = $ecommerce_features;
    }
    
    // SEO & Marketing
    if (isset($_POST['seo_marketing'])) {
        $seo_marketing = array();
        foreach ($_POST['seo_marketing'] as $item) {
            if (!empty($item['name'])) {
                $seo_marketing[] = array(
                    'name' => sanitize_text_field($item['name']),
                    'price' => floatval($item['price']),
                );
            }
        }
        $options['seo_marketing'] = $seo_marketing;
    }
    
    // Maintenance plans
    if (isset($_POST['maintenance_plans'])) {
        $maintenance_plans = array();
        foreach ($_POST['maintenance_plans'] as $plan) {
            if (!empty($plan['name'])) {
                $maintenance_plans[] = array(
                    'name' => sanitize_text_field($plan['name']),
                    'price' => floatval($plan['price']),
                );
            }
        }
        $options['maintenance_plans'] = $maintenance_plans;
    }
    
    // Timeline options
    if (isset($_POST['timeline_options'])) {
        $timeline_options = array();
        foreach ($_POST['timeline_options'] as $timeline) {
            if (!empty($timeline['name'])) {
                $timeline_options[] = array(
                    'name' => sanitize_text_field($timeline['name']),
                    'multiplier' => floatval($timeline['multiplier']),
                );
            }
        }
        $options['timeline_options'] = $timeline_options;
    }
    
    update_option('wcc_options', $options);
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully!', 'website-cost-calculator') . '</p></div>';
}

// Handle reset to defaults
if (isset($_POST['wcc_reset_defaults']) && wp_verify_nonce($_POST['wcc_settings_nonce'], 'wcc_save_settings')) {
    update_option('wcc_options', $default_options);
    $options = $default_options;
    echo '<div class="notice notice-info is-dismissible"><p>' . __('Settings reset to defaults.', 'website-cost-calculator') . '</p></div>';
}
?>

<div class="wrap wcc-admin-wrap">
    <h1><?php _e('Website Cost Calculator Settings', 'website-cost-calculator'); ?></h1>
    
    <div class="wcc-admin-header">
        <p><?php _e('Configure your website cost calculator. Use the shortcode', 'website-cost-calculator'); ?> <code>[website_cost_calculator]</code> <?php _e('to display the calculator on any page or post.', 'website-cost-calculator'); ?></p>
    </div>
    
    <form method="post" action="" class="wcc-settings-form">
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
                            <input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr($options['currency_symbol'] ?? '$'); ?>" class="regular-text">
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
                    <?php foreach ($options['website_types'] as $index => $type): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="website_types[<?php echo $index; ?>][name]" value="<?php echo esc_attr($type['name']); ?>" placeholder="<?php _e('Type Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="website_types[<?php echo $index; ?>][price]" value="<?php echo esc_attr($type['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="website-types-repeater" data-name="website_types"><?php _e('Add Website Type', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Page Ranges', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Additional pricing based on number of pages.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="page-ranges-repeater">
                    <?php foreach ($options['page_ranges'] as $index => $range): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="page_ranges[<?php echo $index; ?>][name]" value="<?php echo esc_attr($range['name']); ?>" placeholder="<?php _e('Range Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="page_ranges[<?php echo $index; ?>][price]" value="<?php echo esc_attr($range['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="page-ranges-repeater" data-name="page_ranges"><?php _e('Add Page Range', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Design Options', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Pricing for different design approaches.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="design-options-repeater">
                    <?php foreach ($options['design_options'] as $index => $opt): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="design_options[<?php echo $index; ?>][name]" value="<?php echo esc_attr($opt['name']); ?>" placeholder="<?php _e('Option Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="design_options[<?php echo $index; ?>][price]" value="<?php echo esc_attr($opt['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="design-options-repeater" data-name="design_options"><?php _e('Add Design Option', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Timeline Options', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Rush pricing multipliers. Standard timeline should have a multiplier of 1.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="timeline-options-repeater">
                    <?php foreach ($options['timeline_options'] as $index => $timeline): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="timeline_options[<?php echo $index; ?>][name]" value="<?php echo esc_attr($timeline['name']); ?>" placeholder="<?php _e('Timeline Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="timeline_options[<?php echo $index; ?>][multiplier]" value="<?php echo esc_attr($timeline['multiplier']); ?>" placeholder="<?php _e('Multiplier', 'website-cost-calculator'); ?>" class="small-text" step="0.01" min="1">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="timeline-options-repeater" data-name="timeline_options" data-field="multiplier"><?php _e('Add Timeline Option', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('Maintenance Plans', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Monthly maintenance plan options.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="maintenance-plans-repeater">
                    <?php foreach ($options['maintenance_plans'] as $index => $plan): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="maintenance_plans[<?php echo $index; ?>][name]" value="<?php echo esc_attr($plan['name']); ?>" placeholder="<?php _e('Plan Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="maintenance_plans[<?php echo $index; ?>][price]" value="<?php echo esc_attr($plan['price']); ?>" placeholder="<?php _e('Monthly Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
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
                    <?php foreach ($options['features'] as $index => $feature): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="features[<?php echo $index; ?>][name]" value="<?php echo esc_attr($feature['name']); ?>" placeholder="<?php _e('Feature Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="features[<?php echo $index; ?>][price]" value="<?php echo esc_attr($feature['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="features-repeater" data-name="features"><?php _e('Add Feature', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('E-commerce Features', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('Features specific to e-commerce websites.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="ecommerce-features-repeater">
                    <?php foreach ($options['ecommerce_features'] as $index => $feature): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="ecommerce_features[<?php echo $index; ?>][name]" value="<?php echo esc_attr($feature['name']); ?>" placeholder="<?php _e('Feature Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="ecommerce_features[<?php echo $index; ?>][price]" value="<?php echo esc_attr($feature['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
                        <button type="button" class="button wcc-remove-item"><?php _e('Remove', 'website-cost-calculator'); ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button wcc-add-item" data-target="ecommerce-features-repeater" data-name="ecommerce_features"><?php _e('Add E-commerce Feature', 'website-cost-calculator'); ?></button>
                
                <hr>
                
                <h2><?php _e('SEO & Marketing', 'website-cost-calculator'); ?></h2>
                <p class="description"><?php _e('SEO and marketing add-ons.', 'website-cost-calculator'); ?></p>
                
                <div class="wcc-repeater" id="seo-marketing-repeater">
                    <?php foreach ($options['seo_marketing'] as $index => $item): ?>
                    <div class="wcc-repeater-item">
                        <input type="text" name="seo_marketing[<?php echo $index; ?>][name]" value="<?php echo esc_attr($item['name']); ?>" placeholder="<?php _e('Item Name', 'website-cost-calculator'); ?>" class="regular-text">
                        <input type="number" name="seo_marketing[<?php echo $index; ?>][price]" value="<?php echo esc_attr($item['price']); ?>" placeholder="<?php _e('Price', 'website-cost-calculator'); ?>" class="small-text" step="0.01">
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
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="primary_color"><?php _e('Primary Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="primary_color" name="primary_color" value="<?php echo esc_attr($options['styling']['primary_color'] ?? '#4F46E5'); ?>">
                            <p class="description"><?php _e('Main buttons and active elements', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="secondary_color"><?php _e('Secondary Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="secondary_color" name="secondary_color" value="<?php echo esc_attr($options['styling']['secondary_color'] ?? '#10B981'); ?>">
                            <p class="description"><?php _e('Success states and highlights', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="accent_color"><?php _e('Accent Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="accent_color" name="accent_color" value="<?php echo esc_attr($options['styling']['accent_color'] ?? '#F59E0B'); ?>">
                            <p class="description"><?php _e('Price displays and important info', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="text_color"><?php _e('Text Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="text_color" name="text_color" value="<?php echo esc_attr($options['styling']['text_color'] ?? '#1F2937'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="background_color"><?php _e('Background Color', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="color" id="background_color" name="background_color" value="<?php echo esc_attr($options['styling']['background_color'] ?? '#F9FAFB'); ?>">
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Contact Form Tab -->
            <div id="form" class="wcc-tab-content">
                <h2><?php _e('Contact Form Settings', 'website-cost-calculator'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Show Contact Form', 'website-cost-calculator'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_contact_form" value="1" <?php checked($options['form_settings']['show_contact_form'] ?? true); ?>>
                                <?php _e('Display contact form to collect user information', 'website-cost-calculator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Require Contact Info', 'website-cost-calculator'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="require_contact" value="1" <?php checked($options['form_settings']['require_contact'] ?? false); ?>>
                                <?php _e('Require users to fill contact form before seeing final quote', 'website-cost-calculator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="email_recipient"><?php _e('Email Recipient', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <input type="email" id="email_recipient" name="email_recipient" value="<?php echo esc_attr($options['form_settings']['email_recipient'] ?? get_option('admin_email')); ?>" class="regular-text">
                            <p class="description"><?php _e('Email address to receive quote requests', 'website-cost-calculator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="success_message"><?php _e('Success Message', 'website-cost-calculator'); ?></label></th>
                        <td>
                            <textarea id="success_message" name="success_message" rows="3" class="large-text"><?php echo esc_textarea($options['form_settings']['success_message'] ?? 'Thank you! We will contact you shortly with a detailed quote.'); ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <p class="submit">
            <input type="submit" name="wcc_save_settings" class="button button-primary" value="<?php _e('Save Settings', 'website-cost-calculator'); ?>">
            <input type="submit" name="wcc_reset_defaults" class="button" value="<?php _e('Reset to Defaults', 'website-cost-calculator'); ?>" onclick="return confirm('<?php _e('Are you sure you want to reset all settings to defaults?', 'website-cost-calculator'); ?>');">
        </p>
    </form>
</div>
