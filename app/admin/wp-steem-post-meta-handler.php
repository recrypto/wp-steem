<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Post_Meta_Handler {

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$instance = __CLASS__;

		add_action('add_meta_boxes', array($instance, 'register_meta_boxes'));

		add_action('admin_notices', array($instance, 'post_notices'));
		add_action('save_post', array($instance, 'post'));

		add_action('admin_enqueue_scripts', array($instance, 'register_editor_scripts'));
		add_filter('the_editor', array($instance, 'display_editor'));
	}

	/**
	 * Register post meta boxes
	 * 
	 * @since 1.0.5
	 */
	public static function register_meta_boxes() {
		$instance = __CLASS__;
		
		if ($post_types = WP_Steem_Helper::get_post_types()) {
			add_meta_box('wp-steem', __('Steem', 'wp-steem'), array($instance, 'post_actions'), $post_types, 'advanced', 'high');
		}
	}

	/**
	 * Display post notices
	 * 
	 * @since 1.0.3
	 */
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

	/**
	 * Display the post settings
	 *
	 * @since 1.0.0
	 */
	public static function post_actions() {
		global $post;

		$steem_post = new WP_Steem_Post($post); 

		if ( ! in_array($steem_post->post->post_type, WP_Steem_Helper::get_post_types())) {
			return;
		}

		$post_id = $steem_post->post->ID;
		$synced_at = WP_Steem::get_synced_at();

		$fields = array(
			'rewards' => $steem_post->has_meta('rewards') ? $steem_post->rewards : wp_steem_get_default_reward_option(),
			'tags' => $steem_post->has_meta('tags') ? $steem_post->tags : wp_steem_get_setting('default_tags'),
			'permalink' => $steem_post->has_meta('permalink') ? $steem_post->permalink : null,

			'header' => $steem_post->has_meta('header') ? $steem_post->header : wp_steem_get_setting('header', null),
			'footer' => $steem_post->has_meta('footer') ? $steem_post->footer : wp_steem_get_setting('footer', null),
			'include_header' => $steem_post->has_meta('include_header') ? $steem_post->include_header : wp_steem_get_setting('include_header', false),
			'include_footer' => $steem_post->has_meta('include_footer') ? $steem_post->include_footer : wp_steem_get_setting('include_footer', false),
		);
	?>

		<div class="wp-steem-notices">

			<?php if (WP_Steem_Helper::is_setup()) : ?>

				<?php if (time() - $synced_at < 300) : ?>

					<div class="wp-steem-notice wp-steem-notice-error">
						<p style="color: blue;"><?php _e("Please be reminded that there is a 5 minute cooldown when creating a Steem post in the Steem blockchain.", 'wp-steem'); ?></p>

						<p><?php printf(__('%s seconds left to do another post action.', 'wp-steem'), 300 - (time() - $synced_at)); ?></p>
					</div>

				<?php endif; ?>

			<?php else : ?>

				<div class="wp-steem-notice wp-steem-notice-error">
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
				</div>

			<?php endif; ?>

		</div>

		<?php if (WP_Steem_Helper::is_setup()) : ?>

			<div class="row">
				<div class="col-md-6">
					<h3>General</h3>

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
												<?php echo ($reward_option == $fields['rewards']) ? $reward_option_label : ''; ?>
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
										<?php echo $fields['permalink']; ?>
									</span>
								</label>
							</p>
							<p>
								<label style="display: block;">
									<strong><?php _e('Tags (Separated by a space)', 'wp-steem'); ?></strong>

									<?php if ($steem_post->editable) : ?>

										<input type="text" name="wp_steem[tags]" value="<?php echo $fields['tags']; ?>" style="width: 100%;" />

									<?php else : ?>

										<span style="display: block; width: 100%;">
											<?php echo implode(' ', $fields['tags']); ?>
										</span>

									<?php endif; ?>
								</label>
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
												<option value="<?php echo $reward_option; ?>" <?php selected($reward_option, $fields['rewards']); ?>><?php echo $reward_option_label; ?></option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
								</label>
							</p>

							<p>
								<label style="display: block;">
									<strong><?php _e('Permalink', 'wp-steem'); ?></strong>

									<input type="text" name="wp_steem[permalink]" value="<?php echo $fields['permalink']; ?>" style="width: 100%;" />
								</label>
							</p>

							<p>
								<label style="display: block;">
									<strong><?php _e('Tags (Separated by a space)', 'wp-steem'); ?></strong>

									<input type="text" name="wp_steem[tags]" value="<?php echo $fields['tags']; ?>" style="width: 100%;" />
								</label>
							</p>
						</div>
					<?php endif; ?>

				</div>

				<div class="col-md-6">
					<h3>Templates</h3>

					<p>
						<label>
							<input type="checkbox" name="wp_steem[include_header]" value="1" <?php checked(true, $fields['include_header']); ?> />
							<?php _e('Include Header', 'wp-steem'); ?>
						</label>
					</p>

					<p class="wp-steem-field-header">
						<label style="display: block;">
							<strong><?php _e('Header', 'wp-steem'); ?></strong>

							<textarea class="regular-text" name="wp_steem[header]" style="width: 100%;" rows="3"><?php echo $fields['header']; ?></textarea>
						</label>
					</p>

					<p>
						<label>
							<input type="checkbox" name="wp_steem[include_footer]" value="1" <?php checked(true, $fields['include_footer']); ?> />
							<?php _e('Include Footer', 'wp-steem'); ?>
						</label>
					</p>

					<p class="wp-steem-field-footer">
						<label style="display: block;">
							<strong><?php _e('Footer', 'wp-steem'); ?></strong>

							<textarea class="regular-text" name="wp_steem[footer]" style="width: 100%;" rows="3"><?php echo $fields['footer'] ?></textarea>
						</label>
					</p>

				</div>
			</div>

			<div class="row">
				<div class="col-md-6">

					<?php if ($steem_post->published) : ?>
						<h3><?php _e('Platforms', 'wp-steem'); ?></h3>

						<p><?php _e("You can view your Steem post to different Steem services that uses the Steem blockchain.", 'wp-steem'); ?></p>

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
							<?php printf(__('Last updated at %s ago in Steem blockchain.', 'wp-steem'), human_time_diff($steem_post->updated_at)); ?>
						</p>

					<?php endif; ?>
				</div>

				<div class="col-md-6">
					<p style="color: red;">
						<?php 
							printf(
								__("%s Once a post is published or updated on the Steem blockchain, there would be a %s record of it on the Steem blockchain.", 'wp-steem'), 
								sprintf(
									'<strong>%s</strong>',
									__('WARNING:', 'wp-steem')
								),
								sprintf(
									'<strong>%s</strong>',
									__('PERMANENT', 'wp-steem')
								)
							);
						?>
					</p>
				</div>
			</div>

		<?php endif; ?>

		<?php
	}

	/**
	 * Save the post settings and execute calls to Steem API
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @return void
	 */
	public static function post($post_id) {
		$post = new WP_Steem_Post($post_id);

		if ( ! in_array($post->post->post_type, WP_Steem_Helper::get_post_types())) {
			return;
		}

		if ($post->published && ! $post->editable) {
			return;
		}

		$post->body = self::get_field('body');
		$post->use_body = self::get_field('use_body') == true;
		$post->tags = implode(' ', WP_Steem_Helper::sanitize_tags(self::get_field('tags')));

		if ( ! $post->published) {
			if (self::get_field('permalink') != null) {
				$post->permalink = WP_Steem_Helper::sanitize_permalink(self::get_field('permalink'));
			}

			$post->rewards = self::get_field('rewards');
		}

		$post->include_header = self::get_field('include_header') == true;
		$post->include_footer = self::get_field('include_footer') == true;
		$post->header = self::get_field('header');
		$post->footer = self::get_field('footer');

		$post->save();

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
		$synchronized = $synchronizer->handle($post);

		if ( ! $synchronized) {
			self::trigger_notices();
		}
	}


	# Component - Editor

	/**
	 * Register post editor scripts
	 *
	 * @since 1.0.0
	 * @param string $page
	 * @return void
	 */
	public static function register_editor_scripts($page) {
		if ( ! in_array($page, array('post.php', 'post-new.php'))) {
			return;
		}

		wp_enqueue_script('wp-steem', WP_STEEM_DIR_URL . 'public/assets/js/plugin.js');

		wp_localize_script('wp-steem', 'wp_steem', array(
			'tabText' => __('Markdown', 'wp-steem'),
		));

		wp_enqueue_style('wp-steem', WP_STEEM_DIR_URL . 'public/assets/css/plugin.min.css');
	}

	/**
	 * Display post editor
	 *
	 * @since 1.0.0
	 * @param string $content
	 * @return string $content
	 */
	public static function display_editor($content) {
		global $post;

		preg_match("/<textarea[^>]*id=[\"']([^\"']+)\"/", $content, $matches);

		if ( ! isset($matches[1]) || $matches[1] !== 'content') {
			return $content;
		}

		$steem_post = new WP_Steem_Post($post); 

		if ( ! in_array($steem_post->post->post_type, WP_Steem_Helper::get_post_types())) {
			return $content;
		}

		ob_start();
		include(WP_STEEM_DIR_PATH . 'resources/views/editor.php');
		$content .= ob_get_clean();

		return $content;
	}


	# Helpers

	/**
	 * Check for incoming POST request for a specific key
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @return boolean
	 */
	public static function has_field($key) {
		return isset($_POST['wp_steem'][$key]);
	}

	/**
	 * Retrieve an incoming POST request for a specific key with its value
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @return mixed
	 */
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