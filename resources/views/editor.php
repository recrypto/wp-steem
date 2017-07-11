<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="wp-steem-editor-container" class="wp-editor-container" style="display: none">
	<p>
		<label>
			<input type="checkbox" name="wp_steem[use_body]" value="1" <?php checked($steem_post->use_body, true); ?> />
			<?php _e('Use this content for the Steem post body to appear on Steem blockchain.', 'wp-steem'); ?>
		</label>
	</p>

	<p>
		<?php 
			printf(
				'<strong>%s</strong> %s', 
				__('Note:', 'wp-steem'),
				__('Placing WordPress shortcodes do not work on the Markdown editor.', 'wp-steem')
			); 
		?>
	</p>

	<hr>

	<textarea class="wp-editor-area" name="wp_steem[body]" cols="40" rows="10" autocomplete="off"><?php echo $steem_post->get_meta('body'); ?></textarea>
</div>