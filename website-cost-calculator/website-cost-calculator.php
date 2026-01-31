<?php
/**
 * Plugin Name: Website Cost Calculator
 * Plugin URI: https://example.com/website-cost-calculator
 * Description: A comprehensive website cost calculator tool that helps clients estimate the cost of their website project based on various factors.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: website-cost-calculator
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WCC_VERSION', '1.0.0');
define('WCC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Website Cost Calculator Class
 */
class Website_Cost_Calculator {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Default calculator options
     */
    private $default_options = array();
    
    /**
     * Get the singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->set_default_options();
        $this->init_hooks();
    }
    
    /**
     * Set default options for the calculator
     */
    private function set_default_options() {
        $this->default_options = array(
            'currency_symbol' => '$',
            'currency_position' => 'before',
            'website_types' => array(
                array('name' => 'Basic Blog/Portfolio', 'price' => 500),
                array('name' => 'Business Website', 'price' => 1500),
                array('name' => 'E-commerce Store', 'price' => 3000),
                array('name' => 'Custom Web Application', 'price' => 5000),
                array('name' => 'Enterprise Solution', 'price' => 10000),
            ),
            'page_ranges' => array(
                array('name' => '1-5 pages', 'price' => 0),
                array('name' => '6-10 pages', 'price' => 300),
                array('name' => '11-20 pages', 'price' => 600),
                array('name' => '21-50 pages', 'price' => 1200),
                array('name' => '50+ pages', 'price' => 2000),
            ),
            'design_options' => array(
                array('name' => 'Template Based', 'price' => 0),
                array('name' => 'Semi-Custom Design', 'price' => 800),
                array('name' => 'Fully Custom Design', 'price' => 2000),
            ),
            'features' => array(
                array('name' => 'Contact Form', 'price' => 50),
                array('name' => 'Blog/News Section', 'price' => 200),
                array('name' => 'Photo Gallery', 'price' => 150),
                array('name' => 'Video Integration', 'price' => 100),
                array('name' => 'Social Media Integration', 'price' => 100),
                array('name' => 'Newsletter/Email Signup', 'price' => 150),
                array('name' => 'Search Functionality', 'price' => 200),
                array('name' => 'Multi-language Support', 'price' => 500),
                array('name' => 'Member Login Area', 'price' => 400),
                array('name' => 'Live Chat Integration', 'price' => 150),
            ),
            'ecommerce_features' => array(
                array('name' => 'Product Catalog (up to 50 products)', 'price' => 300),
                array('name' => 'Product Catalog (50-200 products)', 'price' => 600),
                array('name' => 'Product Catalog (200+ products)', 'price' => 1000),
                array('name' => 'Payment Gateway Integration', 'price' => 250),
                array('name' => 'Inventory Management', 'price' => 300),
                array('name' => 'Shipping Calculator', 'price' => 200),
                array('name' => 'Discount/Coupon System', 'price' => 150),
                array('name' => 'Product Reviews', 'price' => 100),
            ),
            'seo_marketing' => array(
                array('name' => 'Basic SEO Setup', 'price' => 200),
                array('name' => 'Advanced SEO Package', 'price' => 500),
                array('name' => 'Google Analytics Setup', 'price' => 100),
                array('name' => 'Speed Optimization', 'price' => 300),
            ),
            'maintenance_plans' => array(
                array('name' => 'No Maintenance', 'price' => 0),
                array('name' => 'Basic Maintenance (Monthly)', 'price' => 50),
                array('name' => 'Standard Maintenance (Monthly)', 'price' => 150),
                array('name' => 'Premium Maintenance (Monthly)', 'price' => 300),
            ),
            'timeline_options' => array(
                array('name' => 'Standard (4-6 weeks)', 'multiplier' => 1),
                array('name' => 'Rush (2-3 weeks)', 'multiplier' => 1.25),
                array('name' => 'Urgent (1-2 weeks)', 'multiplier' => 1.5),
            ),
            'form_settings' => array(
                'show_contact_form' => true,
                'require_contact' => false,
                'email_recipient' => get_option('admin_email'),
                'success_message' => 'Thank you! We will contact you shortly with a detailed quote.',
            ),
            'styling' => array(
                'primary_color' => '#4F46E5',
                'secondary_color' => '#10B981',
                'accent_color' => '#F59E0B',
                'text_color' => '#1F2937',
                'background_color' => '#F9FAFB',
            ),
        );
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        
        // Shortcode
        add_shortcode('website_cost_calculator', array($this, 'render_calculator'));
        
        // AJAX handlers
        add_action('wp_ajax_wcc_submit_quote', array($this, 'handle_quote_submission'));
        add_action('wp_ajax_nopriv_wcc_submit_quote', array($this, 'handle_quote_submission'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options if not exists
        if (!get_option('wcc_options')) {
            update_option('wcc_options', $this->default_options);
        }
        
        // Create database table for storing quotes
        $this->create_quotes_table();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create quotes table
     */
    private function create_quotes_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcc_quotes';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(50),
            company varchar(100),
            selections longtext NOT NULL,
            total_cost decimal(10,2) NOT NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'pending',
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Website Cost Calculator', 'website-cost-calculator'),
            __('Cost Calculator', 'website-cost-calculator'),
            'manage_options',
            'website-cost-calculator',
            array($this, 'render_admin_page'),
            'dashicons-calculator',
            30
        );
        
        add_submenu_page(
            'website-cost-calculator',
            __('Settings', 'website-cost-calculator'),
            __('Settings', 'website-cost-calculator'),
            'manage_options',
            'website-cost-calculator',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'website-cost-calculator',
            __('Quote Requests', 'website-cost-calculator'),
            __('Quote Requests', 'website-cost-calculator'),
            'manage_options',
            'wcc-quotes',
            array($this, 'render_quotes_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('wcc_options_group', 'wcc_options', array($this, 'sanitize_options'));
    }
    
    /**
     * Sanitize options
     */
    public function sanitize_options($input) {
        // Sanitize all input data
        if (isset($input['currency_symbol'])) {
            $input['currency_symbol'] = sanitize_text_field($input['currency_symbol']);
        }
        
        // Add more sanitization as needed
        return $input;
    }
    
    /**
     * Admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'website-cost-calculator') === false && strpos($hook, 'wcc-quotes') === false) {
            return;
        }
        
        wp_enqueue_style('wcc-admin-style', WCC_PLUGIN_URL . 'assets/css/admin-style.css', array(), WCC_VERSION);
        wp_enqueue_script('wcc-admin-script', WCC_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), WCC_VERSION, true);
        
        wp_localize_script('wcc-admin-script', 'wccAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcc_admin_nonce'),
        ));
    }
    
    /**
     * Frontend scripts and styles
     */
    public function frontend_enqueue_scripts() {
        wp_enqueue_style('wcc-frontend-style', WCC_PLUGIN_URL . 'assets/css/frontend-style.css', array(), WCC_VERSION);
        wp_enqueue_script('wcc-frontend-script', WCC_PLUGIN_URL . 'assets/js/frontend-script.js', array('jquery'), WCC_VERSION, true);
        
        $options = get_option('wcc_options', $this->default_options);
        
        wp_localize_script('wcc-frontend-script', 'wccFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcc_frontend_nonce'),
            'options' => $options,
            'currencySymbol' => $options['currency_symbol'] ?? '$',
            'currencyPosition' => $options['currency_position'] ?? 'before',
        ));
        
        // Add custom CSS variables for styling
        $custom_css = $this->generate_custom_css($options);
        wp_add_inline_style('wcc-frontend-style', $custom_css);
    }
    
    /**
     * Generate custom CSS from options
     */
    private function generate_custom_css($options) {
        $styling = $options['styling'] ?? $this->default_options['styling'];
        
        return "
            :root {
                --wcc-primary: {$styling['primary_color']};
                --wcc-secondary: {$styling['secondary_color']};
                --wcc-accent: {$styling['accent_color']};
                --wcc-text: {$styling['text_color']};
                --wcc-background: {$styling['background_color']};
            }
        ";
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include WCC_PLUGIN_DIR . 'admin/admin-page.php';
    }
    
    /**
     * Render quotes page
     */
    public function render_quotes_page() {
        include WCC_PLUGIN_DIR . 'admin/quotes-page.php';
    }
    
    /**
     * Render calculator shortcode
     */
    public function render_calculator($atts) {
        $atts = shortcode_atts(array(
            'theme' => 'default',
            'compact' => 'false',
        ), $atts, 'website_cost_calculator');
        
        ob_start();
        include WCC_PLUGIN_DIR . 'templates/calculator.php';
        return ob_get_clean();
    }
    
    /**
     * Get plugin options
     */
    public function get_options() {
        return get_option('wcc_options', $this->default_options);
    }
    
    /**
     * Get default options
     */
    public function get_default_options() {
        return $this->default_options;
    }
    
    /**
     * Get icon for website type
     */
    public function get_website_type_icon($type_name) {
        $icons = array(
            'blog' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>',
            'portfolio' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z"/></svg>',
            'business' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>',
            'ecommerce' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>',
            'store' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>',
            'custom' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>',
            'application' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>',
            'enterprise' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z"/></svg>',
        );
        
        $type_lower = strtolower($type_name);
        foreach ($icons as $key => $icon) {
            if (strpos($type_lower, $key) !== false) {
                return $icon;
            }
        }
        
        // Default icon
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';
    }
    
    /**
     * Handle quote submission via AJAX
     */
    public function handle_quote_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wcc_frontend_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
        }
        
