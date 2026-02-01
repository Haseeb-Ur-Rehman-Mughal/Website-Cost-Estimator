<?php
/**
 * Analytics Page
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'wcc_quotes';

// Get statistics
$total_quotes = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
$total_value = $wpdb->get_var("SELECT SUM(total_cost) FROM $table_name") ?: 0;
$avg_value = $wpdb->get_var("SELECT AVG(total_cost) FROM $table_name") ?: 0;
$converted = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'converted'");
$conversion_rate = $total_quotes > 0 ? ($converted / $total_quotes) * 100 : 0;

// Get quotes by status
$status_data = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $table_name GROUP BY status");

// Get quotes by industry
$industry_data = $wpdb->get_results("SELECT industry, COUNT(*) as count, AVG(total_cost) as avg_cost FROM $table_name WHERE industry != '' GROUP BY industry ORDER BY count DESC LIMIT 10");

// Get recent quotes (last 30 days)
$daily_data = $wpdb->get_results("
    SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total_cost) as total 
    FROM $table_name 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
    GROUP BY DATE(created_at) 
    ORDER BY date ASC
");

// Get top requested features (from selections)
$all_selections = $wpdb->get_col("SELECT selections FROM $table_name");
$feature_counts = array();
foreach ($all_selections as $selection) {
    if (preg_match('/Features: (.+)$/m', $selection, $matches)) {
        $features = array_map('trim', explode(',', $matches[1]));
        foreach ($features as $feature) {
            if (!empty($feature)) {
                $feature_counts[$feature] = ($feature_counts[$feature] ?? 0) + 1;
            }
        }
    }
}
arsort($feature_counts);
$top_features = array_slice($feature_counts, 0, 10, true);

$wcc = Website_Cost_Calculator::get_instance();
$options = $wcc->get_options();
$currency = $options['currency_symbol'] ?? '$';
?>

<div class="wrap wcc-analytics-wrap">
    <h1><?php _e('Calculator Analytics', 'website-cost-calculator'); ?></h1>
    
    <div class="wcc-stats-grid">
        <div class="wcc-stat-card">
            <div class="wcc-stat-icon" style="background: #EEF2FF; color: #6366F1;">
                <span class="dashicons dashicons-format-aside"></span>
            </div>
            <div class="wcc-stat-content">
                <div class="wcc-stat-value"><?php echo number_format($total_quotes); ?></div>
                <div class="wcc-stat-label"><?php _e('Total Quotes', 'website-cost-calculator'); ?></div>
            </div>
        </div>
        
        <div class="wcc-stat-card">
            <div class="wcc-stat-icon" style="background: #D1FAE5; color: #10B981;">
                <span class="dashicons dashicons-chart-area"></span>
            </div>
            <div class="wcc-stat-content">
                <div class="wcc-stat-value"><?php echo $currency . number_format($total_value, 0); ?></div>
                <div class="wcc-stat-label"><?php _e('Total Pipeline Value', 'website-cost-calculator'); ?></div>
            </div>
        </div>
        
        <div class="wcc-stat-card">
            <div class="wcc-stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="wcc-stat-content">
                <div class="wcc-stat-value"><?php echo $currency . number_format($avg_value, 0); ?></div>
                <div class="wcc-stat-label"><?php _e('Average Quote Value', 'website-cost-calculator'); ?></div>
            </div>
        </div>
        
        <div class="wcc-stat-card">
            <div class="wcc-stat-icon" style="background: #FCE7F3; color: #EC4899;">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="wcc-stat-content">
                <div class="wcc-stat-value"><?php echo number_format($conversion_rate, 1); ?>%</div>
                <div class="wcc-stat-label"><?php _e('Conversion Rate', 'website-cost-calculator'); ?></div>
            </div>
        </div>
    </div>
    
    <div class="wcc-analytics-grid">
        <div class="wcc-analytics-card wcc-chart-card">
            <h3><?php _e('Quotes Over Time (Last 30 Days)', 'website-cost-calculator'); ?></h3>
            <canvas id="wcc-quotes-chart" height="250"></canvas>
        </div>
        
        <div class="wcc-analytics-card">
            <h3><?php _e('Quote Status Distribution', 'website-cost-calculator'); ?></h3>
            <canvas id="wcc-status-chart" height="250"></canvas>
        </div>
    </div>
    
    <div class="wcc-analytics-grid">
        <div class="wcc-analytics-card">
            <h3><?php _e('Top Industries', 'website-cost-calculator'); ?></h3>
            <?php if (!empty($industry_data)): ?>
            <table class="wcc-analytics-table">
                <thead>
                    <tr>
                        <th><?php _e('Industry', 'website-cost-calculator'); ?></th>
                        <th><?php _e('Quotes', 'website-cost-calculator'); ?></th>
                        <th><?php _e('Avg. Value', 'website-cost-calculator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($industry_data as $industry): ?>
                    <tr>
                        <td><?php echo esc_html($industry->industry); ?></td>
                        <td><?php echo number_format($industry->count); ?></td>
                        <td><?php echo $currency . number_format($industry->avg_cost, 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="wcc-no-data"><?php _e('No industry data available yet.', 'website-cost-calculator'); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="wcc-analytics-card">
            <h3><?php _e('Most Requested Features', 'website-cost-calculator'); ?></h3>
            <?php if (!empty($top_features)): ?>
            <div class="wcc-feature-list">
                <?php 
                $max_count = max($top_features);
                foreach ($top_features as $feature => $count): 
                    $percentage = ($count / $max_count) * 100;
                ?>
                <div class="wcc-feature-item">
                    <div class="wcc-feature-info">
                        <span class="wcc-feature-name"><?php echo esc_html($feature); ?></span>
                        <span class="wcc-feature-count"><?php echo $count; ?> <?php _e('requests', 'website-cost-calculator'); ?></span>
                    </div>
                    <div class="wcc-feature-bar">
                        <div class="wcc-feature-fill" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="wcc-no-data"><?php _e('No feature data available yet.', 'website-cost-calculator'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.wcc-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.wcc-stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.wcc-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wcc-stat-icon .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
}

.wcc-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

.wcc-stat-label {
    font-size: 13px;
    color: #6b7280;
}

.wcc-analytics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.wcc-analytics-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
}

.wcc-analytics-card h3 {
    margin: 0 0 20px;
    font-size: 16px;
    color: #1f2937;
}

.wcc-analytics-table {
    width: 100%;
    border-collapse: collapse;
}

.wcc-analytics-table th,
.wcc-analytics-table td {
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.wcc-analytics-table th {
    font-weight: 600;
    color: #6b7280;
    font-size: 12px;
    text-transform: uppercase;
}

.wcc-feature-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.wcc-feature-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.wcc-feature-info {
    display: flex;
    justify-content: space-between;
}

.wcc-feature-name {
    font-size: 14px;
    color: #1f2937;
}

.wcc-feature-count {
    font-size: 12px;
    color: #6b7280;
}

.wcc-feature-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.wcc-feature-fill {
    height: 100%;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    border-radius: 4px;
}

.wcc-no-data {
    color: #6b7280;
    font-style: italic;
}

@media (max-width: 1200px) {
    .wcc-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .wcc-stats-grid,
    .wcc-analytics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Quotes over time chart
    var quotesCtx = document.getElementById('wcc-quotes-chart');
    if (quotesCtx) {
        new Chart(quotesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_data ?: [], 'date')); ?>,
                datasets: [{
                    label: 'Quotes',
                    data: <?php echo json_encode(array_column($daily_data ?: [], 'count')); ?>,
                    borderColor: '#6366F1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    // Status distribution chart
    var statusCtx = document.getElementById('wcc-status-chart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($status_data ?: [], 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($status_data ?: [], 'count')); ?>,
                    backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#6B7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
});
</script>
