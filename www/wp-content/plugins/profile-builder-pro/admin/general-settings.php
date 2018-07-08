<?php
/**
 * Function that creates the "General Settings" submenu page
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_register_general_settings_submenu_page() {
	add_submenu_page( 'profile-builder', __( 'General Settings', 'profile-builder' ), __( 'General Settings', 'profile-builder' ), 'manage_options', 'profile-builder-general-settings', 'wppb_general_settings_content' );
}
add_action( 'admin_menu', 'wppb_register_general_settings_submenu_page', 3 );


function wppb_generate_default_settings_defaults(){
	add_option( 'wppb_general_settings', array( 'extraFieldsLayout' => 'default', 'emailConfirmation' => 'no', 'activationLandingPage' => '', 'adminApproval' => 'no', 'loginWith' => 'usernameemail', 'rolesEditor' => 'no', 'contentRestriction' => 'no' ) );
}


/**
 * Function that adds content to the "General Settings" submenu page
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_general_settings_content() {
	wppb_generate_default_settings_defaults();
?>	
	<div class="wrap wppb-wrap">
	<form method="post" action="options.php#general-settings">
	<?php $wppb_generalSettings = get_option( 'wppb_general_settings' ); ?>
	<?php settings_fields( 'wppb_general_settings' ); ?>

	<h2><?php _e( 'General Settings', 'profile-builder' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row">
				<?php _e( "Load Profile Builder's own CSS file in the front-end:", "profile-builder" ); ?>
			</th>
			<td>
				<label><input type="checkbox" name="wppb_general_settings[extraFieldsLayout]"<?php echo ( ( isset( $wppb_generalSettings['extraFieldsLayout'] ) && ( $wppb_generalSettings['extraFieldsLayout'] == 'default' ) ) ? ' checked' : '' ); ?> value="default" class="wppb-select"><?php _e( 'Yes', 'profile-builder' ); ?></label>
				<ul>
					<li class="description"><?php printf( __( 'You can find the default file here: %1$s', 'profile-builder' ), '<a href="'.dirname( plugin_dir_url( __FILE__ ) ).'/assets/css/style-front-end.css" target="_blank">'.dirname( dirname( plugin_basename( __FILE__ ) ) ).'\assets\css\style-front-end.css</a>' ); ?></li>
				</ul>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php _e( '"Email Confirmation" Activated:', 'profile-builder' );?>
			</th>
			<td>
				<select name="wppb_general_settings[emailConfirmation]" class="wppb-select" id="wppb_settings_email_confirmation" onchange="wppb_display_page_select(this.value)">
					<option value="yes" <?php if ( $wppb_generalSettings['emailConfirmation'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'profile-builder' ); ?></option>
					<option value="no" <?php if ( $wppb_generalSettings['emailConfirmation'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'profile-builder' ); ?></option>
				</select>
				<ul>
				    <li class="description"><?php _e( 'This works with front-end forms only. Recommended to redirect WP default registration to a Profile Builder one using "Custom Redirects" module.', 'profile-builder' ); ?></li>
				    <?php if ( $wppb_generalSettings['emailConfirmation'] == 'yes' ) { ?>
					    <li class="description dynamic1"><?php printf( __( 'You can find a list of unconfirmed email addresses %1$sUsers > All Users > Email Confirmation%2$s.', 'profile-builder' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/users.php?page=unconfirmed_emails">', '</a>' )?></li>
                    <?php } ?>
				</ul>
			</td>
		</tr>

		<tr id="wppb-settings-activation-page">
			<th scope="row">
				<?php _e( '"Email Confirmation" Landing Page:', 'profile-builder' ); ?>
			</th>
			<td>
				<select name="wppb_general_settings[activationLandingPage]" class="wppb-select">
					<option value="" <?php if ( empty( $wppb_generalSettings['emailConfirmation'] ) ) echo 'selected'; ?>></option>
					<optgroup label="<?php _e( 'Existing Pages', 'profile-builder' ); ?>">
					<?php
						$pages = get_pages( apply_filters( 'wppb_page_args_filter', array( 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page', 'post_status' => array( 'publish' ) ) ) );
						
						foreach ( $pages as $key => $value ){
							echo '<option value="'.$value->ID.'"';
							if ( $wppb_generalSettings['activationLandingPage'] == $value->ID )
								echo ' selected';

							echo '>' . $value->post_title . '</option>';
						}
					?>
					</optgroup>
				</select>
				<p class="description">
					<?php _e( 'Specify the page where the users will be directed when confirming the email account. This page can differ from the register page(s) and can be changed at any time. If none selected, a simple confirmation page will be displayed for the user.', 'profile-builder' ); ?>
				</p>
			</td>
		</tr>


	<?php
	if ( file_exists( WPPB_PLUGIN_DIR.'/features/admin-approval/admin-approval.php' ) ){
	?>
		<tr>
			<th scope="row">
				<?php _e( '"Admin Approval" Activated:', 'profile-builder' ); ?>
			</th>
			<td>
				<select id="adminApprovalSelect" name="wppb_general_settings[adminApproval]" class="wppb-select" onchange="wppb_display_page_select_aa(this.value)">
					<option value="yes" <?php if( !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'profile-builder' ); ?></option>
					<option value="no" <?php if( !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'profile-builder' ); ?></option>
				</select>
				<ul>
					<li class="description dynamic2"><?php printf( __( 'You can find a list of users at %1$sUsers > All Users > Admin Approval%2$s.', 'profile-builder' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/users.php?page=admin_approval&orderby=registered&order=desc">', '</a>' )?></li>
				<ul>
			</td>
		</tr>

		<tr class="dynamic2">
			<th scope="row">
				<?php _e( '"Admin Approval" on User Role:', 'profile-builder' ); ?>
			</th>
			<td>
				<div id="wrap">
					<?php
					$wppb_userRoles = wppb_adminApproval_onUserRole();

					if( ! empty( $wppb_userRoles ) ) {
						foreach( $wppb_userRoles as $role => $role_name ) {
							echo '<label><input type="checkbox" id="adminApprovalOnUserRoleCheckbox" name="wppb_general_settings[adminApprovalOnUserRole][]" class="wppb-checkboxes" value="' . esc_attr( $role ) . '"';
							if( ! empty( $wppb_generalSettings['adminApprovalOnUserRole'] ) && in_array( $role, $wppb_generalSettings['adminApprovalOnUserRole'] ) )	echo ' checked';
							if( empty( $wppb_generalSettings['adminApprovalOnUserRole'] ) )		echo ' checked';
							echo '>';
							echo $role_name . '</label><br>';
						}
					}
					?>
				</div>
				<ul>
					<li class="description"><?php printf( __( 'Select on what user roles to activate Admin Approval.', 'profile-builder' ) ) ?></li>
					<ul>
			</td>
		</tr>

	<?php } ?>

	<?php
		if( file_exists( WPPB_PLUGIN_DIR.'/features/roles-editor/roles-editor.php' ) ) {
			?>
			<tr>
				<th scope="row">
					<?php _e( '"Roles Editor" Activated:', 'profile-builder' ); ?>
				</th>
				<td>
					<select id="rolesEditorSelect" name="wppb_general_settings[rolesEditor]" class="wppb-select" onchange="wppb_display_page_select_re(this.value)">
						<option value="no" <?php if( !empty( $wppb_generalSettings['rolesEditor'] ) && $wppb_generalSettings['rolesEditor'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'profile-builder' ); ?></option>
						<option value="yes" <?php if( !empty( $wppb_generalSettings['rolesEditor'] ) && $wppb_generalSettings['rolesEditor'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'profile-builder' ); ?></option>
					</select>
					<ul>
						<li class="description dynamic3"><?php printf( __( 'You can add / edit user roles at %1$sUsers > Roles Editor%2$s.', 'profile-builder' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/edit.php?post_type=wppb-roles-editor">', '</a>' )?></li>
					<ul>
				</td>
			</tr>
	<?php } ?>

    <?php
        if( file_exists( WPPB_PLUGIN_DIR.'/features/content-restriction/content-restriction.php' ) ) {
            ?>
            <tr>
                <th scope="row">
                    <?php _e( '"Content Restriction" Activated:', 'profile-builder' ); ?>
                </th>
                <td>
                    <select id="contentRestrictionSelect" name="wppb_general_settings[contentRestriction]" class="wppb-select" onchange="wppb_display_page_select_cr(this.value)">
                        <option value="no" <?php if( !empty( $wppb_generalSettings['contentRestriction'] ) && $wppb_generalSettings['contentRestriction'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'profile-builder' ); ?></option>
                        <option value="yes" <?php if( !empty( $wppb_generalSettings['contentRestriction'] ) && $wppb_generalSettings['contentRestriction'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'profile-builder' ); ?></option>
                    </select>
                    <ul>
                        <li class="description dynamic4"><?php printf( __( 'Set your settings at %1$sProfile Builder > Content Restriction%2$s and use each page / post / custom post type individual meta-box to restrict content.', 'profile-builder' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/admin.php?page=profile-builder-content_restriction">', '</a>' )?></li>
                    <ul>
                </td>
            </tr>
    <?php } ?>

	<?php
	if ( PROFILE_BUILDER == 'Profile Builder Free' ) {
	?>
		<tr>
			<th scope="row">
				<?php _e( '"Admin Approval" Feature:', 'profile-builder' ); ?>
			</th>
			<td>
				<p><em>	<?php printf( __( 'You decide who is a user on your website. Get notified via email or approve multiple users at once from the WordPress UI. Enable Admin Approval by upgrading to %1$sHobbyist or PRO versions%2$s.', 'profile-builder' ),'<a href="https://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=general-settings-link&utm_campaign=PBFree">', '</a>' )?></em></p>
			</td>
		</tr>
	<?php } ?>

		<tr>
			<th scope="row">
				<?php _e( 'Allow Users to Log in With:', 'profile-builder' ); ?>
			</th>
			<td>
				<select name="wppb_general_settings[loginWith]" class="wppb-select">
					<option value="usernameemail" <?php if ( $wppb_generalSettings['loginWith'] == 'usernameemail' ) echo 'selected'; ?>><?php _e( 'Username and Email', 'profile-builder' ); ?></option>
					<option value="username" <?php if ( $wppb_generalSettings['loginWith'] == 'username' ) echo 'selected'; ?>><?php _e( 'Username', 'profile-builder' ); ?></option>
					<option value="email" <?php if ( $wppb_generalSettings['loginWith'] == 'email' ) echo 'selected'; ?>><?php _e( 'Email', 'profile-builder' ); ?></option>
				</select>
				<ul>
					<li class="description"><?php _e( '"Username and Email" - users can Log In with both Username and Email.', 'profile-builder' ); ?></li>
					<li class="description"><?php _e( '"Username" - users can Log In only with Username.', 'profile-builder' ); ?></li>
					<li class="description"><?php _e( '"Email" - users can Log In only with Email.', 'profile-builder' ); ?></li>
				</ul>
			</td>
		</tr>

        <tr>
            <th scope="row">
                <?php _e( 'Minimum Password Length:', 'profile-builder' ); ?>
            </th>
            <td>
                <input type="text" name="wppb_general_settings[minimum_password_length]" class="wppb-text" value="<?php if( !empty( $wppb_generalSettings['minimum_password_length'] ) ) echo esc_attr( $wppb_generalSettings['minimum_password_length'] ); ?>"/>
                <ul>
                    <li class="description"><?php _e( 'Enter the minimum characters the password should have. Leave empty for no minimum limit', 'profile-builder' ); ?> </li>
                </ul>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <?php _e( 'Minimum Password Strength:', 'profile-builder' ); ?>
            </th>
            <td>
                <select name="wppb_general_settings[minimum_password_strength]" class="wppb-select">
                    <option value=""><?php _e( 'Disabled', 'profile-builder' ); ?></option>
                    <option value="short" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'short' ) echo 'selected'; ?>><?php _e( 'Very weak', 'profile-builder' ); ?></option>
                    <option value="bad" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'bad' ) echo 'selected'; ?>><?php _e( 'Weak', 'profile-builder' ); ?></option>
                    <option value="good" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'good' ) echo 'selected'; ?>><?php _e( 'Medium', 'profile-builder' ); ?></option>
                    <option value="strong" <?php if ( !empty($wppb_generalSettings['minimum_password_strength']) && $wppb_generalSettings['minimum_password_strength'] == 'strong' ) echo 'selected'; ?>><?php _e( 'Strong', 'profile-builder' ); ?></option>
                </select>
            </td>
        </tr>

        <?php do_action( 'wppb_extra_general_settings', $wppb_generalSettings ); ?>
	</table>
		
	
	
	<input type="hidden" name="action" value="update" />
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" /></p>
</form>
</div>
	
<?php
}


/*
 * Function that sanitizes the general settings
 *
 * @param array $wppb_generalSettings
 *
 * @since v.2.0.7
 */
