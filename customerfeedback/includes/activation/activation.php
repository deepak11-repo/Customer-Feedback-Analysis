<?php

function my_custom_plugin_activation_check() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins('customerfeedback/customerfeedback.php');
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        add_action('admin_notices', 'my_custom_plugin_activation_error_notice');
    } 
}

function activate_custom_plugin() {   
    customer_feedback_plugin_create_table(); 
}

function my_custom_plugin_activation_error_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('This plugin requires WooCommerce to be installed and activated. Please install WooCommerce and try again.', 'my-custom-plugin'); ?></p>
    </div>
    <?php
}

function customer_feedback_plugin_create_table() {
    global $wpdb;    
    $table_name = $wpdb->prefix . 'customer_feedback';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        return;
    }
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        category varchar(255) NOT NULL,
        type varchar(255) NOT NULL,
        label varchar(255) NOT NULL,
        ratings varchar(255) NOT NULL,
        reviews varchar(255) NOT NULL
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

require_once plugin_dir_path( __FILE__ ) . '../admin/admin-functions.php';
require_once plugin_dir_path( __FILE__ ) . '../public/public-functions.php';
