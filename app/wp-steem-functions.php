<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

# Settings

/**
 * Retrieve Steem settings
 *
 * @since 1.0.0
 * @return array
 */
function wp_steem_get_settings() {
	return WP_Steem_Helper::get_settings();
}

/**
 * Retrieve a specific Steem setting by key with a default value
 *
 * @since 1.0.0
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function wp_steem_get_setting($key, $default = null) {
	return WP_Steem_Helper::get_setting($key, $default);
}

/**
 * Check if WordPress Steem is set up
 *
 * @since 1.0.1
 * @return boolean
 */
function wp_steem_is_setup() {
	return WP_Steem_Helper::is_setup();
}


# 

/**
 * Instantiate a new WP_Steem object
 *
 * @since 1.0.0
 * @param string $account
 * @param string $posting_key
 * @return WP_Steem $steem
 */
function wp_steem($account = null, $posting_key = null) {
	$steem = null;

	$account = $account ? $account : wp_steem_get_setting('account');
	$posting_key = $posting_key ? $posting_key : wp_steem_get_setting('posting_key');

	if (empty($account) || empty($posting_key)) {
		return $steem;
	}

	$steem = new WP_Steem($account, $posting_key);

	return $steem;
}

/**
 * Retrieve list of platforms that is built on top of Steem blockchain
 *
 * @since 1.0.0
 * @return array
 */
function wp_steem_get_platforms() {
	return WP_Steem_Helper::get_platforms();
}

/**
 * Retrieve reward options for a post
 *
 * @since 1.0.0
 * @return array
 */
function wp_steem_get_reward_options() {
	return WP_Steem_Helper::get_rewards();
}

/**
 * Retrieve default reward option
 *
 * @since 1.0.0
 * @return int
 */
function wp_steem_get_default_reward_option() {
	return WP_Steem_Helper::get_default_reward();
}