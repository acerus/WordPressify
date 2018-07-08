<div class="um-admin-metabox">

	<?php $status = UM()->options()->get( 'g_recaptcha_status' );

	if ( $status ) { ?>

		<p><?php _e( 'Google reCAPTCHA seems to be <strong style="color:#7ACF58">enabled</strong> by default.', 'um-recaptcha' ); ?></p>

	<?php } else { ?>

		<p><?php _e( 'Google reCAPTCHA seems to be <strong style="color:#C74A4A">disabled</strong> by default.', 'um-recaptcha' ); ?></p>

	<?php }

	UM()->admin_forms( array(
		'class'		=> 'um-form-login-recaptcha um-top-label',
		'prefix_id'	=> 'form',
		'fields' => array(
			array(
				'id'		    => '_um_login_g_recaptcha_status',
				'type'		    => 'select',
				'label'		    => __( 'reCAPTCHA status on this form', 'um-recaptcha' ),
				'value'		    => UM()->query()->get_meta_value( '_um_login_g_recaptcha_status', null, $status ),
				'options'		=> array(
					'0'	=> __('No', 'um-recaptcha'),
					'1'	=> __('Yes', 'um-recaptcha')
				),
			)
		)
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
</div>