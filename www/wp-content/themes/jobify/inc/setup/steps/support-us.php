<p><?php _e( 'Help improve Jobify by submitting a rating and helping to translate the theme to as many languages as possible!', 'jobify' ); ?></p>

<p>
	<a href="http://astoundify.com/go/rate-jobify" class="button button-primary"><?php _e( 'Leave a Positive Rating', 'jobify' ); ?></a>
	<a href="http://astoundify.com/go/translate-jobify" class="button button-secondary"><?php _e( 'Translate Jobify', 'jobify' ); ?></a>
</p>

<?php if ( ! get_option( 'astoundify_setup_guide_hidden', false ) ) : ?>
<p>
	<a href="<?php echo esc_url( Astoundify_Setup_Guide::get_hide_menu_item_url() ); ?>"><em><?php _e( 'Move this guide under the "Appearance" menu item', 'jobify' ); ?></em></a>
</p>
<?php endif; ?>
