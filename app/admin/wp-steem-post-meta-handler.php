<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Post_Meta_Handler {

	public static function init() {
		$instance = __CLASS__;

		add_action('post_submitbox_misc_actions', array($instance, 'post_actions'));
		add_action('save_post', array($instance, 'post'));

		add_action('admin_enqueue_scripts', array($instance, 'register_editor_scripts'));
		add_filter('the_editor', array($instance, 'display_editor'));
	}

	public static function post_actions() {
		global $post; 

		$steem_post = new WP_Steem_Post($post); 

		if ($steem_post->post->post_type != 'post') {
			return;
		}

		$default_reward_option = wp_steem_get_default_reward_option();
		$synced_at = get_option('wp_steem_synced_at', time());
	?>

		<hr>

		<div class="misc-pub-section wp-steem-post-actions">
			<h3 style="margin-top: 0; margin-bottom: 15px !important;">Steem</h3>

			<?php if (wp_steem_get_setting('account') && wp_steem_get_setting('posting_key')) : ?>

				<?php if (time() - $synced_at < 300) : ?>
					<p style="color: blue;"><?php _e("Please be reminded that there is a 5 minute cooldown when creating or updating a Steem post in the Steem blockchain.", 'wp-steem'); ?></p>
					<p><?php printf('%s seconds left to do another post action.', 300 - (time() - $synced_at)); ?></p>
				<?php endif; ?>

				<?php if ($steem_post->published) : ?>
					<label>
						<input type="checkbox" name="wp_steem[update]" value="1" />
						<?php _e('Update on Steem blockchain', 'wp-steem'); ?>
					</label>

					<div>
						<p>
							<label style="display: block;">
								<strong><?php _e('Rewards', 'wp-steem'); ?></strong>

								<?php if ($reward_options = wp_steem_get_reward_options()) : ?>
									<span style="display: block; width: 100%;">
										<?php foreach ($reward_options as $reward_option => $reward_option_label) : ?>
											<?php echo ($reward_option == $steem_post->rewards) ? $reward_option_label : ''; ?>
										<?php endforeach; ?>
									</span>
								<?php endif; ?>
							</label>
						</p>
						<p>
							<label style="display: block;">
								<strong><?php _e('Author', 'wp-steem'); ?></strong>

								<span style="display: block; width: 100%;">
									<?php echo $steem_post->author; ?>
								</span>
							</label>
						</p>
						<p>
							<label style="display: block;">
								<strong><?php _e('Permalink', 'wp-steem'); ?></strong>

								<span style="display: block; width: 100%;">
									<?php echo $steem_post->permalink; ?>
								</span>
							</label>
						</p>
						<p>
							<label style="display: block;">
								<strong><?php _e('Tags (Separated by a space)', 'wp-steem'); ?></strong>

								<input type="text" name="wp_steem[tags]" value="<?php echo implode(' ', $steem_post->tags); ?>" style="width: 100%;" />
							</label>
						</p>
					</div>

					<div>
						<h4 style="margin: 0;"><?php _e('Platforms', 'wp-steem'); ?></h4>

						<?php if ($platforms = wp_steem_get_platforms()) : ?>
						<ul style="margin-top: 0;">
							<?php foreach ($platforms as $platform => $platform_label) : ?>
								<li>
									<a href="<?php echo apply_filters("wp_steem_{$platform}_platform_post_link", $steem_post->get_link($platform), $steem_post); ?>" target="_blank">
										<?php echo $platform_label; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>

						<p>
							<?php printf(__('Last update at %s ago in Steem blockchain.', 'wp-steem'), human_time_diff($steem_post->updated_at)); ?>
						</p>
					</div>

				<?php else : ?>
					<label>
						<input type="checkbox" name="wp_steem[publish]" value="1" />
						<?php _e('Publish on Steem blockchain', 'wp-steem'); ?>
					</label>

					<div style="margin-top: 5px;">
						<p>
							<label style="display: block;">
								<strong><?php _e('Rewards', 'wp-steem'); ?></strong>

								<?php if ($reward_options = wp_steem_get_reward_options()) : ?>
									<select name="wp_steem[rewards]" style="width: 100%;">
										<?php foreach ($reward_options as $reward_option => $reward_option_label) : ?>
											<option value="<?php echo $reward_option; ?>" <?php selected($reward_option, $default_reward_option); ?>><?php echo $reward_option_label; ?></option>
										<?php endforeach; ?>
									</select>
								<?php endif; ?>
							</label>
						</p>

						<p>
							<label style="display: block;">
								<strong><?php _e('Permalink', 'wp-steem'); ?></strong>

								<input type="text" name="wp_steem[permalink]" value="" style="width: 100%;" />
							</label>
						</p>

						<p>
							<label style="display: block;">
								<strong><?php _e('Tags (Separated by a space)', 'wp-steem'); ?></strong>

								<input type="text" name="wp_steem[tags]" value="" style="width: 100%;" />
							</label>
						</p>
					</div>
				<?php endif; ?>

			<?php else : ?>

				<p>
				<?php 
					printf(
						__("Please setup first your Steem account via %s.", 'wp-steem'),
						sprintf(
							'<a href="%s">%s</a>', 
							admin_url('options-general.php?page=wp-steem'),
							__('Steem Settings', 'wp-steem')
						)
					);
				?>
				</p>

			<?php endif; ?>
		</div>

		<?php
	}

	public static function post($post_id) {
		$post = new WP_Steem_Post($post_id);

		if ($post->post->post_type != 'post') {
			return;
		}

		if ($post->published) {
			if ( ! self::has_field('update') || self::get_field('update') == false) {
				return;
			}
		}
		else {
			if ( ! self::has_field('publish') || self::get_field('publish') == false) {
				return;
			}
		}

		$synchronizer = new WP_Steem_Post_Sync();
		$synchronizer->handle($post, array(
			'use_body' => self::has_field('use_body'),
			'body' => self::get_field('body'),
			'tags' => self::get_field('tags'),
			'permalink' => self::get_field('permalink'),
			'rewards' => self::get_field('rewards'),
		));
	}


	# Component - Editor

	public static function register_editor_scripts($page) {
		if ( ! in_array($page, array('post.php', 'post-new.php'))) {
			return;
		}

		wp_enqueue_script('wp-steem', WP_STEEM_DIR_URL . 'public/assets/js/plugin.js');

		wp_localize_script('wp-steem', 'wp_steem', array(
			'tabText' => __('Markdown', 'wp-steem'),
		));

		wp_enqueue_style('wp-steem', WP_STEEM_DIR_URL . 'public/assets/css/plugin.css');
	}

	public static function display_editor($content) {
		global $post;

		preg_match("/<textarea[^>]*id=[\"']([^\"']+)\"/", $content, $matches);

		if ( ! isset($matches[1]) || $matches[1] !== 'content') {
			return $content;
		}

		$steem_post = new WP_Steem_Post($post); 

		if ($steem_post->post->post_type != 'post') {
			return $content;
		}

		ob_start();
		include(WP_STEEM_DIR_PATH . 'resources/views/editor.php');
		$content .= ob_get_clean();

		return $content;
	}


	# Helpers

	public static function has_field($key) {
		return isset($_POST['wp_steem'][$key]);
	}

	public static function get_field($key) {
		return self::has_field($key) ? $_POST['wp_steem'][$key] : null;
	}
}

WP_Steem_Post_Meta_Handler::init();