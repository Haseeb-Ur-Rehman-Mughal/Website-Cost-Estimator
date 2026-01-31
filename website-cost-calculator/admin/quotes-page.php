<?php
/**
 * Quote Requests Admin Page
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'wcc_quotes';

// Handle status update
if (isset($_POST['wcc_update_status']) && wp_verify_nonce($_POST['wcc_quotes_nonce'], 'wcc_update_quote')) {
    $quote_id = intval($_POST['quote_id']);
    $new_status = sanitize_text_field($_POST['new_status']);
    
    $wpdb->update(
        $table_name,
        array('status' => $new_status),
        array('id' => $quote_id),
        array('%s'),
        array('%d')
    );
    
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Quote status updated.', 'website-cost-calculator') . '</p></div>';
}

// Handle quote deletion
if (isset($_POST['wcc_delete_quote']) && wp_verify_nonce($_POST['wcc_quotes_nonce'], 'wcc_update_quote')) {
    $quote_id = intval($_POST['quote_id']);
    
    $wpdb->delete($table_name, array('id' => $quote_id), array('%d'));
    
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Quote deleted.', 'website-cost-calculator') . '</p></div>';
}

// Get quotes
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

$where_clause = '';
if (!empty($status_filter)) {
    $where_clause = $wpdb->prepare(" WHERE status = %s", $status_filter);
}

$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name" . $where_clause);
$total_pages = ceil($total_items / $per_page);

$quotes = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name" . $where_clause . " ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    )
);

// Get status counts
$status_counts = $wpdb->get_results(
    "SELECT status, COUNT(*) as count FROM $table_name GROUP BY status",
    OBJECT_K
);

$wcc = Website_Cost_Calculator::get_instance();
$options = $wcc->get_options();
$currency = $options['currency_symbol'] ?? '$';
?>

<div class="wrap wcc-quotes-wrap">
    <h1><?php _e('Quote Requests', 'website-cost-calculator'); ?></h1>
    
    <ul class="subsubsub">
        <li>
            <a href="<?php echo admin_url('admin.php?page=wcc-quotes'); ?>" <?php echo empty($status_filter) ? 'class="current"' : ''; ?>>
                <?php _e('All', 'website-cost-calculator'); ?>
                <span class="count">(<?php echo $total_items; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=wcc-quotes&status=pending'); ?>" <?php echo $status_filter === 'pending' ? 'class="current"' : ''; ?>>
                <?php _e('Pending', 'website-cost-calculator'); ?>
                <span class="count">(<?php echo isset($status_counts['pending']) ? $status_counts['pending']->count : 0; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=wcc-quotes&status=contacted'); ?>" <?php echo $status_filter === 'contacted' ? 'class="current"' : ''; ?>>
                <?php _e('Contacted', 'website-cost-calculator'); ?>
                <span class="count">(<?php echo isset($status_counts['contacted']) ? $status_counts['contacted']->count : 0; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=wcc-quotes&status=converted'); ?>" <?php echo $status_filter === 'converted' ? 'class="current"' : ''; ?>>
                <?php _e('Converted', 'website-cost-calculator'); ?>
                <span class="count">(<?php echo isset($status_counts['converted']) ? $status_counts['converted']->count : 0; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=wcc-quotes&status=closed'); ?>" <?php echo $status_filter === 'closed' ? 'class="current"' : ''; ?>>
                <?php _e('Closed', 'website-cost-calculator'); ?>
                <span class="count">(<?php echo isset($status_counts['closed']) ? $status_counts['closed']->count : 0; ?>)</span>
            </a>
        </li>
    </ul>
    
    <table class="wp-list-table widefat fixed striped wcc-quotes-table">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-name"><?php _e('Name', 'website-cost-calculator'); ?></th>
                <th scope="col" class="manage-column column-email"><?php _e('Email', 'website-cost-calculator'); ?></th>
                <th scope="col" class="manage-column column-company"><?php _e('Company', 'website-cost-calculator'); ?></th>
                <th scope="col" class="manage-column column-total"><?php _e('Estimated Total', 'website-cost-calculator'); ?></th>
                <th scope="col" class="manage-column column-date"><?php _e('Date', 'website-cost-calculator'); ?></th>
                <th scope="col" class="manage-column column-status"><?php _e('Status', 'website-cost-calculator'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php _e('Actions', 'website-cost-calculator'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quotes)): ?>
            <tr>
                <td colspan="7"><?php _e('No quote requests found.', 'website-cost-calculator'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($quotes as $quote): ?>
            <tr>
                <td class="column-name">
                    <strong><?php echo esc_html($quote->name); ?></strong>
                    <?php if (!empty($quote->phone)): ?>
                    <br><small><?php echo esc_html($quote->phone); ?></small>
                    <?php endif; ?>
                </td>
                <td class="column-email">
                    <a href="mailto:<?php echo esc_attr($quote->email); ?>"><?php echo esc_html($quote->email); ?></a>
                </td>
                <td class="column-company"><?php echo esc_html($quote->company); ?></td>
                <td class="column-total">
                    <strong><?php echo $currency . number_format($quote->total_cost, 2); ?></strong>
                </td>
                <td class="column-date">
                    <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quote->created_at)); ?>
                </td>
                <td class="column-status">
                    <span class="wcc-status wcc-status-<?php echo esc_attr($quote->status); ?>">
                        <?php echo esc_html(ucfirst($quote->status)); ?>
                    </span>
                </td>
                <td class="column-actions">
                    <button type="button" class="button button-small wcc-view-details" data-quote-id="<?php echo $quote->id; ?>">
                        <?php _e('View', 'website-cost-calculator'); ?>
                    </button>
                </td>
            </tr>
            <tr class="wcc-quote-details" id="quote-details-<?php echo $quote->id; ?>" style="display: none;">
                <td colspan="7">
                    <div class="wcc-quote-details-inner">
                        <h4><?php _e('Quote Details', 'website-cost-calculator'); ?></h4>
                        
                        <div class="wcc-quote-selections">
                            <strong><?php _e('Selections:', 'website-cost-calculator'); ?></strong>
                            <pre><?php echo esc_html($quote->selections); ?></pre>
                        </div>
                        
                        <?php if (!empty($quote->notes)): ?>
                        <div class="wcc-quote-notes">
                            <strong><?php _e('Additional Notes:', 'website-cost-calculator'); ?></strong>
                            <p><?php echo nl2br(esc_html($quote->notes)); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="wcc-quote-actions">
                            <form method="post" style="display: inline-block;">
                                <?php wp_nonce_field('wcc_update_quote', 'wcc_quotes_nonce'); ?>
                                <input type="hidden" name="quote_id" value="<?php echo $quote->id; ?>">
                                <select name="new_status">
                                    <option value="pending" <?php selected($quote->status, 'pending'); ?>><?php _e('Pending', 'website-cost-calculator'); ?></option>
                                    <option value="contacted" <?php selected($quote->status, 'contacted'); ?>><?php _e('Contacted', 'website-cost-calculator'); ?></option>
                                    <option value="converted" <?php selected($quote->status, 'converted'); ?>><?php _e('Converted', 'website-cost-calculator'); ?></option>
                                    <option value="closed" <?php selected($quote->status, 'closed'); ?>><?php _e('Closed', 'website-cost-calculator'); ?></option>
                                </select>
                                <input type="submit" name="wcc_update_status" class="button" value="<?php _e('Update Status', 'website-cost-calculator'); ?>">
                            </form>
                            
                            <form method="post" style="display: inline-block; margin-left: 10px;">
                                <?php wp_nonce_field('wcc_update_quote', 'wcc_quotes_nonce'); ?>
                                <input type="hidden" name="quote_id" value="<?php echo $quote->id; ?>">
                                <input type="submit" name="wcc_delete_quote" class="button button-link-delete" value="<?php _e('Delete', 'website-cost-calculator'); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this quote?', 'website-cost-calculator'); ?>');">
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($total_pages > 1): ?>
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <?php
            $page_links = paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $current_page,
            ));
            
            if ($page_links) {
                echo $page_links;
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>
