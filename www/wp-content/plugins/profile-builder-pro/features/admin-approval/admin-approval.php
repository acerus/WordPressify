<?php
function wppb_add_header_script(){
	$total_users = count_users();
	$term = get_term_by('slug', 'unapproved', 'user_status' );
	if( is_object( $term ) && !empty( $term->count ) )
		$unapproved_users = $term->count;
	else
		$unapproved_users = '0';

	?>
	<script type="text/javascript">	
		// script to add an extra link to the users page listing the unapproved users
		jQuery(document).ready(function() {
			jQuery('.wrap ul.subsubsub').append('<span id="separatorID2"> |</span> <li class="listAllUserForBulk"><a class="bulkActionUsers" href="?page=admin_approval&orderby=registered&order=desc"><?php echo str_replace( "'", "&#39;", __( 'Admin Approval', 'profile-builder' ) ); ?> (<?php echo $unapproved_users . '/'. $total_users['total_users']; ?>) </a> </li>');
		});
		
		function confirmAUActionBulk( URL, message, nonce, users, todo ) {
			if (confirm(message)) {
				jQuery.post( ajaxurl, { action:"wppb_handle_bulk_approve_unapprove_cases", URL:URL, todo:todo, users:users, _ajax_nonce:nonce}, function(response) {
					alert(jQuery.trim(response));
					window.location=URL;
				})
			}
		}
	
		// script to create a confirmation box for the user upon approving/unapproving a user
		function confirmAUAction( URL, todo, userID, nonce, actionText ) {
			actionText = '<?php _e( 'Do you want to', 'profile-builder' );?>'+' '+actionText;
		
			if (confirm(actionText)) {
				jQuery.post( ajaxurl, { action:"wppb_handle_approve_unapprove_cases", URL:URL, todo:todo, userID:userID, _ajax_nonce:nonce}, function(response) {
					alert(jQuery.trim(response));
					window.location=URL;
				});			
			}
		}
		
	</script>
<?php
}

function wppb_handle_approve_unapprove_cases(){
	global $current_user;
	global $wpdb;
	
	$todo = sanitize_text_field( $_POST['todo'] );
	$userID = absint( $_POST['userID'] );
	$nonce = trim( $_POST['_ajax_nonce'] );
	
	if (! wp_verify_nonce($nonce, '_nonce_'.$current_user->ID.$userID) )
		die( __( 'Your session has expired! Please refresh the page and try again.', 'profile-builder' ) );
	
	if ( current_user_can( 'delete_users' ) ){
		if ( ( $todo != '' ) && ( $userID != '' ) ){
		
			if ( $todo == 'approve' ){					
				wp_set_object_terms( $userID, NULL, 'user_status' );
				clean_object_term_cache( $userID, 'user_status' );

                // now that the user is approved, remove approval link key from usermeta
                delete_user_meta( $userID, '_wppb_admin_approval_link_param');
				
				do_action( 'wppb_after_user_approval', $userID );
				
				wppb_send_new_user_status_email( $userID, 'approved' );
				
				die( __( "User successfully approved!", "profile-builder" ) );
				
			}elseif ( $todo == 'unapprove' ){
				wp_set_object_terms( $userID, array( 'unapproved' ), 'user_status', false );
				clean_object_term_cache( $userID, 'user_status' );

				do_action( 'wppb_after_user_unapproval', $userID );

				wppb_send_new_user_status_email( $userID, 'unapproved' );
				
				die( __( "User successfully unapproved!", "profile-builder" ) );

			}elseif ( $todo == 'delete' ){
				require_once( ABSPATH.'wp-admin/includes/user.php' );
				wp_delete_user( $userID );
				
				die( __( "User successfully deleted!", "profile-builder" ) );
			}
		}
		
	}else
		die(__("You either don't have permission for that action or there was an error!", "profile-builder"));
}

function wppb_handle_bulk_approve_unapprove_cases(){
	global $current_user;
	
	$todo = sanitize_text_field($_POST['todo']);
	$users = array_map( 'absint', explode(',', trim( $_POST['users'] ) ) );
	$nonce = trim($_POST['_ajax_nonce']);

	if (! wp_verify_nonce($nonce, '_nonce_'.$current_user->ID.'_bulk') )
		die(__( "Your session has expired! Please refresh the page and try again.", "profile-builder" ));

	if (current_user_can('delete_users')){
		if (($todo != '') && (is_array($users)) && !empty( $users ) ){
			if( $todo === 'bulkApprove' ){
				foreach( $users as $user ){
                    if ($current_user->ID != $user){
                        wp_set_object_terms( $user, NULL, 'user_status' );
                        clean_object_term_cache( $user, 'user_status' );
                        wppb_send_new_user_status_email( $user, 'approved' );
						do_action('wppb_after_user_approval', $user );
                    }
                }
                die( __( "Users successfully approved!", "profile-builder" ) );
			}elseif ($todo === 'bulkUnapprove'){
				foreach( $users as $user ){
                    if ($current_user->ID != $user ){
                        wp_set_object_terms( $user, array( 'unapproved' ), 'user_status', false);
                        clean_object_term_cache( $user, 'user_status' );
                        wppb_send_new_user_status_email( $user, 'unapproved' );
						do_action('wppb_after_user_unapproval', $user );
                    }
				}
				die(__("Users successfully unapproved!", "profile-builder"));
			}elseif( $todo === 'bulkDelete' ){
				require_once(ABSPATH.'wp-admin/includes/user.php');
				foreach( $users as $user ){
					if ($current_user->ID != $user ){
						wp_delete_user( $user );
					}
				}
				die(__("Users successfully deleted!", "profile-builder"));
			}
		}
    }else
        die(__("You either don't have permission for that action or there was an error!", "profile-builder"));
}

function wppb_send_new_user_status_email($userID, $newStatus){
	$wppb_general_settings = get_option( 'wppb_general_settings' );
	$user_info = get_userdata($userID);

	if( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) ) {
		$user_login = $user_info->user_email;
	} else {
		$user_login = $user_info->user_login;
	}

	$userMessageFrom = apply_filters( 'wppb_new_user_status_from_email_content', get_bloginfo( 'name' ), $userID, $newStatus );

	if ( $newStatus == 'approved' ){
		$userMessageSubject = sprintf( __( 'Your account on %1$s has been approved!', 'profile-builder' ), get_bloginfo( 'name' ) );
		$userMessageSubject = apply_filters( 'wppb_new_user_status_subject_approved', $userMessageSubject, $user_info, __( 'approved', 'profile-builder' ), $userMessageFrom, 'wppb_user_emailc_admin_approval_notif_approved_email_subject' );

		$userMessageContent = sprintf( __( 'An administrator has just approved your account on %1$s (%2$s).', 'profile-builder' ), get_bloginfo( 'name' ), $user_login );
		$userMessageContent = apply_filters('wppb_new_user_status_message_approved', $userMessageContent, $user_info, __( 'approved', 'profile-builder' ), $userMessageFrom, 'wppb_user_emailc_admin_approval_notif_approved_email_content' );

		$userMessage_context = 'email_user_approved';
	}elseif ( $newStatus == 'unapproved' ){
		$userMessageSubject = sprintf( __( 'Your account on %1$s has been unapproved!', 'profile-builder'), get_bloginfo( 'name' ));
		$userMessageSubject = apply_filters( 'wppb_new_user_status_subject_unapproved', $userMessageSubject, $user_info, __( 'unapproved', 'profile-builder' ), $userMessageFrom, 'wppb_user_emailc_admin_approval_notif_unapproved_email_subject' );

		$userMessageContent = sprintf( __( 'An administrator has just unapproved your account on %1$s (%2$s).', 'profile-builder' ), get_bloginfo( 'name' ), $user_login );
		$userMessageContent = apply_filters( 'wppb_new_user_status_message_unapproved', $userMessageContent, $user_info, __( 'unapproved', 'profile-builder' ), $userMessageFrom, 'wppb_user_emailc_admin_approval_notif_unapproved_email_content' );

		$userMessage_context = 'email_user_unapproved';
	}

	wppb_mail( $user_info->user_email, $userMessageSubject, $userMessageContent, $userMessageFrom, $userMessage_context );
}

