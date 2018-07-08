<?php
add_action( 'admin_menu', 'tikemp_add_admin_menu' );
add_action( 'admin_init', 'tikemp_settings_init' );



function tikemp_add_admin_menu(  ) { 

	add_options_page( 'Fast user switching', 'Fast user switching', 'manage_options', 'fast_user_switching', 'tikemp_options_page' );

}


function tikemp_settings_init(  ) { 

	register_setting( 'pluginPage', 'fus_settings', 'tikemp_settings_filter' );

	add_settings_section(
		'tikemp_pluginPage_section', 
		__( '', 'fast-user-switching' ), 
		'tikemp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'fus_name', 
		__( 'Show first name and surname', 'fast-user-switching' ), 
		'tikemp_name_render', 
		'pluginPage', 
		'tikemp_pluginPage_section' 
	);

	add_settings_field( 
		'fus_role', 
		__( 'Show role (access level)', 'fast-user-switching' ), 
		'tikemp_role_render', 
		'pluginPage', 
		'tikemp_pluginPage_section' 
	);

	add_settings_field( 
		'fus_username', 
		__( 'Show user name', 'fast-user-switching' ), 
		'tikemp_username_render', 
		'pluginPage', 
		'tikemp_pluginPage_section' 
	);

	add_settings_field(
		'fus_showdate',
		__( 'Show last switched date','fast-user-switching' ),
		'tikemp_lastSwitchedDate_render',
		'pluginPage',
		'tikemp_pluginPage_section'
	);

	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ){
		
		add_settings_field(
			'fus_woo',
			__( 'Empty WooCommerce cart item during switching','fast-user-switching' ),
			'tikemp_woocom_render',
			'pluginPage',
			'tikemp_pluginPage_section'
		);

		add_settings_field(
			'fus_showon_woo_order',
			__( 'Switch user from order details page','fast-user-switching' ),
			'tikemp_wooorder_render',
			'pluginPage',
			'tikemp_pluginPage_section'
		);

	}


}

function tikemp_role_select_repeater(){

	$roles = wp_roles()->role_names;

	$options = get_option( 'fus_settings', [] );
	$fus_roles = isset($options['fus_roles']) ? $options['fus_roles'] : [];

	if( empty($fus_roles) ){
		$fus_roles[] = 'administrator';
	}

	$fus_roles = array_unique($fus_roles);

	echo '<table class="form-table" id="role-select">';

	?>
	
	<tr class="role-selector hidden">
		<th scope="row"><?= __('Enable User Switching For','fast-user-switching'); ?></th>
		<td>
			<select name="fus_settings[fus_roles][]" >

				<?php 

					foreach ($roles as $rkey => $rval) {
						?>

							<option value="<?= $rkey; ?>"><?= $rval; ?></option>

						<?php
					}
				?>
			</select>
			<strong><?= __('Roles','fast-user-switching'); ?></strong>
			<button type="button" class="remove-this-row"><span class="dashicons dashicons-no-alt"></span></button>
		</td>
	</tr>

	<?php
	foreach ($fus_roles as $key => $value) :
	?>
	
		<tr class="role-selector">
			<th scope="row"><?= __('Enable User Switching For','fast-user-switching'); ?></th>
			<td>
				<select name="fus_settings[fus_roles][]" >

				<?php 

					foreach ($roles as $rkey => $rval) {
						?>

							<option value="<?= $rkey; ?>" <?php selected( $value, $rkey, true ); disabled( $value,'administrator',true);?>><?= $rval; ?></option>

						<?php
					}
				?>
				</select>
				<strong><?= __('Roles','fast-user-switching'); ?></strong>
				<?php if ( $value != 'administrator' ): ?>
					<button type="button" class="remove-this-row"><span class="dashicons dashicons-no-alt"></span></button>
				<?php endif; ?>
			</td>
		</tr>


	<?php
	endforeach;
	
	echo '</table>';
}


function tikemp_name_render(  ) { 

	$options = get_option( 'fus_settings', []);
	$fus_name = isset($options['fus_name']) ? $options['fus_name'] : 0;

	?>
	<input type='checkbox' name='fus_settings[fus_name]' <?php checked( $fus_name, 1 ); ?> value='1'>
	<?php

}


