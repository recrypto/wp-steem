<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Helper {

	# Settings

	/**
	 * Retrieve Steem settings
	 *
	 * @since 1.0.5
	 * @return array
	 */
	public static function get_settings() {
		return get_option('wp_steem_settings', array());
	}

	/**
	 * Retrieve a specific Steem setting by key with a default value
	 *
	 * @since 1.0.5
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get_setting($key, $default = null) {
		$settings = self::get_settings();
		return isset($settings[$key]) ? $settings[$key] : $default;
	}


	#

	/**
	 * Check if the plugin is setup properly with proper settings
	 *
	 * @since 1.0.5
	 * @return boolean
	 */
	public static function is_setup() {
		return self::get_setting('account') && self::get_setting('posting_key');
	}


	# 

	/**
	 * Retrieve enabled post types
	 *
	 * @since 1.0.5
	 * @return array
	 */
	public static function get_post_types() {
		return self::get_setting('post_types', array('post'));
	}

	/**
	 * Retrieve Steem service platforms
	 *
	 * @since 1.0.5
	 * @return array
	 */
	public static function get_platforms() {
		return apply_filters('wp_steem_platforms', array(
			'steemit' => __('Steemit', 'wp-steem'),
			'chainbb' => __('chainBB', 'wp-steem'),
			'busy' => __('Busy.org', 'wp-steem'),
			'steemd' => __('Steemd', 'wp-steem'),
		));
	}

	/**
	 * Retrieve Steem post reward options
	 *
	 * @since 1.0.5
	 * @return array
	 */
	public static function get_rewards() {
		return apply_filters('wp_steem_reward_options', array(
			100 => __('Power Up 100%', 'wp-steem'),
			50 => __('Default (50% / 50%)', 'wp-steem'),
			0 => __('Decline Payout', 'wp-steem')
		));
	}

	/**
	 * Retrieve default Steem post reward option
	 *
	 * @since 1.0.5
	 * @return int
	 */
	public static function get_default_reward() {
		return apply_filters('wp_steem_default_reward_option', 50);
	}


	# Sanitations

	/**
	 * Sanitize tags 
	 *
	 * @since 1.0.5
	 * @param array $tags
	 * @return array $tags
	 */
	public static function sanitize_tags($tags) {
		if ( ! empty($tags)) {
			$tags = is_string($tags) ? explode(' ', $tags) : $tags;

			foreach ($tags as $index => $tag) {
				$tags[$index] = sanitize_title(trim($tag));
			}
		}

		return $tags ? $tags : array();
	}

	/**
	 * Sanitize post permalink
	 *
	 * @since 1.0.5
	 * @param string $permalink
	 * @return string
	 */
	public static function sanitize_permalink($permalink) {
		return str_replace('_', '-', sanitize_title($permalink));
	}
}