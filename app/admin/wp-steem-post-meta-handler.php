<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Post_Meta_Handler {

	public static function init() {
		$instance = __CLASS__;

		add_action('admin_notices', array($instance, 'post_notices'));
		add_action('post_submitbox_misc_actions', array($instance, 'post_actions'));
		add_action('save_post', array($instance, 'post'));

		add_action('admin_enqueue_scripts', array($instance, 'register_editor_scripts'));
		add_filter('the_editor', array($instance, 'display_editor'));
	}

	public static function post_notices() {
		$screen = get_current_screen();

		if ( ! isset($screen->post_type) || empty($screen->post_type)) {
			return;
		}

		if ( ! isset($_GET['wp_steem_error'])) {
			return;
		}

		$synced_at = WP_Steem::get_synced_at();
	?>

		<?php if ($_GET['wp_steem_error'] == 'cooldown' && time() - $synced_at < 300) : ?>
			<div class="wp-steem-notice notice notice-error">
				<p>
					<?php 
						printf(
							__('Please wait for %s seconds to be able to publish this post to the Steem blockchain as there is a 5 minute cooldown.', 'wp-steem'),
							300 - (time() - $synced_at)
						);
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php
	}

	public static function post_actions() {
		global $post;

		$steem_post = new WP_Steem_Post($post); 

		if ( ! in_array($steem_post->post->post_type, wp_steem_get_setting('post_types', array('post')))) {
			return;
		}

		$default_reward_option = wp_steem_get_default_reward_option();
		$synced_at = WP_Steem::get_synced_at();
	?>

		<hr>

		<div class="misc-pub-section wp-steem-post-actions">
			<h3 style="margin-top: 0; margin-bottom: 15px !important;">Steem</h3>

			<?php if (wp_steem_get_setting('account') && wp_steem_get_setting('posting_key')) : ?>

				<?php if (time() - $synced_at < 300) : ?>
					<p style="color: blue;"><?php _e("Please be reminded that there is a 5 minute cooldown when creating a Steem post in the Steem blockchain.", 'wp-steem'); ?></p>
					<p><?php printf(__('%s seconds left to do another post action.', 'wp-steem'), 300 - (time() - $synced_at)); ?></p>
				<?php endif; ?>

				<?php if ($steem_post->published) : ?>

					<?php if ($steem_post->editable) : ?>
						<label>
							<input type="checkbox" name="wp_steem[update]" value="1" <?php checked(true, wp_steem_get_setting('default_update', false)); ?> />
							<?php _e('Update on Steem blockchain', 'wp-steem'); ?>
						</label>
					<?php endif; ?>

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
						<input type="checkbox" name="wp_steem[publish]" value="1" <?php checked(true, wp_steem_get_setting('default_store', false)); ?> />
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

								<input type="text" name="wp_steem[tags]" value="<?php echo wp_steem_get_setting('default_tags'); ?>" style="width: 100%;" />
							</label>
						</p>
					</div>
				<?php endif; ?>

				<p style="color: red;">
					<?php 
						printf(
							__("WARNING: Once a post is published or updated on the Steem blockchain, there would be a %s record of it on the Steem blockchain.", 'wp-steem'), 
							sprintf(
								'<strong>%s</strong>',
								__('PERMANENT', 'wp-steem')
							)
						);
					?>
				</p>

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

		if ( ! in_array($post->post->post_type, wp_steem_get_setting('post_types', array('post')))) {
			return;
		}

		if ($post->published) {
			if ( ! self::has_field('update') || self::get_field('update') == false) {
				return;
			}

			if ( ! $post->editable) {
				return;
			}
		}
		else {
			if ( ! self::has_field('publish') || self::get_field('publish') == false) {
				return;
			}
		}

		$synchronizer = new WP_Steem_Post_Sync();
		$synchronized = $synchronizer->handle($post, array(
			'use_body' => self::has_field('use_body'),
			'body' => self::get_field('body'),
			'tags' => self::get_field('tags'),
			'permalink' => self::get_field('permalink'),
			'rewards' => self::get_field('rewards'),
		));

		if ( ! $synchronized) {
			self::trigger_notices();
		}
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

		if ( ! in_array($steem_post->post->post_type, wp_steem_get_setting('post_types', array('post')))) {
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


	# Internals

	/**
	 * Register a query parameter to trigger the post notice
	 *
	 * @since 1.0.3
	 * @param string $location
	 * @return stirng $location
	 */
	public static function register_redirect_post_location($location) {
		$instance = __CLASS__;

		remove_filter('redirect_post_location', array($instance, 'register_redirect_post_location'));

		$location = add_query_arg(array(
			'wp_steem_error' => 'cooldown',
		), $location);

		return $location;
	}

	/**
	 * Trigger the post notice
	 *
	 * @since 1.0.3
	 */
	protected static function trigger_notices() {
		$instance = __CLASS__;

		add_filter('redirect_post_location', array($instance, 'register_redirect_post_location'));
	}
}

WP_Steem_Post_Meta_Handler::init();