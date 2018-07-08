<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add Notifications tab to account page
 *
 * @param array $tabs
 * @return array
 */
function um_messaging_account_notification_tab( $tabs ) {

	if ( empty( $tabs[400]['notifications'] ) ) {
		$tabs[400]['notifications'] = array(
			'icon'          => 'um-faicon-envelope',
			'title'         => __( 'Notifications', 'um-messaging' ),
			'submit_title'  => __( 'Update Notifications', 'um-messaging' ),
		);
	}

	return $tabs;
}
add_filter( 'um_account_page_default_tabs_hook', 'um_messaging_account_notification_tab', 10, 1 );


/**
 * Show a notification option in email tab
 *
 *
 * @param $output
 * @param $shortcode_args
 * @return string
 */
function um_messaging_account_tab( $output, $shortcode_args ) {

	if ( isset( $shortcode_args['_enable_new_pm'] ) && 0 == $shortcode_args['_enable_new_pm'] )
		return $output;

	$_enable_new_pm = UM()->Messaging_API()->api()->enabled_email( get_current_user_id() );

	ob_start(); ?>

	<div class="um-field-area">
		<label class="um-field-checkbox <?php if ( ! empty( $_enable_new_pm ) ) { ?>active<?php } ?>">
			<input type="checkbox" name="_enable_new_pm" value="1" <?php checked( ! empty( $_enable_new_pm ) ) ?> />
			<span class="um-field-checkbox-state"><i class="um-icon-android-checkbox-<?php if ( ! empty( $_enable_new_pm ) ) { ?>outline<?php } else { ?>outline-blank<?php } ?>"></i></span>
			<span class="um-field-checkbox-option"><?php echo __( 'Someone sends me a private message', 'um-messaging' ); ?></span>
		</label>

		<div class="um-clear"></div>

	</div>

	<?php $output .= ob_get_clean();

	return $output;
}
add_filter('um_account_content_hook_notifications', 'um_messaging_account_tab', 46, 2 );