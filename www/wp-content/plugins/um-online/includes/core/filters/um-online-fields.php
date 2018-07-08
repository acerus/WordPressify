<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Extends core fields
 *
 * @param array $fields
 *
 * @return array
 */
function um_online_add_fields( $fields ) {

	$fields['_hide_online_status'] = array(
		'metakey'       => '_hide_online_status',
		'type'          => 'online_field',
		'show_anyway'   => true,
		'custom'        => true,
		'account_only'  => true
	);

	$fields = apply_filters( 'um_account_secure_fields', $fields, '_hide_online_status' );

	$fields['online_status'] = array(
		'title'             => __( 'Online Status', 'um-online' ),
		'metakey'           => 'online_status',
		'type'              => 'text',
		'label'             => __( 'Online Status', 'um-online' ),
		'edit_forbidden'    => 1,
		'show_anyway'       => true,
		'custom'            => true,
	);

	return $fields;
}
add_filter( 'um_predefined_fields_hook', 'um_online_add_fields', 100 );


/**
 * Shows the online field in account page
 *
 * @param string $args
 * @param array $shortcode_args
 *
 * @return string
 */
function um_activity_account_online_fields( $args, $shortcode_args ) {
	$args = $args . ',_hide_online_status';
	return $args;
}
add_filter( 'um_account_tab_privacy_fields', 'um_activity_account_online_fields', 10, 2 );


/**
 * Shows the online status
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_online_show_status( $value, $data ) {
	if ( UM()->Online_API()->is_online( um_user('ID') ) ) {
		$output = '<span class="um-online-status online">' . __( 'online', 'um-online' ) . '</span>';
	} else {
		$output = '<span class="um-online-status offline">' . __( 'offline', 'um-online' ) . '</span>';
	}

	return $output;
}
add_filter( 'um_profile_field_filter_hook__online_status', 'um_online_show_status', 99, 2 );


/**
 * Add account privacy setting to control online status
 *
 * @param $output
 *
 * @return string
 */
function um_online_privacy_setting( $output ) {
	ob_start(); ?>

	<div class="um-field" data-key="">

		<div class="um-field-label">
			<label for="hide_online_status"><?php _e('Show my online status?','um-online'); ?></label>
			<span class="um-tip um-tip-w" title="<?php _e('Do you want other people to see that you are online?','um-online'); ?>"><i class="um-icon-help-circled"></i></span>
			<div class="um-clear"></div>
		</div>

		<div class="um-field-area">

			<?php $active = get_user_meta( get_current_user_id(), '_hide_online_status', true ) == 1 ? true : false; ?>

			<label class="um-field-radio <?php if ( ! $active ) { ?>active<?php } ?> um-field-half">
				<input type="radio" name="_hide_online_status" value="0" <?php checked( ! $active ) ?>/>
				<span class="um-field-radio-state">
					<i class="um-icon-android-radio-button-<?php if ( ! $active ) { ?>on<?php } else { ?>off<?php } ?>"></i>
				</span>
				<span class="um-field-radio-option"><?php _e('Yes','um-online'); ?></span>
			</label>
			<label class="um-field-radio <?php if ( $active ) { ?>active<?php } ?> um-field-half right">
				<input type="radio" name="_hide_online_status" value="1" <?php checked( $active ) ?> />
				<span class="um-field-radio-state">
					<i class="um-icon-android-radio-button-<?php if ( $active ) { ?>on<?php } else { ?>off<?php } ?>"></i>
				</span>
				<span class="um-field-radio-option"><?php _e('No','um-online'); ?></span>
			</label>

			<div class="um-clear"></div>

			<div class="um-clear"></div>

		</div>

	</div>

	<?php $output .= ob_get_clean();
	return $output;
}
add_filter( 'um_edit_field_account_online_field', 'um_online_privacy_setting', 10, 1 );