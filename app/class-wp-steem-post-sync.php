<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Post_Sync {

	protected $steem;

	public function __construct($steem = null) {
		$this->steem = $steem ? $steem : wp_steem();
	}

	public function handle($post, $attributes = array()) {
		$attributes = wp_parse_args($attributes, array(
			'use_body' => false,
		));

		if (empty($attributes)) {
			return false;
		}

		if ( ! in_array($post->post->post_type, wp_steem_get_setting('post_types', array('post')))) {
			return false;
		}

		$tags = isset($attributes['tags']) ? $this->sanitize_tags(explode(' ', $attributes['tags'])) : array();

		if (isset($attributes['use_body'])) {
			$body = $attributes['use_body'] ? $attributes['body'] : null;
		}

		$post->update_meta('use_body', $attributes['use_body']);
		$post->update_meta('body', $body);

		$parent_permalink = $this->sanitize_permalink($post->parent_permalink);
		$permalink = $this->sanitize_permalink($post->permalink);
		$title = $post->title;
		$body = $post->body;

		if ( ! $post->published) {

			if (isset($tags[0]) && $tags[0]) {
				$parent_permalink = $tags[0];
			}

			$permalink = isset($attributes['permalink']) && sanitize_title($attributes['permalink'])
							? $this->sanitize_permalink($attributes['permalink']) 
								: $permalink;
			$rewards = isset($attributes['rewards']) && $attributes['rewards'] != null
							? abs($attributes['rewards']) 
								: wp_steem_get_default_reward_option();

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

		if ($response != null && $response->success == true) {
			$post->update_meta('published', true);
			$post->update_meta('tags', $tags);
			$post->update_meta('rewards', $rewards);

			if ($post->published_at == null) {
				$post->update_meta('published_at', time());

				$post->update_meta('parent_permalink', $response->data->operations[0][1]->parent_permlink);
				$post->update_meta('permalink', $response->data->operations[0][1]->permlink);
				$post->update_meta('author', $this->steem->get_account());
				$post->update_meta('raw', $response->data);
			}

			$post->update_meta('updated_at', time());

			// Remember when was the last successful communication with the Steem blockchain
			update_option('wp_steem_synced_at', time());
		}
	}


	# Helpers

	protected function sanitize_tags($tags) {
		if (! empty($tags)) {
			foreach ($tags as $index => $tag) {
				$tags[$index] = sanitize_title(trim($tag));
			}
		}
		return $tags;
	}

	protected function sanitize_permalink($permalink) {
		return str_replace('_', '-', sanitize_title($permalink));
	}
}