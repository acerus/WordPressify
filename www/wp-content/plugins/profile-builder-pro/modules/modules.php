<?php
/**
 * Function that creates the "modules" submenu page
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_register_modules_submenu_page() {
	if ( PROFILE_BUILDER == 'Profile Builder Pro' )
		add_submenu_page( 'profile-builder', __( 'Modules', 'profile-builder' ), __( 'Modules', 'profile-builder' ), 'manage_options', 'profile-builder-modules', 'wppb_modules_content' );
}
add_action( 'admin_menu', 'wppb_register_modules_submenu_page', 13 );


function wppb_generate_modules_default_values(){
	$wppb_module_settings = get_option( 'wppb_module_settings', 'not_found' );
	if ( $wppb_module_settings == 'not_found' ){
		$wppb_module_settings = 	array(	'wppb_userListing'					=> 'hide',
										    'wppb_customRedirect'				=> 'hide',
										    'wppb_emailCustomizer'				=> 'hide',
										    'wppb_emailCustomizerAdmin'			=> 'hide',
										    'wppb_multipleEditProfileForms'		=> 'hide',
										    'wppb_multipleRegistrationForms'	=> 'hide',
										    'wppb_repeaterFields'				=> 'hide'
								);
		update_option( 'wppb_module_settings', $wppb_module_settings );
	}
		
	$wppb_module_settings_description = get_option( 'wppb_module_settings_description', 'not_found' );
	if ( $wppb_module_settings_description == 'not_found' ){
		$wppb_module_settings_description = 	array(	'wppb_userListing' => 'User-Listing',
													    'wppb_customRedirect' => 'Custom Redirects',
													    'wppb_emailCustomizer' => 'User Email Customizer',
													    'wppb_emailCustomizerAdmin' => 'Admin Email Customizer',
													    'wppb_multipleEditProfileForms' => 'Edit-profile Forms',
													    'wppb_multipleRegistrationForms' => 'Registration Forms',
													    'wppb_repeaterFields' => 'Repeater Fields',

											);
		update_option( 'wppb_module_settings_description', $wppb_module_settings_description );
	}
}


/**
 * Function that adds content to the "modules" submenu page
 *
 * @since v.2.0
 *
 * @return string
 */
