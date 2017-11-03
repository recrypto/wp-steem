<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Settings_Handler {


	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$instance = __CLASS__;

		add_action('admin_notices', array($instance, 'display_notices'));

		add_action('admin_menu', array($instance, 'register_pages'));
		add_action('admin_init', array($instance, 'register_page_settings'));
		add_action('admin_enqueue_scripts', array($instance, 'enqueue_settings_scripts_styles'));
	}

	/**
	 * Display setting notices
	 *
	 * @since 1.0.1
	 */
	public static function display_notices() { 
		if (wp_steem_is_setup()) return; ?>

		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
					printf(
						__('Please setup the settings for %s.', 'wp-steem'),
						sprintf(
							'<a href="%s">%s</a>',
							admin_url('options-general.php?page=wp-steem'),
							__('WordPress Steem', 'wp-steem')
						)
					);
				?>
			</p>
		</div>

		<?php
	}

	/**
	 * Register pages
	 *
	 * @since 1.0.0
	 */
	public static function register_pages() {
		$instance = __CLASS__;

		add_options_page(
			__('Steem Settings', 'wp-steem'), 
			__('Steem', 'wp-steem'), 
			'manage_options', 
			'wp-steem', 
			array($instance, 'display_page_settings')
		);
	}

	/**
	 * Register page settings
	 *
	 * @since 1.0.0
	 */
	public static function register_page_settings() {
		$instance = __CLASS__;

		register_setting(
			'wp_steem_settings',
			'wp_steem_settings',
			array($instance, 'sanitize')
		);


		# Section - General

		add_settings_section(
			'general', 
			'General',
			array($instance, 'print_section_info'),
			'wp-steem'
		);  

		add_settings_field(
			'account',
			__('Account', 'wp-steem'),
			array($instance, 'display_account_field'),
			'wp-steem',
			'general' 
		);

		add_settings_field(
			'posting_key',
			__('Posting Key', 'wp-steem'),
			array($instance, 'display_posting_key_field'),
			'wp-steem',
			'general' 
		);

		add_settings_field(
			'default_store',
			__('Default Publish to Steem', 'wp-steem'),
			array($instance, 'display_default_store_field'),
			'wp-steem',
			'general' 
		);

		add_settings_field(
			'default_update',
			__('Default Update to Steem', 'wp-steem'),
			array($instance, 'display_default_update_field'),
			'wp-steem',
			'general' 
		);

		add_settings_field(
			'default_tags',
			__('Default Tags', 'wp-steem'),
			array($instance, 'display_default_tags_field'),
			'wp-steem',
			'general' 
		);

		add_settings_field(
			'post_types',
			__('Post Types', 'wp-steem'),
			array($instance, 'display_post_types_field'),
			'wp-steem',
			'general' 
		);


		# Section - Templates

		add_settings_section(
			'templates', 
			'Templates',
			null,
			'wp-steem'
		);

		add_settings_field(
			'include_header',
			__('Default Include Header', 'wp-steem'),
			array($instance, 'display_default_include_header_field'),
			'wp-steem',
			'templates' 
		);

		add_settings_field(
			'header',
			__('Header', 'wp-steem'),
			array($instance, 'display_header_field'),
			'wp-steem',
			'templates' 
		);

		add_settings_field(
			'include_footer',
			__('Default Include Footer', 'wp-steem'),
			array($instance, 'display_default_include_footer_field'),
			'wp-steem',
			'templates' 
		);

		add_settings_field(
			'footer',
			__('Footer', 'wp-steem'),
			array($instance, 'display_footer_field'),
			'wp-steem',
			'templates' 
		);
	}

	/**
	 * Display page settings
	 *
	 * @since 1.0.0
	 */
	public static function display_page_settings() { ?>

		<div class="wrap">
			<h1><?php _e('Steem Settings', 'wp-steem'); ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields('wp_steem_settings'); ?>

				<?php do_settings_sections('wp-steem'); ?>

				<?php submit_button(); ?>

				<hr>

				<p style="width: 35%;">
					<?php 
						printf(
							__("If you would like to continue to support this plugin, please consider helping out the development and maintenance cost of this plugin by donating STEEM/SBD to %s or simply support by upvoting %s post. I would greatly appreciate it. :)", 'wp-steem'),
							sprintf('@<a href="%s" target="_blank">%s</a>', 'https://steemit.com/@recrypto', "recrypto"),
							sprintf('@<a href="%s" target="_blank">%s</a>\'s', 'https://steemit.com/@recrypto', "recrypto")
						);
					?>
				</p>

				<p style="width: 35%;">
					<?php 
						printf(
							__("We've reached version %s! A big thanks to the Steem community for supporting this project and specially to %s %s.", 'wp-steem'),
							WP_STEEM_VERSION,
							sprintf('@<a href="%s" target="_blank">%s</a>', 'https://steemit.com/@transisto', "transisto"),
							sprintf('@<a href="%s" target="_blank">%s</a>', 'https://steemit.com/@newsflash', "newsflash")
						);
					?>
				</p>
			</form>
		</div>

		<?php
	}


	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 * @return array $new_input
	 */
	public static function sanitize($input) {
		$new_input = array();

		if (isset($input['account'])) {
			$new_input['account'] = str_replace('@', '', sanitize_text_field($input['account']));
		}

		if (isset($input['posting_key'])) {
			$new_input['posting_key'] = sanitize_text_field(trim($input['posting_key']));
		}

		$new_input['default_store'] = isset($input['default_store']) ? true : false;
		$new_input['default_update'] = isset($input['default_update']) ? true : false;

		if (isset($input['default_tags'])) {
			$new_input['default_tags'] = $input['default_tags'];
		}

		if (isset($input['post_types'])) {
			$new_input['post_types'] = $input['post_types'];
		}

		$new_input['include_header'] = isset($input['include_header']) ? true : false;
		$new_input['include_footer'] = isset($input['include_footer']) ? true : false;

		if (isset($input['header'])) {
			$new_input['header'] = $input['header'];
		}

		if (isset($input['footer'])) {
			$new_input['footer'] = $input['footer'];
		}

		return $new_input;
	}

	/** 
	 * Print the Section text
	 *
	 * @since 1.0.0
	 */
	public static function print_section_info() { ?>
		<p>
			<?php 
				printf(
					__("If you don't have an account on Steem yet, please register at %s."),
					sprintf('<a href="%s" target="_blank">%s</a>', 'https://steemit.com/pick_account', 'Steemit')
				); 
			?>
		</p>
		<?php
	}


	# Fields

	/**
	 * Display "account" input field
	 *
	 * @since 1.0.0
	 */
	public static function display_account_field() {
		printf(
			'<input type="text" class="regular-text" name="wp_steem_settings[account]" value="%s" />',
			wp_steem_get_setting('account')
		);
		printf(
			'<p>%s</p>',
			__('Example: username', 'wp-steem')
		);
	}

	/**
	 * Display "posting key" input field
	 *
	 * @since 1.0.0
	 */
	public static function display_posting_key_field() {
		printf(
			'<input type="text" class="regular-text" name="wp_steem_settings[posting_key]" value="%s" autocomplete="off" />',
			wp_steem_get_setting('posting_key')
		);
		printf(
			'<p>%s</p>',
			sprintf(
				__("Please only provide the %s.", 'wp-steem'),
				sprintf(
					'<a style="cursor:pointer" id="posting-key-link">%s</a>',
					__('PRIVATE POSTING KEY', 'wp-steem')
				)
			)
		);
		printf(
      '<div id="private-key-details" class="hidden" style="max-width:800px"><p>%1$s</p><p>%2$s</p><p>%3$s</p><p>%4$s</p><p>%5$s</p></div>',
      sprintf( __('Your Steemit PRIVATE POSTING KEY is %1$s NOT %2$s your main Steemit password.', 'wp-steem'), "<strong>", "</strong>"),
      sprintf( __('Get your PRIVATE POSTING KEY by visiting http://steemit.com/@username/permissions', 'wp-steem')),
      sprintf( __('Click the button that says "show private key".', 'wp-steem')),
      sprintf( __('The revealed key is what needs to be placed in this field.', 'wp-steem')),
      sprintf( '<a href="https://steemit.com/security/@noisy/what-is-the-difference-between-a-password-and-a-private-key-s-on-steemit-how-to-make-your-account-more-secure-by-using-them">%1$s</a> %2$s.',
            __("Click Here", 'wp-steem'), __("for more info on Steemit Keys", 'wp-steem'))
      );
	}

	# Popup

    public static function enqueue_settings_scripts_styles($page) {
      wp_enqueue_script (  'wp-steem-modal' ,       // handle
                          WP_STEEM_DIR_URL . 'public/assets/js/modal.js'  ,       // source
                          array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'), null, true); // dependencies
      wp_enqueue_style (  'wp-jquery-ui-dialog');
    }
	/**
	 * Display "default store" input field
	 *
	 * @since 1.0.2
	 */
	public static function display_default_store_field() {
		printf(
			'<input type="checkbox" name="wp_steem_settings[default_store]" value="1" %s />',
			checked(true, wp_steem_get_setting('default_store'), false)
		);
		printf(
			'<p>%s</p>',
			__('By checking this, the checkbox for "Publish on Steem blockchain" is ticked in the Add New Post screen.', 'wp-steem')
		);
	}

	/**
	 * Display "default update" input field
	 *
	 * @since 1.0.2
	 */
	public static function display_default_update_field() {
		printf(
			'<input type="checkbox" name="wp_steem_settings[default_update]" value="1" %s />',
			checked(true, wp_steem_get_setting('default_update'), false)
		);
		printf(
			'<p>%s</p>',
			__('By checking this, the checkbox for "Update on Steem blockchain" is ticked in the Edit Post screen.', 'wp-steem')
		);
	}

	/**
	 * Display "default tags" input field
	 *
	 * @since 1.0.2
	 */
	public static function display_default_tags_field() {
		printf(
			'<input type="text" class="regular-text" name="wp_steem_settings[default_tags]" value="%s" autocomplete="off" />',
			wp_steem_get_setting('default_tags')
		);
		printf(
			'<p>%s <br> %s</p>',
			__('Separated by a space.', 'wp-steem'),
			__('Example: wordpress wordpress-steem steem blog', 'wp-steem')
		);
	}

	/**
	 * Display "post types" input field
	 *
	 * @since 1.0.2
	 */
	public static function display_post_types_field() {
		$post_types = wp_steem_get_setting('post_types', array());

		if ($_post_types = self::get_post_types()) {
			foreach ($_post_types as $_post_type) {
				printf(
					'<p><label><input type="checkbox" name="wp_steem_settings[post_types][]" value="%1$s" %2$s />%3$s (%1$s)</label></p>',
					$_post_type->name,
					checked(true, in_array($_post_type->name, $post_types), false),
					$_post_type->label
				);
			}
		}
	}

	/**
	 * Display "default include header" input field
	 *
	 * @since 1.0.5
	 */
	public static function display_default_include_header_field() {
		printf(
			'<input type="checkbox" name="wp_steem_settings[include_header]" value="1" %s />',
			checked(true, wp_steem_get_setting('include_header'), false)
		);
		printf(
			'<p>%s</p>',
			__('By checking this, the checkbox for "Include Header" is ticked and "Header" field filled with the content from this field in the Edit Post screen.', 'wp-steem')
		);
	}

	/**
	 * Display "header" input field
	 *
	 * @since 1.0.5
	 */
	public static function display_header_field() {
		printf(
			'<textarea class="regular-text" name="wp_steem_settings[header]" rows="3">%s</textarea>',
			wp_steem_get_setting('header')
		);
	}

	/**
	 * Display "default include footer" input field
	 *
	 * @since 1.0.5
	 */
	public static function display_default_include_footer_field() {
		printf(
			'<input type="checkbox" name="wp_steem_settings[include_footer]" value="1" %s />',
			checked(true, wp_steem_get_setting('include_footer'), false)
		);
		printf(
			'<p>%s</p>',
			__('By checking this, the checkbox for "Include Footer" is ticked and "Footer" field filled with the content from this field in the Edit Post screen.', 'wp-steem')
		);
	}

	/**
	 * Display "footer" input field
	 *
	 * @since 1.0.5
	 */
	public static function display_footer_field() {
		printf(
			'<textarea class="regular-text" name="wp_steem_settings[footer]" rows="3">%s</textarea>',
			wp_steem_get_setting('footer')
		);
	}


	# Internal

	/**
	 * Retrieve post types that are enabled
	 *
	 * @since 1.0.2
	 * @param array $args
	 * @param string $output
	 * @param string $operator
	 * @return array $post_types
	 */
	protected static function get_post_types($args = array(), $output = 'objects', $operator = 'and') {
		$args = wp_parse_args($args, array(
			'public' => true,
		));

		$exclusions = array(
			'attachment',
		);

		if ($post_types = get_post_types($args, $output, $operator)) {
			foreach ($post_types as $post_type_key => $post_type) {
				if ( ! in_array($post_type->name, $exclusions)) {
					continue;
				}

				unset($post_types[$post_type_key]);
			}
		}

		return $post_types;
	}
}

WP_Steem_Settings_Handler::init();