// function to register the new user_status taxonomy for the admin approval
function wppb_register_user_status_taxonomy() {

	register_taxonomy('user_status','user',array('public' => false));
}

// function to create a new wp error in case the admin approval feature is active and the given user is still unapproved
function wppb_unapproved_user_admin_error_message_handler($userdata, $password){

	if (wp_get_object_terms( $userdata->ID, 'user_status' )){
		$errorMessage = __('<strong>ERROR</strong>: Your account has to be confirmed by an administrator before you can log in.', 'profile-builder');
	
		return new WP_Error('wppb_unapproved_user_admin_error_message', $errorMessage);
	}else

		return $userdata;
}

// function to prohibit user from using the default wp password recovery feature
function wppb_unapproved_user_password_recovery( $allow, $userID ){

	if (wp_get_object_terms( $userID, 'user_status' ))
		return new WP_Error( 'wppb_no_password_reset', __( 'Your account has to be confirmed by an administrator before you can use the "Password Recovery" feature.', 'profile-builder' ) );
	else
		return true;
}

// function to add the "unapproved" status for the user who just registered using the WP registration form (only if the admin approval feature is active)
function wppb_update_user_status_on_admin_registration( $user_id ){
    if( ! current_user_can( 'delete_users' ) ) {
        $user_data = get_userdata($user_id);

        $wppb_generalSettings = get_option('wppb_general_settings', 'not_found');

        if ($wppb_generalSettings != 'not_found' && !empty($wppb_generalSettings['adminApprovalOnUserRole'])) {
            foreach ($user_data->roles as $role) {
                if (in_array($role, $wppb_generalSettings['adminApprovalOnUserRole'])) {
                    wp_set_object_terms($user_id, array('unapproved'), 'user_status', false);
                    clean_object_term_cache($user_id, 'user_status');
                    // save admin approval email link unique parameter ( used for outputting Admin Email Customizer {{{approve_link}}} or {{approve_url}} tags )
                    add_user_meta( $user_id, '_wppb_admin_approval_link_param', wppb_get_admin_approval_email_link_key($user_id) );


                } else {
                    add_filter('wppb_register_success_message', 'wppb_noAdminApproval_successMessage');
                }
            }
        } else {
            wp_set_object_terms($user_id, array('unapproved'), 'user_status', false);
            clean_object_term_cache($user_id, 'user_status');
            // save admin approval email link unique parameter ( used for outputting Admin Email Customizer {{{approve_link}}} or {{approve_url}} tags )
            add_user_meta( $user_id, '_wppb_admin_approval_link_param', wppb_get_admin_approval_email_link_key($user_id) );
        }
    }
}

