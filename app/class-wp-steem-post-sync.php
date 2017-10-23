<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Post_Sync {

	protected $steem;


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param WP_Steem $steem
	 * @return void
	 */
	public function __construct($steem = null) {
		$this->steem = $steem ? $steem : wp_steem();
	}

	/**
	 * Handle
	 *
	 * @since 1.0.0
	 * @param WP_Steem_Post $post
	 * @return boolean
	 */
	public function handle($post) {
		if ( ! in_array($post->post->post_type, WP_Steem_Helper::get_post_types())) {
			return false;
		}

		$title = $post->get_title();
		$body = $post->get_body();
		$rewards = $post->rewards;
		$parent_permalink = WP_Steem_Helper::sanitize_permalink($post->parent_permalink);
		$permalink = WP_Steem_Helper::sanitize_permalink($post->permalink);
		$tags = implode(' ', WP_Steem_Helper::sanitize_tags($post->tags));
		$tags = empty($tags) ? $parent_permalink : $tags;

		if ($post->include_header == true) {
			$body = $post->header . "\n" . $body;
		}

		if ($post->include_footer == true) {
			$body = $body . "\n" . $post->footer;
		}

		if ( ! $post->published) {
			$response = $this->steem->post(
				$parent_permalink,
				$permalink,
				$title,
				$body,
				array(
					'tags' => $tags,
					'canonical' => get_permalink($post->id),
				),
				array(
					'rewards' => $rewards,
				)
			);
		}
		else {

			$response = $this->steem->post(
				$parent_permalink,
				$permalink,
				$title,
				$body,
				array(
					'tags' => $tags
				)
			);
		}

		if (empty($response) || ! isset($response->success) || $response->success == false) {
			return false;
		}

		if ($response != null && $response->success == true) {
			$operation = $response->data->operations[0][1];

			$post->update_meta('published', true);
			$post->update_meta('tags', $tags);
			$post->update_meta('raw', $response->data);

			if ($post->published_at == null) {

				$post->update_meta('published_at', time());
				$post->update_meta('rewards', $rewards);

				$post->update_meta('parent_permalink', $operation->parent_permlink);
				$post->update_meta('permalink', $operation->permlink);
				$post->update_meta('author', $this->steem->get_account());
			}

			$post->update_meta('updated_at', time());

			// Remember when was the last successful communication with the Steem blockchain
			update_option('wp_steem_synced_at', time());
		}

		return true;
	}
}