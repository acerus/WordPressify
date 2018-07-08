<?php
/**
 * Function that creates the "Show/Hide the Admin Bar on the Front-End" submenu page
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_show_hide_admin_bar_submenu_page() {
	add_submenu_page( 'profile-builder', __( 'Show/Hide the Admin Bar on the Front-End', 'profile-builder' ), __( 'Admin Bar Settings', 'profile-builder' ), 'manage_options', 'profile-builder-admin-bar-settings', 'wppb_show_hide_admin_bar_content' );
}
add_action( 'admin_menu', 'wppb_show_hide_admin_bar_submenu_page', 4 );


function wppb_generate_admin_bar_default_values( $roles ){
	$wppb_display_admin_settings = get_option( 'wppb_display_admin_settings', 'not_found' );
	
	if ( $wppb_display_admin_settings == 'not_found' ){
        if( !empty( $roles ) ){
            $admin_settings = array();
            foreach ( $roles as $role ){
                if( !empty( $role['name'] ) )
                    $admin_settings[$role['name']] = 'default';
            }

            update_option( 'wppb_display_admin_settings', $admin_settings );
        }
	}
}


/**
 * Function that adds content to the "Show/Hide the Admin Bar on the Front-End" submenu page
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_show_hide_admin_bar_content() {
	global $wp_roles;
	
	wppb_generate_admin_bar_default_values( $wp_roles );
	?>
	
	<div class="wrap wppb-wrap wppb-admin-bar">
	
		<h2><?php _e( 'Admin Bar Settings', 'profile-builder' );?></h2>
		<p><?php _e( 'Choose which user roles view the admin bar in the front-end of the website.', 'profile-builder' ); ?>
		<form method="post" action="options.php#show-hide-admin-bar">
		<?php	
			$admin_bar_settings = get_option( 'wppb_display_admin_settings' );
			settings_fields( 'wppb_display_admin_settings' );
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th class="row-title" scope="col"><?php _e('User-Role', 'profile-builder');?></th>
					<th scope="col"><?php _e('Visibility', 'profile-builder');?></th>
				</tr>
			</thead>
				<tbody>
					<?php
					$alt_i = 0;
					foreach ( $wp_roles->roles as $role ) {
						$alt_i++;
						$key = $role['name'];
						$setting_exists = !empty( $admin_bar_settings[$key] );
						$alt_class = ( ( $alt_i%2 == 0 ) ? ' class="alternate"' : '' );
						
						echo'<tr'.$alt_class.'>
								<td>'.translate_user_role($key).'</td>
								<td>
									<span><input id="rd'.$key.'" type="radio" name="wppb_display_admin_settings['.$key.']" value="default"'.( ( !$setting_exists || $admin_bar_settings[$key] == 'default' ) ? ' checked' : '' ).'/><label for="rd'.$key.'">'.__( 'Default', 'profile-builder' ).'</label></span>
									<span><input id="rs'.$key.'" type="radio" name="wppb_display_admin_settings['.$key.']" value="show"'.( ( $setting_exists && $admin_bar_settings[$key] == 'show') ? ' checked' : '' ).'/><label for="rs'.$key.'">'.__( 'Show', 'profile-builder' ).'</label></span>
									<span><input id="rh'.$key.'" type="radio" name="wppb_display_admin_settings['.$key.']" value="hide"'.( ( $setting_exists && $admin_bar_settings[$key] == 'hide') ? ' checked' : '' ).'/><label for="rh'.$key.'">'.__( 'Hide', 'profile-builder' ).'</label></span>
								</td>
							</tr>';
					}
					?>
				
		</table>

		<div id="wppb_submit_button_div">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" /> 
			</p>
		</div>
		
		</form>
		
	</div>
	<?php
}

/**
 * Function that changes the username on the top right menu (admin bar)
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_replace_username_on_admin_bar( $wp_admin_bar ) {
	$wppb_general_settings = get_option( 'wppb_general_settings' );
	
	if ( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) ){
		$current_user = wp_get_current_user();

		if ( $current_user->ID != 0 ) {
			$my_account_main = $wp_admin_bar->get_node('my-account');
			$new_title1 = str_replace($current_user->display_name, $current_user->user_email, $my_account_main->title);
			$wp_admin_bar->add_node(array('id' => 'my-account', 'title' => $new_title1));

			$my_account_sub = $wp_admin_bar->get_node('user-info');
			$wp_admin_bar->add_node(array('parent' => 'user-actions', 'id' => 'user-info', 'title' => get_avatar($current_user->ID, 64) . "<span class='display-name'>{$current_user->user_email}</span>", 'href' => get_edit_profile_url($current_user->ID), 'meta' => array('tabindex' => -1)));
		}
	}
	
	return $wp_admin_bar;
}
add_filter( 'admin_bar_menu', 'wppb_replace_username_on_admin_bar', 25 );