function wppb_noAdminApproval_successMessage() {
	return __( "Your account has been successfully created!", 'profile-builder' );
}

/**
 * Function that returns the key (hash value) used for enabling user approval by the admin directly from email, by clicking a specifically formed link (which contains the hash value)
 *
 * @param int $userID The ID of the user pending approval
 *
 * @return string $key to be appended to the admin approval email link created by using Admin Email Customizer {{approve_url}} or {{{approve_link}}} tags
 */
function wppb_get_admin_approval_email_link_key( $userID ){

    $user_info = get_userdata($userID);

    $data = $userID . $user_info->user_email . get_site_url() . time() ;

    $key = hash_hmac( 'sha256' , $data, $user_info->user_email . time() );

    return $key;
}

/**
 * Function that listens and handles the admin approval of users from email, which is done by clicking a specifically formed link
 *
 */
function wppb_approve_user_from_email_url_listener(){

    if( !isset( $_GET['pbapprove'] ) ){
        return;
    }

    global $wpdb;

    //search db to see if there's any identical key saved in _usermeta and get that user id
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='_wppb_admin_approval_link_param' AND meta_value =%s", $_GET['pbapprove']), ARRAY_N );

    // check if we got a match
    if ( !empty($results[0][0]) ) {

        //approve user by removing 'unnaprove' term
        $userID = intval($results[0][0]);
        wp_set_object_terms( $userID, NULL, 'user_status' );
        clean_object_term_cache( $userID, 'user_status' );

        do_action( 'wppb_after_user_approval', $userID );

        // send email notifying the user
        wppb_send_new_user_status_email( $userID, 'approved' );

        // now that the user is approved, remove approval link key from usermeta
        delete_user_meta( $userID, '_wppb_admin_approval_link_param');

        $message = apply_filters( 'wppb_approve_user_from_email_success_message', __( 'User successfully approved!', 'profile-builder' ), $userID);

        wp_die( $message, 'Admin Approval Successful' );
    }

    // no user was found that has the same hash key

    // build the admin approval from backend link (when admin is logged in)
    $admin_approval_url = add_query_arg(
                         array(
                                'page'      => 'admin_approval',
                                'orderby'   => 'registered',
                                'order'     => 'desc'
                         ),
                        admin_url('users.php')
    );
    $message = sprintf( wp_kses( __( 'The approval link is not valid! Please <a href="%s"> log in </a> to approve the user manually. ', 'profile-builder'), array(  'a' => array( 'href' => array() ) ) ), esc_url( $admin_approval_url ) );

    $message = apply_filters('wppb_approve_user_from_email_error_message', $message);
    wp_die( $message , 'Admin Approval Unsuccessful' );

}
add_action('wp_loaded', 'wppb_approve_user_from_email_url_listener');



