<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem {

	protected $account = null;
	protected $posting_key = null;

	public function __construct($account, $posting_key) {
		$this->account = $account;
		$this->posting_key = $posting_key;
	}

	public function post($parent_permalink, $permalink, $title, $body, $json_metadata = array(), $options = array()) {
		$response_body = null;

		$json_metadata['app'] = sprintf('wp-steem/%s', WP_STEEM_VERSION);
		$json_metadata['community'] = 'blogs';

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

	public function get_account() {
		return $this->account;
	}

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