<?php
/**
 * Plugin Name: WordPress Steem
 * Plugin URI: https://github.com/recrypto/wp-steem
 * Description: Publish your WordPress posts on Steem blockchain.
 * Version: 1.0.3
 * Author: ReCrypto
 * Author URI: https://steemit.com/@recrypto
 * Requires at least: 4.1
 * Tested up to: 4.8.0
 *
 * Text Domain: wp-steem
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define('WP_STEEM_VERSION', '1.0.3');
define('WP_STEEM_DIR_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('WP_STEEM_DIR_URL', trailingslashit(plugin_dir_url(__FILE__)));


register_activation_hook(__FILE__, 'wp_steem_activate');
register_deactivation_hook(__FILE__, 'wp_steem_deactivate');

/** 
 * Plugin activation
 *
 * @since 1.0.0
 */
function wp_steem_activate() {
	do_action('wp_steem_activated');
}

/**
 * Plugin deactivation
 *
 * @since 1.0.0
 */
function wp_steem_deactivate() {
	do_action('wp_steem_deactivated');
}

/**
 * Plugin init
 * 
 * @since 1.0.0
 */
function wp_steem_init() {

	/**
	 * Fires before including the files
	 *
	 * @since 1.0.0
	 */
	do_action('wp_steem_pre_init');

	require_once(WP_STEEM_DIR_PATH . 'vendor/autoload.php');
	require_once(WP_STEEM_DIR_PATH . 'app/class-wp-steem.php');
	require_once(WP_STEEM_DIR_PATH . 'app/class-wp-steem-post.php');
	require_once(WP_STEEM_DIR_PATH . 'app/class-wp-steem-post-sync.php');
	require_once(WP_STEEM_DIR_PATH . 'app/wp-steem-functions.php');

	if (is_admin()) {
		require_once(WP_STEEM_DIR_PATH . 'app/admin/wp-steem-post-meta-handler.php');
		require_once(WP_STEEM_DIR_PATH . 'app/admin/wp-steem-settings-handler.php');
	}

	/**
	 * Fires after including the files
	 *
	 * @since 1.0.0
	 */
	do_action('wp_steem_init');
}
add_action('plugins_loaded', 'wp_steem_init');