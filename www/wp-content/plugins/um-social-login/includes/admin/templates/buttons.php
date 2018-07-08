<div class="um-admin-metabox">

	<h4><?php _e( 'Provider Settings','um-social-login' ); ?></h4>

	<?php $fields = array();
	foreach( UM()->Social_Login_API()->networks as $provider => $array ) {
		$fields[] = array(
			'id'		    => '_um_enable_' . $provider,
			'type'		    => 'checkbox',
			'label'    		=> sprintf( __( 'Enable <b>%s</b>', 'um-social-login' ), $array['name'] ),
			'value' 		=> UM()->query()->get_meta_value( '_um_enable_' . $provider, null, 1 )
		);
	}

	UM()->admin_forms( array(
		'class'		=> 'um-social-login-networks um-half-column',
		'prefix_id'	=> 'social_login',
		'fields' 	=> $fields
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
	<h4><?php _e( 'General Settings','um-social-login' ); ?></h4>

	<?php UM()->admin_forms( array(
		'class'		=> 'um-social-login-general um-half-column',
		'prefix_id'	=> 'social_login',
		'fields' 	=> array(
			array(
				'id'		=> '_um_assigned_role',
				'type'		=> 'select',
				'label'    	=> __( 'Assign Role','um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value( '_um_assigned_role' ),
				'options' 	=> UM()->roles()->get_roles()
			),
			array(
				'id'		=> '_um_show_for_members',
				'type'		=> 'checkbox',
				'label'    	=> __( 'Show for logged-in users?', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value( '_um_show_for_members', null, 1 ),
			)
		)
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
	<h4><?php _e('Button Appearance', 'um-social-login'); ?></h4>

	<?php UM()->admin_forms( array(
		'class'		=> 'um-social-login-button um-half-column',
		'prefix_id'	=> 'social_login',
		'fields' 	=> array(
			array(
				'id'		=> '_um_show_icons',
				'type'		=> 'checkbox',
				'label'    	=> __( 'Show icon in the social login button?', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value( '_um_show_icons', null, 1 ),
			),
			array(
				'id'		=> '_um_show_labels',
				'type'		=> 'checkbox',
				'label'    	=> __( 'Show label in the social login button?', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value( '_um_show_labels', null, 1 ),
			),
			array(
				'id'		=> '_um_fontsize',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => '15px',
				'label'    	=> __( 'Font Size', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_fontsize', null, 'na' ),
			),
			array(
				'id'		=> '_um_iconsize',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => '18px',
				'label'    	=> __( 'Icon Size', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_iconsize', null, 'na' ),
			),
			array(
				'id'		=> '_um_button_style',
				'type'		=> 'select',
				'label'    	=> __( 'Button Style','um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value( '_um_button_style' ),
				'options' 	=> array(
					'default' => __( 'One button per line', 'um-social-login' ),
					'responsive' => __( 'Responsive','um-social-login' ),
					'floated' => __( 'Floated', 'um-social-login' )
				)
			),
			array(
				'id'		=> '_um_button_min_width',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => 'e.g. 205px',
				'label'    	=> __( 'Button Min Width', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_button_min_width', null, 'na' ),
			),
			array(
				'id'		=> '_um_button_padding',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => '16px 20px',
				'label'    	=> __( 'Button Padding', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_button_padding', null, 'na' ),
			),
		)
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
	<h4><?php _e('Container Appearance','um-social-login'); ?></h4>

	<?php UM()->admin_forms( array(
		'class'		=> 'um-social-login-container um-half-column',
		'prefix_id'	=> 'social_login',
		'fields' 	=> array(
			array(
				'id'		=> '_um_container_max_width',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => '600px',
				'label'    	=> __( 'Icon Size', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_container_max_width', null, '600px' ),
			),
			array(
				'id'		=> '_um_margin',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => '0px 0px 0px 0px',
				'label'    	=> __( 'Container Margin', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_margin', null, 'na' ),
			),
			array(
				'id'		=> '_um_padding',
				'type'		=> 'text',
				'size'		=> 'small',
				'placeholder' => '0px 0px 0px 0px',
				'label'    	=> __( 'Container Padding', 'um-social-login' ),
				'value' 	=> UM()->query()->get_meta_value('_um_padding', null, 'na' ),
			),
		)
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
</div>