        // Sanitize input
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $company = sanitize_text_field($_POST['company'] ?? '');
        $selections = sanitize_text_field($_POST['selections'] ?? '');
        $total_cost = floatval($_POST['total_cost'] ?? 0);
        $notes = sanitize_textarea_field($_POST['notes'] ?? '');
        
        // Validate required fields
        if (empty($name) || empty($email)) {
            wp_send_json_error(array('message' => 'Name and email are required.'));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        }
        
        // Save to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcc_quotes';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'company' => $company,
                'selections' => $selections,
                'total_cost' => $total_cost,
                'notes' => $notes,
                'status' => 'pending',
            ),
            array('%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to save quote request.'));
        }
        
        // Send email notification
        $this->send_quote_notification($name, $email, $phone, $company, $selections, $total_cost, $notes);
        
        $options = $this->get_options();
        $success_message = $options['form_settings']['success_message'] ?? 'Thank you! We will contact you shortly with a detailed quote.';
        
        wp_send_json_success(array('message' => $success_message));
    }
    
    /**
     * Send quote notification email
     */
    private function send_quote_notification($name, $email, $phone, $company, $selections, $total_cost, $notes) {
        $options = $this->get_options();
        $to = $options['form_settings']['email_recipient'] ?? get_option('admin_email');
        $currency = $options['currency_symbol'] ?? '$';
        
        $subject = sprintf(__('New Website Quote Request from %s', 'website-cost-calculator'), $name);
        
        $message = sprintf(
            __("A new website quote request has been submitted.\n\n" .
            "Name: %s\n" .
            "Email: %s\n" .
            "Phone: %s\n" .
            "Company: %s\n\n" .
            "Selections:\n%s\n\n" .
            "Estimated Total: %s%s\n\n" .
            "Additional Notes:\n%s", 'website-cost-calculator'),
            $name,
            $email,
            $phone,
            $company,
            $selections,
            $currency,
            number_format($total_cost, 2),
            $notes
        );
        
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'Reply-To: ' . $name . ' <' . $email . '>',
        );
        
        wp_mail($to, $subject, $message, $headers);
        
        // Send confirmation to customer
        $customer_subject = __('Your Website Quote Request', 'website-cost-calculator');
        $customer_message = sprintf(
            __("Thank you for your interest in our web development services!\n\n" .
            "We have received your quote request with an estimated total of %s%s.\n\n" .
            "Our team will review your requirements and get back to you within 24-48 hours with a detailed proposal.\n\n" .
            "Best regards,\nThe Web Development Team", 'website-cost-calculator'),
            $currency,
            number_format($total_cost, 2)
        );
        
        wp_mail($email, $customer_subject, $customer_message);
    }
}

// Initialize the plugin
function wcc_init() {
    return Website_Cost_Calculator::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'wcc_init');