function tikemp_role_render(  ) { 

	$options = get_option( 'fus_settings', []);
	$fus_role = isset($options['fus_role']) ? $options['fus_role'] : 0;
	?>
	<input type='checkbox' name='fus_settings[fus_role]' <?php checked( $fus_role, 1 ); ?> value='1'>
	<?php

}


function tikemp_username_render(  ) { 

	$options = get_option( 'fus_settings', []);
	$fus_username = isset($options['fus_username']) ? $options['fus_username'] : 0;
	?>
	<input type='checkbox' name='fus_settings[fus_username]' <?php checked( $fus_username , 1 ); ?> value='1'>
	<?php

}

function tikemp_lastSwitchedDate_render(){
	$options = get_option( 'fus_settings', []);
	$fus_showdate = isset($options['fus_showdate']) ? $options['fus_showdate'] : 0;
	?>
	<input type='checkbox' name='fus_settings[fus_showdate]' <?php checked( $fus_showdate, 1 ); ?> value='1'>
	<?php
}

function tikemp_woocom_render(){
	$options = get_option( 'fus_settings', []);
	$fus_woo = isset($options['fus_woo']) ? $options['fus_woo'] : 0;
	?>
	<input type='checkbox' name='fus_settings[fus_woo]' <?php checked( $fus_woo, 1 ); ?> value='1'>
	<?php
}

function tikemp_wooorder_render(){
	$options = get_option( 'fus_settings', []);
	$fus_showon_woo_order = isset($options['fus_showon_woo_order']) ? $options['fus_showon_woo_order'] : 0;
	?>
	<input type='checkbox' name='fus_settings[fus_showon_woo_order]' <?php checked( $fus_showon_woo_order, 1 ); ?> value='1'>
	<?php
}


function tikemp_settings_section_callback(  ) { 

}


function tikemp_options_page(  ) { 

	?>
	<form action='options.php' method='post' id='tikemp_settings_page'>

		<h2>Fast user switching</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		echo '<hr/>';
		echo '<h2>'.__('Role Assign','fast-user-switching').'</h2>';
		tikemp_role_select_repeater();
		submit_button();
		tikemp_admin_scripts();
		?>

	</form>
	<?php

}


function tikemp_settings_filter($opt){

	if ( empty($opt) ){
		$opt['fus_username'] = 1;
		add_settings_error('pluginPage','error',__('You must have to enable at least one option!','fast-user-switching'),'error');
	}

	$recent_users = get_option( 'tikemp_recent_imp_users', [] );
	foreach ($recent_users as $key => $value) {

		$user = explode('&', $value);
		$user_id = $user[0];

		if ( count($user) == 4 ){
			$last_date = end($user);
		}
		
		$new_display_format = tikemp_updateUserInfo($user_id,$opt);
		$recent_users[$key] = $new_display_format.'&'.$last_date;

	}

	update_option('tikemp_recent_imp_users',$recent_users);

	return $opt;
}

function tikemp_updateUserInfo($user_id,$fus_settings){

	$user = get_userdata( $user_id );

	$roles = tikemp_get_readable_rolename(array_shift($user->roles));
	
	if ( isset($fus_settings['fus_name']) ){
		$name_display = $user->first_name.' '.$user->last_name;				
	}else {
		$name_display = ' ';
	}

	if ( isset($fus_settings['fus_role']) ){
		$role_display = $roles;
	} else {
		$role_display = '';
	}

	if ( isset($fus_settings['fus_username']) ){
		$role_display .= ' - '.$user->user_login;
	}

	$keep = $user->data->ID.'&'.$name_display.'&'.$role_display;

	return $keep;
}

function tikemp_admin_scripts(){

	?>
	<style>
		button.remove-this-row{
			width: 30px;
		    height: 22px;
		    background-color: #ce3030;
		    border: 0 none;
		    color: #fff;
		    font-size: 21px;
		    line-height: 14px;
		    padding: 0;
		    border-radius: 2px;
		    cursor: pointer;
		}
	</style>
	<script>

		jQuery(document).ready(function($) {

			$('table#role-select').append('<button type="button" class="button button-default add-new-roles" >Add new</button>');

			$('.form-table').on('click','.remove-this-row', function(){
				$(this).parent().parent().remove();
			});

			$('table#role-select').on('click','.add-new-roles',function(){

					cont = $('tr.role-selector').html();

					$('table#role-select tbody').append('<tr>'+cont+'</tr>');

			});

		});

	</script>


	<?php
}