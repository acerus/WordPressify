<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add a message button to directory
 *
 * @param $user_id
 * @param $args
 */
function um_messaging_button_in_directory( $user_id, $args ) {
	if ( isset( $args['show_pm_button'] ) && !$args['show_pm_button'] ) return;
	if ( $user_id == get_current_user_id() ) {
		$messages_link = add_query_arg( 'profiletab', 'messages', um_user_profile_url() );
		echo '<a href="' . $messages_link . '" class="um-message-abtn um-button"><span>'. __('My messages','um-messaging'). '</span></a>';
	} else {
		echo do_shortcode('[ultimatemember_message_button user_id='.$user_id.']');
	}
}
add_action('um_members_just_after_name', 'um_messaging_button_in_directory', 110, 2 );


/**
 * Open modal if $_SESSION is not empty
 */
function um_messaging_open_modal(){

	if ( ! empty( $_COOKIE['UMTestCookie'] ) &&  ! empty( $_POST ) ) {
		$data = json_decode( wp_unslash( $_COOKIE['UMTestCookie'] ), true ); ?>

		<script type="text/javascript">
			setTimeout( function(){
				<?php $message_to = $data['message_to']; ?>
				jQuery('.um-message-btn[data-message_to="<?php echo $message_to; ?>"]')[0].click();
			},1000) ;
		</script>

	<?php }

	if ( ! is_user_logged_in() ) {
		return;
	}

	if ( ! isset( $_SESSION["um_messaging_message_to"] ) ) {
		return;
	} ?>

	<script type="text/javascript">
		jQuery('document').ready( function(){
			<?php $message_to = $_SESSION["um_messaging_message_to"]; ?>
			setTimeout( function(){
				jQuery('.um-message-btn[data-message_to="<?php echo $message_to; ?>"]')[0].click();
			},1000) ;

		});
	</script>

	<?php unset( $_SESSION["um_messaging_message_to"] );
}
add_action( 'wp_footer', 'um_messaging_open_modal' );


/**
 * Delete messages on user delete
 *
 * @param $user_id
 */
function um_delete_user_messages( $user_id ) {
	//Update with delete old messages conversations
	global $wpdb;

	$conversations = UM()->Messaging_API()->api()->table_name1;
	$messages = UM()->Messaging_API()->api()->table_name2;

	$wpdb->query( $wpdb->prepare(
		"DELETE
	    FROM {$conversations}
	    WHERE user_a = %d OR
	          user_b = %d",
		$user_id,
		$user_id
	) );

	$wpdb->query( $wpdb->prepare(
		"DELETE
	    FROM {$messages}
	    WHERE recipient = %d OR
	          author = %d",
		$user_id,
		$user_id
	) );
}
add_action( 'um_delete_user', 'um_delete_user_messages', 10, 1 );


/**
 * @param $user_id
 */
function remove_error_form_coockie( $user_id ) {
	if ( isset( $_COOKIE['um_messaging_invite_login'] ) ) {
		unset( $_COOKIE['um_messaging_invite_login'] );
		setcookie( "um_messaging_invite_login", null, -1, '/' );
	}
}
add_action( 'um_on_login_before_redirect', 'remove_error_form_coockie' );


/**
 * @param $data
 */
function add_error_form_cookie( $data ) {
	if( ! empty( $_POST ) ) {
		setcookie( "um_messaging_invite_login", json_encode( $_POST ), time()+3600, '/' );
	}
}
add_action( 'um_user_login_extra_hook', 'add_error_form_cookie' );