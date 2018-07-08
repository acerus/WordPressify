<?php
/**
 * Add Notifications tab to account page
 *
 * @param array $tabs
 * @return array
 */
function um_followers_account_notification_tab( $tabs ) {

	if ( empty( $tabs[400]['notifications'] ) ) {
		$tabs[400]['notifications'] = array(
			'icon'          => 'um-faicon-envelope',
			'title'         => __( 'Notifications', 'um-followers' ),
			'submit_title'  => __( 'Update Notifications', 'um-followers' ),
		);
	}

	return $tabs;
}
add_filter( 'um_account_page_default_tabs_hook', 'um_followers_account_notification_tab', 10, 1 );


/**
 * Show followers notifications in account
 *
 * @param $output
 * @param $shortcode_args
 * @return string
 */
function um_followers_account_tab( $output, $shortcode_args ) {

	if ( isset( $shortcode_args['_enable_new_follow'] ) && 0 == $shortcode_args['_enable_new_follow'] )
		return $output;

	$_enable_new_follow = UM()->Followers_API()->api()->enabled_email( get_current_user_id() );

	$fields['_enable_new_follow'] = array(
		'meta_key' => '_enable_new_follow'
	);
	$fields = apply_filters('um_account_secure_fields', $fields, 'notifications' );

	ob_start(); ?>

	<div class="um-field-area">
		<label class="um-field-checkbox <?php if ( ! empty( $_enable_new_follow ) ) { ?>active<?php } ?>">
			<input type="checkbox" name="_enable_new_follow" value="1" <?php checked( ! empty( $_enable_new_follow ) ) ?> />
			<span class="um-field-checkbox-state"><i class="um-icon-android-checkbox-<?php if ( ! empty( $_enable_new_follow ) ) { ?>outline<?php } else { ?>outline-blank<?php } ?>"></i></span>
			<span class="um-field-checkbox-option"><?php echo __('I\'m followed by someone new','um-followers'); ?></span>
		</label>

		<div class="um-clear"></div>

	</div>

	<?php $output .= ob_get_clean();

	return $output;
}
add_filter('um_account_content_hook_notifications', 'um_followers_account_tab', 50, 2 );