function wppb_general_settings_sanitize( $wppb_generalSettings ) {
    $wppb_generalSettings = apply_filters( 'wppb_general_settings_sanitize_extra', $wppb_generalSettings );

	if( !empty( $wppb_generalSettings ) ){
		foreach( $wppb_generalSettings as $settings_name => $settings_value ){
			if( $settings_name == "minimum_password_length" || $settings_name == "activationLandingPage" )
				$wppb_generalSettings[$settings_name] = filter_var( $settings_value, FILTER_SANITIZE_NUMBER_INT );
			elseif( $settings_name == "extraFieldsLayout" || $settings_name == "emailConfirmation" || $settings_name == "adminApproval" || $settings_name == "loginWith" || $settings_name == "minimum_password_strength" )
				$wppb_generalSettings[$settings_name] = filter_var( $settings_value, FILTER_SANITIZE_STRING );
			elseif( $settings_name == "adminApprovalOnUserRole" ){
				if( is_array( $settings_value ) && !empty( $settings_value ) ){
					foreach( $settings_value as $key => $value ){
						$wppb_generalSettings[$settings_name][$key] = filter_var( $value, FILTER_SANITIZE_STRING );
					}
				}
			}
		}
	}

    return $wppb_generalSettings;
}


/*
 * Function that pushes settings errors to the user
 *
 * @since v.2.0.7
 */
function wppb_general_settings_admin_notices() {
    settings_errors( 'wppb_general_settings' );
}
add_action( 'admin_notices', 'wppb_general_settings_admin_notices' );


/*
 * Function that return user roles
 *
 * @since v.2.2.0
 *
 * @return array
 */
function wppb_adminApproval_onUserRole() {
	global $wp_roles;

	$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();

	unset( $roles['administrator'] );

	return $roles;
}
