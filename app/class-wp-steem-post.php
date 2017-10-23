<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use League\HTMLToMarkdown\HtmlConverter;

class WP_Steem_Post {

	public $id;
	public $post;

	protected $fillable = array(
		'body',
		'permalink',
		'raw',
		'published',
		'published_at',
		'updated_at',
		'tags',
		'rewards',
		'use_body',

		'include_header',
		'header',
		'include_footer',
		'footer',
	);


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param mixed $post
	 * @return void
	 */
	public function __construct($post) {
		if (is_int($post)) {
			$post = get_post($post);
		}

		$this->id = (int) $post->ID;
		$this->post = $post;
	}

	/**
	 * Getter
	 *
	 * @since 1.0.0
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		
		if ($this->post != null) {

			if ($name == 'title') {
				return $this->post->post_title;
			}

			if ($name == 'body') {
				if ($this->use_body) {
					return $this->get_meta('body');
				}

				return $this->format(array(), 'markdown');
			}

			if ($name == 'parent_author') {
				return null;
			}

			if ($name == 'parent_permalink' || $name == 'parent_permlink') {
				$parent_permalink = $this->get_meta('parent_permalink');

				if (empty($parent_permalink)) {

					if ($tags = WP_Steem_Helper::sanitize_tags(explode(' ', $this->tags))) {
						$parent_permalink = $tags[0];
					}
				}

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
				$rewards = $this->get_meta('rewards');
				return $rewards != null ? abs($rewards) : wp_steem_get_default_reward_option();
			}

			if ($name == 'use_body') {
				return (boolean) $this->get_meta('use_body');
			}

			if ($name == 'editable') {
				return (boolean) (isset($this->raw->expiration) 
						? (strtotime($this->raw->expiration) + (60 * 60 * 24 * 7)) >= time()
							: true)
				;
			}

			if ($name == 'include_header') {
				return (boolean) $this->get_meta('include_header');
			}

			if ($name == 'header') {
				return $this->get_meta('header');
			}

			if ($name == 'include_footer') {
				return (boolean) $this->get_meta('include_footer');
			}

			if ($name == 'footer') {
				return $this->get_meta('footer');
			}
		}
	}

	/**
	 * Save the post
	 *
	 * @since 1.0.5
	 */
	public function save() {

		$fields = $this->fillable;

		if ($this->published) {

			if (isset($fields['permalink'])) {
				unset($fields['permalink']);
			}
		}

		foreach ($fields as $field) {
			if ($this->get_meta($field) != $this->{$field}) {
				$this->update_meta($field, $this->{$field});
			}
		}
	}

	/**
	 * Format the body
	 *
	 * @since 1.0.5
	 * @param array $options
	 * @param string $type
	 * @return string $body
	 */
	public function format($options = array(), $type = 'markdown') {
		$options = wp_parse_args($options, array(
			'strip_tags' => true,
		));

		if (empty($this->post) || empty($this->post->post_content)) {
			return null;
		}

		$converter = new HtmlConverter($options);

		$body = apply_filters('the_content', $this->post->post_content);
		$body = $converter->convert($body);

		// Fixes when HTML tags are stripped away including line breaks on headings
		$body = str_replace('# ', "\n# ", $body);
		$body = str_replace('## ', "\n## ", $body);
		$body = str_replace('### ', "\n### ", $body);
		$body = str_replace('#### ', "\n#### ", $body);
		$body = str_replace('##### ', "\n##### ", $body);
		$body = str_replace('###### ', "\n###### ", $body);

		return $body;
	}

	/**
	 * Format post body to markdown
	 *
	 * @since 1.0.5
	 * @param array $options
	 * @return string
	 */
	public function format_markdown($options = array()) {
		return $this->format($options, 'markdown');
	}

	/**
	 * Retrieve post title
	 *
	 * @since 1.0.5
	 * @return string
	 */
	public function get_title() {

		return $this->title;
	}

	/**
	 * Retrieve post body
	 *
	 * @since 1.0.5
	 * @return string
	 */
	public function get_body() {
		if ($this->use_body) {
			return $this->get_meta('body');
		}

		return $this->format(array(), 'markdown');
	}


	# 

	/**
	 * Retrieve Steem service link by platform
	 *
	 * @since 1.0.0
	 * @param string $platform
	 * @return string $link
	 */
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

			case 'steemd' :
				$link = sprintf('https://steemd.com/%s/@%s/%s', 
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

	/**
	 * Retrieve post meta
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @param boolean $single
	 * @return mixed
	 */
	public function get_meta($key, $single = true) {
		return get_post_meta($this->id, "_wp_steem_{$key}", $single);
	}

	/**
	 * Update post meta
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @param mixed $new_value
	 * @param mixed $old_value
	 * @return mixed
	 */
	public function update_meta($key, $new_value, $old_value = null) {
		return update_post_meta($this->id, "_wp_steem_{$key}", $new_value, $old_value);
	}

	/**
	 * Delete post meta
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function delete_meta($key, $value = null) {
		return delete_post_meta($this->id, "_wp_steem_{$key}", $value);
	}

	/**
	 * Check if post meta exists in a WordPress post
	 *
	 * @since 1.0.5
	 * @param string $meta_key
	 * @return boolean
	 */
	public function has_meta($key) {
		return metadata_exists('post', $this->id, "_wp_steem_{$key}");
	}
}