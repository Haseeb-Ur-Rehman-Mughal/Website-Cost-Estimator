<?php
/**
 * Plugin Name: Website Cost Calculator Pro
 * Plugin URI: https://ezyontech.com/website-cost-calculator
 * Description: The most comprehensive website cost calculator tool for WordPress. Features multi-step wizard, PDF quotes, industry presets, cost breakdown charts, and beautiful modern UI.
 * Version: 2.0.0
 * Author: Haseeb Ur Rehman Mughal
 * Author URI: https://ezyontech.com
 * License: Proprietary
 * Text Domain: website-cost-calculator
 * Domain Path: /languages
 * 
 * Copyright (C) 2024 Haseeb Ur Rehman Mughal / EzyOnTech
 * All rights reserved.
 * 
 * This plugin is proprietary software. Unauthorized copying, modification,
 * distribution, or use of this software is strictly prohibited.
 * 
 * For licensing inquiries: https://ezyontech.com
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WCC_VERSION', '2.0.0');
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
            'show_price_range' => true,
            'range_variance' => 20, // +/- percentage for price range
            
            // Industry Presets
            'industry_presets' => array(
                array(
                    'id' => 'restaurant',
                    'name' => 'Restaurant & Food',
                    'icon' => 'utensils',
                    'description' => 'Perfect for restaurants, cafes, and food businesses',
                    'suggested_type' => 1,
                    'suggested_features' => array(0, 2, 4, 9),
                    'base_modifier' => 1.0
                ),
                array(
                    'id' => 'ecommerce',
                    'name' => 'E-commerce & Retail',
                    'icon' => 'shopping-cart',
                    'description' => 'Online stores and retail businesses',
                    'suggested_type' => 2,
                    'suggested_features' => array(0, 4, 5),
                    'suggested_ecommerce' => array(0, 3, 4, 6, 7),
                    'base_modifier' => 1.0
                ),
                array(
                    'id' => 'professional',
                    'name' => 'Professional Services',
                    'icon' => 'briefcase',
                    'description' => 'Law firms, consultants, accountants',
                    'suggested_type' => 1,
                    'suggested_features' => array(0, 1, 4, 8),
                    'base_modifier' => 1.1
                ),
                array(
                    'id' => 'healthcare',
                    'name' => 'Healthcare & Medical',
                    'icon' => 'heartbeat',
                    'description' => 'Clinics, doctors, healthcare providers',
                    'suggested_type' => 1,
                    'suggested_features' => array(0, 4, 8, 9),
                    'base_modifier' => 1.2
                ),
                array(
                    'id' => 'realestate',
                    'name' => 'Real Estate',
                    'icon' => 'home',
                    'description' => 'Realtors, property listings, agencies',
                    'suggested_type' => 1,
                    'suggested_features' => array(0, 2, 4, 6),
                    'base_modifier' => 1.15
                ),
                array(
                    'id' => 'startup',
                    'name' => 'Startup & Tech',
                    'icon' => 'rocket',
                    'description' => 'Tech startups and SaaS companies',
                    'suggested_type' => 3,
                    'suggested_features' => array(0, 1, 4, 5, 7, 8),
                    'base_modifier' => 1.3
                ),
                array(
                    'id' => 'nonprofit',
                    'name' => 'Non-Profit & Charity',
                    'icon' => 'heart',
                    'description' => 'Charities, NGOs, foundations',
                    'suggested_type' => 1,
                    'suggested_features' => array(0, 1, 4, 5),
                    'base_modifier' => 0.85
                ),
                array(
                    'id' => 'education',
                    'name' => 'Education & Training',
                    'icon' => 'graduation-cap',
                    'description' => 'Schools, courses, e-learning',
                    'suggested_type' => 3,
                    'suggested_features' => array(0, 1, 4, 7, 8),
                    'base_modifier' => 1.2
                ),
                array(
                    'id' => 'portfolio',
                    'name' => 'Portfolio & Creative',
                    'icon' => 'palette',
                    'description' => 'Artists, photographers, designers',
                    'suggested_type' => 0,
                    'suggested_features' => array(0, 2, 3, 4),
                    'base_modifier' => 0.9
                ),
                array(
                    'id' => 'custom',
                    'name' => 'Custom / Other',
                    'icon' => 'cog',
                    'description' => 'Build your own from scratch',
                    'suggested_type' => null,
                    'base_modifier' => 1.0
                ),
            ),
            
            'website_types' => array(
                array('name' => 'Landing Page', 'price' => 300, 'description' => 'Single page with key information', 'icon' => 'file'),
                array('name' => 'Basic Website', 'price' => 800, 'description' => '3-5 pages, perfect for small businesses', 'icon' => 'globe'),
                array('name' => 'Business Website', 'price' => 2000, 'description' => 'Full-featured business presence', 'icon' => 'building'),
                array('name' => 'E-commerce Store', 'price' => 4000, 'description' => 'Complete online store solution', 'icon' => 'shopping-bag'),
                array('name' => 'Web Application', 'price' => 8000, 'description' => 'Custom functionality & features', 'icon' => 'code'),
                array('name' => 'Enterprise Platform', 'price' => 15000, 'description' => 'Large-scale custom solution', 'icon' => 'server'),
            ),
            
            'page_ranges' => array(
                array('name' => '1-3 pages', 'price' => 0, 'icon' => 'file'),
                array('name' => '4-7 pages', 'price' => 400, 'icon' => 'files'),
                array('name' => '8-15 pages', 'price' => 900, 'icon' => 'folder'),
                array('name' => '16-30 pages', 'price' => 1800, 'icon' => 'folders'),
                array('name' => '30+ pages', 'price' => 3000, 'icon' => 'archive'),
            ),
            
            'design_options' => array(
                array('name' => 'Template Based', 'price' => 0, 'description' => 'Pre-made design, quick setup', 'icon' => 'layout'),
                array('name' => 'Premium Template', 'price' => 500, 'description' => 'High-quality premium theme', 'icon' => 'star'),
                array('name' => 'Semi-Custom', 'price' => 1500, 'description' => 'Customized template design', 'icon' => 'edit'),
                array('name' => 'Fully Custom', 'price' => 3500, 'description' => 'Unique design from scratch', 'icon' => 'pen-tool'),
                array('name' => 'Premium Custom', 'price' => 6000, 'description' => 'Award-winning custom design', 'icon' => 'award'),
            ),
            
            'features' => array(
                array('name' => 'Contact Form', 'price' => 75, 'description' => 'Professional contact form with validation', 'icon' => 'mail', 'popular' => true),
                array('name' => 'Blog/News Section', 'price' => 250, 'description' => 'Full blog with categories & comments', 'icon' => 'edit-3', 'popular' => true),
                array('name' => 'Photo Gallery', 'price' => 200, 'description' => 'Beautiful image gallery with lightbox', 'icon' => 'image', 'popular' => false),
                array('name' => 'Video Integration', 'price' => 150, 'description' => 'Embed videos from YouTube/Vimeo', 'icon' => 'video', 'popular' => false),
                array('name' => 'Social Media Integration', 'price' => 150, 'description' => 'Connect all social platforms', 'icon' => 'share-2', 'popular' => true),
                array('name' => 'Newsletter Signup', 'price' => 200, 'description' => 'Email capture with integration', 'icon' => 'send', 'popular' => true),
                array('name' => 'Search Functionality', 'price' => 250, 'description' => 'Site-wide search feature', 'icon' => 'search', 'popular' => false),
                array('name' => 'Multi-language Support', 'price' => 600, 'description' => 'Translate site to multiple languages', 'icon' => 'globe', 'popular' => false),
                array('name' => 'Member/Login Area', 'price' => 500, 'description' => 'User registration & profiles', 'icon' => 'users', 'popular' => false),
                array('name' => 'Live Chat Widget', 'price' => 200, 'description' => 'Real-time chat support', 'icon' => 'message-circle', 'popular' => true),
                array('name' => 'Appointment Booking', 'price' => 400, 'description' => 'Online scheduling system', 'icon' => 'calendar', 'popular' => false),
                array('name' => 'Reviews/Testimonials', 'price' => 150, 'description' => 'Customer review display', 'icon' => 'star', 'popular' => false),
                array('name' => 'FAQ Section', 'price' => 100, 'description' => 'Accordion FAQ module', 'icon' => 'help-circle', 'popular' => false),
                array('name' => 'Interactive Maps', 'price' => 150, 'description' => 'Google Maps integration', 'icon' => 'map-pin', 'popular' => false),
                array('name' => 'Animations & Effects', 'price' => 300, 'description' => 'Scroll animations & parallax', 'icon' => 'zap', 'popular' => false),
            ),
            
            'ecommerce_features' => array(
                array('name' => 'Product Catalog (up to 25)', 'price' => 400, 'description' => 'Basic product listing', 'icon' => 'package'),
                array('name' => 'Product Catalog (25-100)', 'price' => 800, 'description' => 'Medium product catalog', 'icon' => 'packages'),
                array('name' => 'Product Catalog (100-500)', 'price' => 1500, 'description' => 'Large product inventory', 'icon' => 'database'),
                array('name' => 'Product Catalog (500+)', 'price' => 2500, 'description' => 'Enterprise inventory', 'icon' => 'server'),
                array('name' => 'Payment Gateway', 'price' => 350, 'description' => 'Stripe, PayPal integration', 'icon' => 'credit-card'),
                array('name' => 'Inventory Management', 'price' => 400, 'description' => 'Stock tracking & alerts', 'icon' => 'clipboard'),
                array('name' => 'Shipping Calculator', 'price' => 300, 'description' => 'Real-time shipping rates', 'icon' => 'truck'),
                array('name' => 'Discount/Coupon System', 'price' => 200, 'description' => 'Promo codes & discounts', 'icon' => 'tag'),
                array('name' => 'Product Reviews', 'price' => 150, 'description' => 'Customer product reviews', 'icon' => 'message-square'),
                array('name' => 'Wishlist Feature', 'price' => 150, 'description' => 'Save for later functionality', 'icon' => 'heart'),
                array('name' => 'Product Comparisons', 'price' => 250, 'description' => 'Compare products side-by-side', 'icon' => 'columns'),
                array('name' => 'Abandoned Cart Recovery', 'price' => 350, 'description' => 'Recover lost sales via email', 'icon' => 'refresh-cw'),
            ),
            
            'seo_marketing' => array(
                array('name' => 'Basic SEO Setup', 'price' => 300, 'description' => 'Meta tags, sitemap, basics', 'icon' => 'search'),
                array('name' => 'Advanced SEO Package', 'price' => 800, 'description' => 'Full on-page optimization', 'icon' => 'trending-up'),
                array('name' => 'Google Analytics Setup', 'price' => 150, 'description' => 'Traffic tracking & reports', 'icon' => 'bar-chart-2'),
                array('name' => 'Google Search Console', 'price' => 100, 'description' => 'Search performance tracking', 'icon' => 'activity'),
                array('name' => 'Speed Optimization', 'price' => 400, 'description' => 'Fast loading performance', 'icon' => 'zap'),
                array('name' => 'Schema Markup', 'price' => 250, 'description' => 'Rich snippets for search', 'icon' => 'code'),
                array('name' => 'Social Media Setup', 'price' => 200, 'description' => 'Profile creation & linking', 'icon' => 'share-2'),
                array('name' => 'Email Marketing Setup', 'price' => 300, 'description' => 'Mailchimp/ConvertKit setup', 'icon' => 'mail'),
            ),
            
            'hosting_options' => array(
                array('name' => 'No Hosting Needed', 'price' => 0, 'monthly' => 0, 'description' => 'I have my own hosting', 'icon' => 'x'),
                array('name' => 'Basic Shared Hosting', 'price' => 0, 'monthly' => 15, 'description' => 'Good for small websites', 'icon' => 'hard-drive'),
                array('name' => 'Premium Hosting', 'price' => 0, 'monthly' => 35, 'description' => 'Faster speeds, more resources', 'icon' => 'cpu'),
                array('name' => 'VPS Hosting', 'price' => 0, 'monthly' => 75, 'description' => 'Dedicated resources', 'icon' => 'server'),
                array('name' => 'Managed WordPress', 'price' => 0, 'monthly' => 50, 'description' => 'Optimized for WordPress', 'icon' => 'shield'),
                array('name' => 'Enterprise Hosting', 'price' => 0, 'monthly' => 150, 'description' => 'High-traffic, enterprise-grade', 'icon' => 'cloud'),
            ),
            
            'maintenance_plans' => array(
                array('name' => 'No Maintenance', 'price' => 0, 'description' => 'Self-managed website', 'icon' => 'x'),
                array('name' => 'Basic Care', 'price' => 75, 'description' => 'Updates & backups', 'icon' => 'shield'),
                array('name' => 'Standard Care', 'price' => 150, 'description' => 'Updates, backups, minor edits', 'icon' => 'check-circle'),
                array('name' => 'Premium Care', 'price' => 300, 'description' => 'Full support & priority fixes', 'icon' => 'star'),
                array('name' => 'Enterprise Care', 'price' => 500, 'description' => '24/7 support, SLA guarantee', 'icon' => 'award'),
            ),
            
            'timeline_options' => array(
                array('name' => 'Flexible (6-8 weeks)', 'multiplier' => 0.95, 'description' => 'Best value, flexible schedule', 'icon' => 'clock'),
                array('name' => 'Standard (4-6 weeks)', 'multiplier' => 1, 'description' => 'Normal delivery timeline', 'icon' => 'calendar'),
                array('name' => 'Priority (2-4 weeks)', 'multiplier' => 1.25, 'description' => 'Faster delivery priority', 'icon' => 'zap'),
                array('name' => 'Rush (1-2 weeks)', 'multiplier' => 1.5, 'description' => 'Urgent, expedited delivery', 'icon' => 'alert-circle'),
                array('name' => 'Emergency (< 1 week)', 'multiplier' => 2, 'description' => 'ASAP, all hands on deck', 'icon' => 'alert-triangle'),
            ),
            
            'content_options' => array(
                array('name' => 'I\'ll Provide Content', 'price' => 0, 'description' => 'You supply text & images', 'icon' => 'upload'),
                array('name' => 'Basic Copywriting', 'price' => 400, 'description' => 'Up to 5 pages of copy', 'icon' => 'edit-2'),
                array('name' => 'Professional Copywriting', 'price' => 1000, 'description' => 'Full site professional copy', 'icon' => 'file-text'),
                array('name' => 'Stock Photography', 'price' => 200, 'description' => 'Licensed stock images', 'icon' => 'image'),
                array('name' => 'Custom Photography', 'price' => 800, 'description' => 'Professional photo shoot', 'icon' => 'camera'),
                array('name' => 'Video Production', 'price' => 1500, 'description' => 'Professional promo video', 'icon' => 'video'),
            ),
            
            'form_settings' => array(
                'show_contact_form' => true,
                'require_contact' => false,
                'email_recipient' => get_option('admin_email'),
                'success_message' => 'Thank you! Your personalized quote has been submitted. We\'ll contact you within 24 hours with a detailed proposal.',
                'enable_pdf' => true,
                'enable_save_quote' => true,
            ),
            
            'styling' => array(
                'primary_color' => '#6366F1',
                'secondary_color' => '#10B981',
                'accent_color' => '#F59E0B',
                'text_color' => '#1F2937',
                'background_color' => '#F8FAFC',
                'card_color' => '#FFFFFF',
                'gradient_start' => '#6366F1',
                'gradient_end' => '#8B5CF6',
            ),
            
            'advanced' => array(
                'show_comparison' => true,
                'show_savings' => true,
                'show_tooltips' => true,
                'animate_numbers' => true,
                'show_industry_presets' => true,
                'show_popular_badges' => true,
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
        
        // Add settings link on plugins page
        add_filter('plugin_action_links_' . WCC_PLUGIN_BASENAME, array($this, 'add_settings_link'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        
        // Shortcode
        add_shortcode('website_cost_calculator', array($this, 'render_calculator'));
        
        // AJAX handlers
        add_action('wp_ajax_wcc_submit_quote', array($this, 'handle_quote_submission'));
        add_action('wp_ajax_nopriv_wcc_submit_quote', array($this, 'handle_quote_submission'));
        add_action('wp_ajax_wcc_generate_pdf', array($this, 'handle_pdf_generation'));
        add_action('wp_ajax_nopriv_wcc_generate_pdf', array($this, 'handle_pdf_generation'));
        add_action('wp_ajax_wcc_save_quote', array($this, 'handle_save_quote'));
        add_action('wp_ajax_nopriv_wcc_save_quote', array($this, 'handle_save_quote'));
        add_action('wp_ajax_wcc_load_quote', array($this, 'handle_load_quote'));
        add_action('wp_ajax_nopriv_wcc_load_quote', array($this, 'handle_load_quote'));
    }
    
    /**
     * Add settings link on plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=website-cost-calculator') . '">' . __('Settings', 'website-cost-calculator') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options if not exists
        if (!get_option('wcc_options')) {
            update_option('wcc_options', $this->default_options);
        }
        
        // Create database tables
        $this->create_quotes_table();
        $this->create_saved_quotes_table();
        
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
            industry varchar(50),
            selections longtext NOT NULL,
            breakdown longtext,
            subtotal decimal(10,2),
            total_cost decimal(10,2) NOT NULL,
            monthly_cost decimal(10,2),
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'pending',
            source varchar(50) DEFAULT 'calculator',
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create saved quotes table
     */
    private function create_saved_quotes_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcc_saved_quotes';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            quote_code varchar(20) NOT NULL UNIQUE,
            quote_data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime,
            views int DEFAULT 0,
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
            __('Cost Calculator', 'website-cost-calculator'),
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
        
        add_submenu_page(
            'website-cost-calculator',
            __('Analytics', 'website-cost-calculator'),
            __('Analytics', 'website-cost-calculator'),
            'manage_options',
            'wcc-analytics',
            array($this, 'render_analytics_page')
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
        if (isset($input['currency_symbol'])) {
            $input['currency_symbol'] = sanitize_text_field($input['currency_symbol']);
        }
        return $input;
    }
    
    /**
     * Admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'website-cost-calculator') === false && 
            strpos($hook, 'wcc-quotes') === false && 
            strpos($hook, 'wcc-analytics') === false) {
            return;
        }
        
        wp_enqueue_style('wcc-admin-style', WCC_PLUGIN_URL . 'assets/css/admin-style.css', array(), WCC_VERSION);
        wp_enqueue_script('wcc-admin-script', WCC_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), WCC_VERSION, true);
        
        // Chart.js for analytics
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.0', true);
        
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
        
        // Include Feather Icons
        wp_enqueue_script('feather-icons', 'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js', array(), '4.29.0', true);
        
        // Include html2canvas and jsPDF for PDF generation
        wp_enqueue_script('html2canvas', 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js', array(), '1.4.1', true);
        wp_enqueue_script('jspdf', 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js', array(), '2.5.1', true);
        
        // Include Chart.js for breakdown chart
        wp_enqueue_script('chartjs-frontend', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);
        
        $options = get_option('wcc_options', $this->default_options);
        
        wp_localize_script('wcc-frontend-script', 'wccFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcc_frontend_nonce'),
            'options' => $options,
            'currencySymbol' => $options['currency_symbol'] ?? '$',
            'currencyPosition' => $options['currency_position'] ?? 'before',
            'showRange' => $options['show_price_range'] ?? true,
            'rangeVariance' => $options['range_variance'] ?? 20,
            'strings' => array(
                'low' => __('Budget', 'website-cost-calculator'),
                'mid' => __('Standard', 'website-cost-calculator'),
                'high' => __('Premium', 'website-cost-calculator'),
            ),
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
                --wcc-card: {$styling['card_color']};
                --wcc-gradient-start: {$styling['gradient_start']};
                --wcc-gradient-end: {$styling['gradient_end']};
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
     * Render analytics page
     */
    public function render_analytics_page() {
        include WCC_PLUGIN_DIR . 'admin/analytics-page.php';
    }
    
    /**
     * Render calculator shortcode
     */
    public function render_calculator($atts) {
        $atts = shortcode_atts(array(
            'theme' => 'default',
            'compact' => 'false',
            'industry' => '',
        ), $atts, 'website_cost_calculator');
        
        ob_start();
        include WCC_PLUGIN_DIR . 'templates/calculator-pro.php';
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
     * Get icon SVG
     */
    public function get_icon($name) {
        return '<i data-feather="' . esc_attr($name) . '"></i>';
    }
    
    /**
     * Get website type icon
     */
    public function get_website_type_icon($type_name) {
        $icons = array(
            'landing' => 'file',
            'basic' => 'globe',
            'business' => 'building',
            'blog' => 'edit-3',
            'portfolio' => 'image',
            'ecommerce' => 'shopping-bag',
            'store' => 'shopping-cart',
            'application' => 'code',
            'web app' => 'code',
            'enterprise' => 'server',
            'custom' => 'tool',
        );
        
        $type_lower = strtolower($type_name);
        foreach ($icons as $key => $icon) {
            if (strpos($type_lower, $key) !== false) {
                return $icon;
            }
        }
        
        return 'globe';
    }
    
    /**
     * Handle quote submission via AJAX
     */
    public function handle_quote_submission() {
        if (!wp_verify_nonce($_POST['nonce'], 'wcc_frontend_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
        }
        
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $company = sanitize_text_field($_POST['company'] ?? '');
        $industry = sanitize_text_field($_POST['industry'] ?? '');
        $selections = sanitize_text_field($_POST['selections'] ?? '');
        $breakdown = sanitize_text_field($_POST['breakdown'] ?? '');
        $subtotal = floatval($_POST['subtotal'] ?? 0);
        $total_cost = floatval($_POST['total_cost'] ?? 0);
        $monthly_cost = floatval($_POST['monthly_cost'] ?? 0);
        $notes = sanitize_textarea_field($_POST['notes'] ?? '');
        
        if (empty($name) || empty($email)) {
            wp_send_json_error(array('message' => 'Name and email are required.'));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcc_quotes';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'company' => $company,
                'industry' => $industry,
                'selections' => $selections,
                'breakdown' => $breakdown,
                'subtotal' => $subtotal,
                'total_cost' => $total_cost,
                'monthly_cost' => $monthly_cost,
                'notes' => $notes,
                'status' => 'pending',
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to save quote request.'));
        }
        
        $quote_id = $wpdb->insert_id;
        
        // Send email notification
        $this->send_quote_notification($name, $email, $phone, $company, $industry, $selections, $total_cost, $monthly_cost, $notes);
        
        $options = $this->get_options();
        $success_message = $options['form_settings']['success_message'] ?? 'Thank you! We will contact you shortly.';
        
        wp_send_json_success(array(
            'message' => $success_message,
            'quote_id' => $quote_id,
        ));
    }
    
    /**
     * Send quote notification email
     */
    private function send_quote_notification($name, $email, $phone, $company, $industry, $selections, $total_cost, $monthly_cost, $notes) {
        $options = $this->get_options();
        $to = $options['form_settings']['email_recipient'] ?? get_option('admin_email');
        $currency = $options['currency_symbol'] ?? '$';
        
        $subject = sprintf(__('New Website Quote Request from %s - %s%s', 'website-cost-calculator'), $name, $currency, number_format($total_cost, 2));
        
        $message = sprintf(
            __("A new website quote request has been submitted.\n\n" .
            "=== CONTACT INFORMATION ===\n" .
            "Name: %s\n" .
            "Email: %s\n" .
            "Phone: %s\n" .
            "Company: %s\n" .
            "Industry: %s\n\n" .
            "=== PROJECT DETAILS ===\n%s\n\n" .
            "=== PRICING ===\n" .
            "One-time Cost: %s%s\n" .
            "Monthly Cost: %s%s/month\n\n" .
            "=== ADDITIONAL NOTES ===\n%s\n\n" .
            "---\nRespond promptly to convert this lead!", 'website-cost-calculator'),
            $name,
            $email,
            $phone ?: 'Not provided',
            $company ?: 'Not provided',
            $industry ?: 'Not specified',
            $selections,
            $currency,
            number_format($total_cost, 2),
            $currency,
            number_format($monthly_cost, 2),
            $notes ?: 'None'
        );
        
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'Reply-To: ' . $name . ' <' . $email . '>',
        );
        
        wp_mail($to, $subject, $message, $headers);
        
        // Send confirmation to customer
        $customer_subject = __('Your Website Project Quote', 'website-cost-calculator');
        $customer_message = sprintf(
            __("Thank you for your interest, %s!\n\n" .
            "We've received your website quote request with an estimated investment of %s%s.\n\n" .
            "What happens next:\n" .
            "1. Our team will review your requirements\n" .
            "2. We'll prepare a detailed proposal\n" .
            "3. You'll receive a response within 24 hours\n\n" .
            "In the meantime, feel free to reply to this email with any questions.\n\n" .
            "Best regards,\nThe Web Development Team", 'website-cost-calculator'),
            $name,
            $currency,
            number_format($total_cost, 2)
        );
        
        wp_mail($email, $customer_subject, $customer_message);
    }
    
    /**
     * Handle save quote
     */
    public function handle_save_quote() {
        if (!wp_verify_nonce($_POST['nonce'], 'wcc_frontend_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
        }
        
        $quote_data = sanitize_text_field($_POST['quote_data'] ?? '');
        
        if (empty($quote_data)) {
            wp_send_json_error(array('message' => 'No quote data provided.'));
        }
        
        // Generate unique quote code
        $quote_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcc_saved_quotes';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'quote_code' => $quote_code,
                'quote_data' => $quote_data,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            ),
            array('%s', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to save quote.'));
        }
        
        wp_send_json_success(array(
            'quote_code' => $quote_code,
            'message' => 'Quote saved successfully!',
        ));
    }
    
    /**
     * Handle load quote
     */
    public function handle_load_quote() {
        if (!wp_verify_nonce($_POST['nonce'], 'wcc_frontend_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
        }
        
        $quote_code = sanitize_text_field($_POST['quote_code'] ?? '');
        
        if (empty($quote_code)) {
            wp_send_json_error(array('message' => 'No quote code provided.'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcc_saved_quotes';
        
        $quote = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE quote_code = %s AND expires_at > NOW()",
            $quote_code
        ));
        
        if (!$quote) {
            wp_send_json_error(array('message' => 'Quote not found or expired.'));
        }
        
        // Update view count
        $wpdb->update(
            $table_name,
            array('views' => $quote->views + 1),
            array('id' => $quote->id)
        );
        
        wp_send_json_success(array(
            'quote_data' => $quote->quote_data,
        ));
    }
    
    /**
     * Handle PDF generation
     */
    public function handle_pdf_generation() {
        // PDF is generated client-side using jsPDF
        // This endpoint can be used for server-side PDF if needed
        wp_send_json_success(array('message' => 'PDF generation handled client-side.'));
    }
}

// Initialize the plugin
function wcc_init() {
    return Website_Cost_Calculator::get_instance();
}

add_action('plugins_loaded', 'wcc_init');
