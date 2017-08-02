<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Steem_Settings_Handler {

	public static function init() {
		$instance = __CLASS__;

		add_action('admin_notices', array($instance, 'display_notices'));

		add_action('admin_menu', array($instance, 'register_pages'));
		add_action('admin_init', array($instance, 'register_page_settings'));
		add_action('admin_enqueue_scripts', array($instance, 'enqueue_settings_scripts_styles'));
	}

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

	public static function register_page_settings() {
		$instance = __CLASS__;

		register_setting(
			'wp_steem_settings',
			'wp_steem_settings',
			array($instance, 'sanitize')
		);

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
	}

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
							sprintf('<a href="%s" target="_blank">%s</a>', 'https://steemit.com/@recrypto', "@recrypto"),
							sprintf('<a href="%s" target="_blank">%s</a>', 'https://steemit.com/@recrypto', "@recrypto's")
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
}

WP_Steem_Settings_Handler::init();