<div class="um-admin-metabox">

	<?php UM()->admin_forms( array(
		'class'		=> 'um-form-login-social um-top-label',
		'prefix_id'	=> 'form',
		'fields' => array(
			array(
				'id'		    => '_um_login_show_social',
				'type'		    => 'select',
				'label'    		=> __( 'Show social connect on this form?','um-social-login' ),
				'value' 		=> UM()->query()->get_meta_value( '_um_login_show_social', null, 1 ),
				'options' 		=> array(
					'0'	=>	__('No','um-social-login'),
					'1'	=>	__('Yes','um-social-login')
				),
			)
		)
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
</div>