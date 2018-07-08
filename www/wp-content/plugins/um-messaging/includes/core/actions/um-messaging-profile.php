<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/***
***	@customize the nav bar
***/
function um_messaging_add_profile_bar( $args ) {
    $user_id = um_profile_id();

    if ( ! is_user_logged_in() || get_current_user_id() != $user_id ) {
        echo '<div class="um-messaging-btn">' . do_shortcode( '[ultimatemember_message_button user_id='.$user_id.']' ) . '</div>';
    }
}
add_action('um_profile_navbar', 'um_messaging_add_profile_bar', 4 );


/**
 * @param string $classes
 * @return string
 */
function um_messaging_profile_navbar_classes( $classes ) {
	$classes .= " um-messaging-bar";
	return $classes;
}
add_filter( "um_profile_navbar_classes", 'um_messaging_profile_navbar_classes', 10, 1 );