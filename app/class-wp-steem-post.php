<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use League\HTMLToMarkdown\HtmlConverter;

class WP_Steem_Post {

	public $post;

	public function __construct($post) {
		if (is_int($post)) {
			$post = get_post($post);
		}

		$this->id = (int) $post->ID;
		$this->post = $post;
	}

	public function __get($name) {
		
		if ($this->post != null) {

			if ($name == 'title') {
				return $this->post->post_title;
			}

			if ($name == 'body') {
				if ($this->use_body) {
					return $this->get_meta('body');
				}

				return $this->toMarkdown();
			}

			if ($name == 'parent_author') {
				return null;
			}

			if ($name == 'parent_permalink' || $name == 'parent_permlink') {
				$parent_permalink = $this->get_meta('parent_permalink');
				return $parent_permalink ? $parent_permalink : 'uncategorized';
			}

			if ($name == 'author') {
				return $this->get_meta('author');
			}

			if ($name == 'permalink' || $name == 'permlink') {
				$permalink = $this->get_meta('permalink');
				return $permalink ? $permalink : $this->post->post_name;
			}


			# 

			if ($name == 'raw') {
				return $this->get_meta('raw');
			}

			if ($name == 'published') {
				return (boolean) $this->get_meta('published');
			}

			if ($name == 'published_at') {
				return $this->get_meta('published_at');
			}

			if ($name == 'updated_at') {
				return $this->get_meta('updated_at');
			}

			if ($name == 'tags') {
				return $this->get_meta('tags');
			}

			if ($name == 'rewards') {
				return $this->get_meta('rewards');
			}

			if ($name == 'use_body') {
				return (boolean) $this->get_meta('use_body');
			}
		}
	}

	public function toMarkdown($options = array()) {
		$options = wp_parse_args($options, array(
			'strip_tags' => true,
		));

		if (empty($this->post) || empty($this->post->post_content)) {
			return null;
		}

		$converter = new HtmlConverter($options);

		$body = $converter->convert($this->post->post_content);

		// Fixes when HTML tags are stripped away including line breaks on headings
		$body = str_replace('# ', "\n# ", $body);
		$body = str_replace('## ', "\n## ", $body);
		$body = str_replace('### ', "\n### ", $body);
		$body = str_replace('#### ', "\n#### ", $body);
		$body = str_replace('##### ', "\n##### ", $body);
		$body = str_replace('###### ', "\n###### ", $body);

		return $body;
	}


	# 

	public function get_link($platform = 'steemit') {
		$link = null;

		switch ($platform) {
			case 'busy':
			case 'busy.org':
			case 'busyorg':
				$link = sprintf(
					'https://busy.org/%s/@%s/%s', 
					$this->parent_permalink, 
					$this->author, 
					$this->permalink
				);
				break;

			case 'chainbb' :
				$link = sprintf(
					'https://beta.chainbb.com/%s/@%s/%s', 
					$this->parent_permalink, 
					$this->author, 
					$this->permalink
				);
				break;
			
			default:
				$link = sprintf(
					'https://steemit.com/%s/@%s/%s', 
					$this->parent_permalink, 
					$this->author, 
					$this->permalink
				);
				break;
		}

		return $link;
	}


	# Helpers

	public function get_meta($key, $single = true) {
		return get_post_meta($this->id, "_wp_steem_{$key}", $single);
	}

	public function update_meta($key, $new_value, $old_value = null) {
		return update_post_meta($this->id, "_wp_steem_{$key}", $new_value, $old_value);
	}

	public function delete_meta($key, $value = null) {
		return delete_post_meta($this->id, "_wp_steem_{$key}", $value);
	}
}