// Set up the AJAX hooks
add_action( 'wp_ajax_wppb_handle_approve_unapprove_cases', 'wppb_handle_approve_unapprove_cases' );
add_action( 'wp_ajax_wppb_handle_bulk_approve_unapprove_cases', 'wppb_handle_bulk_approve_unapprove_cases' );

	
$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
if( $wppb_generalSettings != 'not_found' )
	if( !empty( $wppb_generalSettings['adminApproval'] ) && ( $wppb_generalSettings['adminApproval'] == 'yes' ) ){
		if ( is_multisite() ){
			if ( strpos( $_SERVER['SCRIPT_NAME'], 'users.php' ) ){  //global $pagenow doesn't seem to work
				add_action( 'admin_head', 'wppb_add_header_script' );
			}
		}else{
			global $pagenow;
		
			if ( $pagenow == 'users.php' ){
				add_action( 'admin_head', 'wppb_add_header_script' );
			}
		}
		
		add_action( 'init', 'wppb_register_user_status_taxonomy', 1 );
		add_filter( 'wp_authenticate_user', 'wppb_unapproved_user_admin_error_message_handler', 10, 2 );
		add_filter( 'allow_password_reset', 'wppb_unapproved_user_password_recovery', 10, 2 );
		add_action( 'user_register', 'wppb_update_user_status_on_admin_registration' );

		/* when deleting a user delete the taxonomy as well */
		add_action( 'deleted_user', 'wppb_remove_unapproved_term_from_db_when_deleting_user' );
		function wppb_remove_unapproved_term_from_db_when_deleting_user( $id )
		{
			wp_remove_object_terms( $id, 'unapproved', 'user_status' );
		}

		add_action( 'load-users.php', 'wppb_delete_user_status_scraps_from_db' );
		function wppb_delete_user_status_scraps_from_db() {
			$cleaned_up = get_option( 'wppb_cleaned_up_user_status_taxonomy_from_db' );
			if( !$cleaned_up ) {
				global $wpdb;

				$all_user_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users");
				if (!empty($all_user_ids)) {
					$all_user_ids = implode(',', $all_user_ids);

					$term = get_term_by('name', 'unapproved', 'user_status');
					if (!empty($term->term_taxonomy_id)) {
						$deleted = $wpdb->query("DELETE $wpdb->term_relationships FROM $wpdb->term_relationships WHERE $wpdb->term_relationships.term_taxonomy_id = $term->term_taxonomy_id AND $wpdb->term_relationships.object_id NOT IN ($all_user_ids)");
						wp_update_term_count_now(array($term->term_taxonomy_id), 'user_status');
						update_option('wppb_cleaned_up_user_status_taxonomy_from_db', 'done');
					}
				}
			}
		}
	}