function wppb_modules_content() {
	wppb_generate_modules_default_values();
	?>
	<div class="wrap wppb-wrap wppb-modules">
	
		<h2><?php _e( 'Modules', 'profile-builder' );?></h2>
		<p><?php _e( 'Here you can activate / deactivate available modules for Profile Builder.', 'profile-builder' );?></p>
		
		<form method="post" action="options.php#add-ons">
		
			<?php $wppb_addonOptions = get_option('wppb_module_settings'); ?>
			<?php settings_fields('wppb_module_settings'); ?>
			
			<table class="widefat column-1">
				<thead>
					<tr>
						<th scope="col"><?php _e( 'Name/Description', 'profile-builder' );?></th>
						<th scope="col"><?php _e( 'Status', 'profile-builder' );?></th>
					</tr>
				</thead>
				<tbody>
                    <tr>
                        <td><?php _e( 'Multiple Registration Forms', 'profile-builder' );?></td>
                        <td>
                            <input id="mrf_s" type="radio" name="wppb_module_settings[wppb_multipleRegistrationForms]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_multipleRegistrationForms'] ) && $wppb_addonOptions['wppb_multipleRegistrationForms'] == 'show') echo 'checked';?> /><label for="mrf_s"><?php _e( 'Active', 'profile-builder' );?></label>
                            <input id="mrf_h" type="radio" name="wppb_module_settings[wppb_multipleRegistrationForms]" value="hide" <?php if ( empty( $wppb_addonOptions['wppb_multipleRegistrationForms'] ) || ( !empty( $wppb_addonOptions['wppb_multipleRegistrationForms'] ) && $wppb_addonOptions['wppb_multipleRegistrationForms'] == 'hide') ) echo 'checked';?> /><label for="mrf_h"><?php _e( 'Inactive', 'profile-builder' );?></label>
                        </td>
                    </tr>
                    <tr class="alternate">
                        <td><?php _e( 'Multiple Edit-profile Forms', 'profile-builder' );?></td>
                        <td>
                            <input id="mepf_s" type="radio" name="wppb_module_settings[wppb_multipleEditProfileForms]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_multipleEditProfileForms'] ) && $wppb_addonOptions['wppb_multipleEditProfileForms'] == 'show') echo 'checked';?> /><label for="mepf_s"><?php _e( 'Active', 'profile-builder' );?></label>
                            <input id="mepf_h" type="radio" name="wppb_module_settings[wppb_multipleEditProfileForms]" value="hide" <?php if ( empty( $wppb_addonOptions['wppb_multipleEditProfileForms'] ) || ( !empty( $wppb_addonOptions['wppb_multipleEditProfileForms'] ) && $wppb_addonOptions['wppb_multipleEditProfileForms'] == 'hide' ) ) echo 'checked';?> /><label for="mepf_h"><?php _e( 'Inactive', 'profile-builder' );?></label>
                        </td>
                    </tr>
					<tr>
						<td><?php _e( 'User Listing', 'profile-builder' );?></td>
						<td>
							<input id="ul_s" type="radio" name="wppb_module_settings[wppb_userListing]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_userListing'] ) && $wppb_addonOptions['wppb_userListing'] == 'show') echo 'checked';?> /><label for="ul_s"><?php _e('Active', 'profile-builder' );?></label>
							<input id="ul_h" type="radio" name="wppb_module_settings[wppb_userListing]" value="hide" <?php if ( !empty( $wppb_addonOptions['wppb_userListing'] ) && $wppb_addonOptions['wppb_userListing'] == 'hide') echo 'checked';?>/><label for="ul_h"><?php _e('Inactive', 'profile-builder' );?></label>
						</td> 
					</tr>
					<tr class="alternate">
                        <td><?php _e( 'Admin Email Customizer', 'profile-builder' );?></td>
                        <td>
                            <input id="eca_s" type="radio" name="wppb_module_settings[wppb_emailCustomizerAdmin]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_emailCustomizerAdmin'] ) && $wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'show') echo 'checked';?> /><label for="eca_s"><?php _e( 'Active', 'profile-builder' );?></label>
                            <input id="eca_h" type="radio" name="wppb_module_settings[wppb_emailCustomizerAdmin]" value="hide" <?php if ( !empty( $wppb_addonOptions['wppb_emailCustomizerAdmin'] ) && $wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'hide') echo 'checked';?> /><label for="eca_h"><?php _e( 'Inactive', 'profile-builder' );?></label>
                        </td>
					</tr>
					<tr>  
						<td><?php _e( 'User Email Customizer', 'profile-builder' );?></td>
						<td> 
							<input id="ec_s" type="radio" name="wppb_module_settings[wppb_emailCustomizer]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_emailCustomizer'] ) && $wppb_addonOptions['wppb_emailCustomizer'] == 'show') echo 'checked';?> /><label for="ec_s"><?php _e( 'Active', 'profile-builder' );?></label>
							<input id="ec_h" type="radio" name="wppb_module_settings[wppb_emailCustomizer]" value="hide" <?php if ( !empty( $wppb_addonOptions['wppb_emailCustomizer'] ) && $wppb_addonOptions['wppb_emailCustomizer'] == 'hide') echo 'checked';?> /><label for="ec_h"><?php _e( 'Inactive', 'profile-builder' );?></label>
						</td> 
					</tr>
					<tr class="alternate">
                        <td><?php _e( 'Custom Redirects', 'profile-builder' );?></td>
                        <td>
                            <input id="cr_s" type="radio" name="wppb_module_settings[wppb_customRedirect]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_customRedirect'] ) && $wppb_addonOptions['wppb_customRedirect'] == 'show') echo 'checked';?> /><label for="cr_s"><?php _e( 'Active', 'profile-builder' );?></label>
                            <input id="cr_h" type="radio" name="wppb_module_settings[wppb_customRedirect]" value="hide" <?php if ( !empty( $wppb_addonOptions['wppb_customRedirect'] ) && $wppb_addonOptions['wppb_customRedirect'] == 'hide') echo 'checked';?> /><label for="cr_h"><?php _e( 'Inactive', 'profile-builder' );?></label>
                        </td>
					</tr>
					<tr>
                        <td><?php _e( 'Repeater Fields', 'profile-builder' );?></td>
                        <td>
                            <input id="rpf_s" type="radio" name="wppb_module_settings[wppb_repeaterFields]" value="show" <?php if ( !empty( $wppb_addonOptions['wppb_repeaterFields'] ) && $wppb_addonOptions['wppb_repeaterFields'] == 'show') echo 'checked';?> /><label for="rpf_s"><?php _e( 'Active', 'profile-builder' );?></label>
                            <input id="rpf_h" type="radio" name="wppb_module_settings[wppb_repeaterFields]" value="hide" <?php if ( empty($wppb_addonOptions['wppb_repeaterFields']) || ( !empty( $wppb_addonOptions['wppb_repeaterFields'] ) && $wppb_addonOptions['wppb_repeaterFields'] == 'hide') ) echo 'checked';?> /><label for="rpf_h"><?php _e( 'Inactive', 'profile-builder' );?></label>
                        </td>
					</tr>
				</tbody>
			</table>
			<div id="wppb_submit_button_div">
				<input type="hidden" name="action" value="update" />
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" /></p>
			</div>
			
		</form>
		
	</div>
	<?php
}