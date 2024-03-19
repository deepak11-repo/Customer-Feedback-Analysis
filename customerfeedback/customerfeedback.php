<?php
/*
Plugin Name: Customer Feedback Insights
Plugin URI: https://yourwebsite.com/plugin-details
Description: This plugin gathers customer feedback and provides insights.
Version: 1.0
Author: Deepak Naidu
Author URI: https://yourwebsite.com/author-info
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: customer-feedback-insights
Domain Path: /languages
*/

/**
 * Registers the activation hook for the plugin.
 * This function will be called when the plugin is activated.
 */
register_activation_hook(__FILE__, 'activate_custom_plugin');

/**
 * Registers the activation hook callback.
 * This will call the my_custom_plugin_activation_check() function
 * when the plugin is activated.
 */
add_action('admin_init', 'my_custom_plugin_activation_check');

/**
 * Includes the activation script that handles plugin activation tasks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/activation/activation.php';
