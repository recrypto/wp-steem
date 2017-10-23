<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem {

	protected $account = null;
	protected $posting_key = null;


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param string $account
	 * @param string $posting_key
	 * @return void
	 */
	public function __construct($account, $posting_key) {
		$this->account = $account;
		$this->posting_key = $posting_key;
	}

	/**
	 * Post to the Steem blockchain
	 * 
	 * @since 1.0.0
	 * @param string $parent_permalink
	 * @param string $permalink
	 * @param string $title
	 * @param string $body
	 * @param array $json_metadata
	 * @param array $options
	 * @return array $response_body
	 */
	public function post($parent_permalink, $permalink, $title, $body, $json_metadata = array(), $options = array()) {
		$response_body = null;

		$json_metadata['app'] = sprintf('wp-steem/%s', WP_STEEM_VERSION);
		$json_metadata['community'] = 'blogs';

		if (is_array($json_metadata['tags'])) {
			$json_metadata['tags'] = implode(' ', $json_metadata['tags']);
		}

		$response = wp_remote_post('https://steemful.com/api/v1/posts/', array(
			'body' => array(
				'key' => $this->posting_key,
				'parent_permalink' => $parent_permalink,
				'author' => $this->account,
				'permalink' => $permalink,
				'title' => $title,
				'body' => $body,
				'json_metadata' => $json_metadata,
				'options' => $options,
			)
		));

		if ($response != null && is_wp_error($response) == false) {
			$response_body = json_decode(wp_remote_retrieve_body($response));
		}

		return $response_body;
	}

	/**
	 * Retrieve Steem account
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_account() {
		return $this->account;
	}

	/**
	 * Retrieve Steem posting key
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_posting_key() {
		return $this->posting_key;
	}


	# Helpers

	/**
	 * Retrieve last synced time on Steem blockchain
	 * 
	 * @since 1.0.3
	 * @return timestamp
	 */
	public static function get_synced_at() {
		return get_option('wp_steem_synced_at', time());
	}
}