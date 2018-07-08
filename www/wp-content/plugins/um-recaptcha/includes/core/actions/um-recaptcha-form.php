<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	 ***    @add recaptcha
	 ***/
	add_action( 'um_after_register_fields', 'um_recaptcha_add_captcha', 500 );
	add_action( 'um_after_login_fields', 'um_recaptcha_add_captcha', 500 );
	function um_recaptcha_add_captcha( $args ) {

		if ( ! UM()->reCAPTCHA_API()->captcha_allowed( $args ) )
			return;

		$your_sitekey = UM()->options()->get( 'g_recaptcha_sitekey' );

		$options = array(
			'data-type'  => UM()->options()->get( 'g_recaptcha_type' ),
			'data-size'  => UM()->options()->get( 'g_recaptcha_size' ),
			'data-theme' => UM()->options()->get( 'g_recaptcha_theme' ),
		);
		?>

		<?php if ('invisible' == $options['data-size']): ?>
            <script>

                var onSubmit = function (token) {
                    var me = jQuery('.um-<?php _e( $args['form_id'] );?> form');
                    me.attr('disabled', 'disabled');
                    me.submit();
                };

                var onloadCallback = function () {
                    grecaptcha.render('um-submit-btn', {
                        'sitekey': '<?php _e( $your_sitekey );?>',
                        'callback': onSubmit
                    });
                };

                jQuery(document).ready(function () {
                    jQuery('.um-<?php _e( $args['form_id'] );?> #um-submit-btn').addClass('um-has-recaptcha');
                });

            </script>

		<?php else: ?>
			<?php $options['data-sitekey'] = $your_sitekey; ?>
            <script>
                var onloadCallback = function () {
                    jQuery('.g-recaptcha').each(function (i) {
                        grecaptcha.render(jQuery(this).attr('id'), {
                            'sitekey': jQuery(this).attr('data-sitekey'),
                            'theme': jQuery(this).attr('data-theme')
                        });

                    });
                }
            </script>
		<?php endif; ?>

		<?php
		$attrs = '';
		foreach ($options as $att => $value) {
			if ($value) {
				$attrs .= " {$att}=\"{$value}\" ";
			}
		}
		?>

        <div class="g-recaptcha" id="um-<?php _e( $args['form_id'] ); ?>" <?php echo $attrs; ?> ></div>
		<?php

		if (UM()->form()->has_error( 'recaptcha' )) {
			echo '<div class="um-field-error">' . UM()->form()->errors['recaptcha'] . '</div>';
		}

	}

	/***
	 ***    @form error handling
	 ***/
	add_action( 'um_submit_form_errors_hook', 'um_recaptcha_validate', 20 );
	function um_recaptcha_validate( $args ) {
		if (isset( $args['mode'] ) && !in_array( $args['mode'], array( 'login', 'register' ) ) && !isset( $args['_social_login_form'] )) return;

		if (!UM()->reCAPTCHA_API()->captcha_allowed( $args )) return;

		$your_secret = trim( UM()->options()->get( 'g_recaptcha_secretkey' ) );
		$client_captcha_response = $_POST['g-recaptcha-response'];
		$user_ip = $_SERVER['REMOTE_ADDR'];

		$response = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=$your_secret&response=$client_captcha_response&remoteip=$user_ip" );

		$error_codes = array(
			'missing-input-secret'   => __( 'The secret parameter is missing.', 'um-recaptcha' ),
			'invalid-input-secret'   => __( 'The secret parameter is invalid or malformed.', 'um-recaptcha' ),
			'missing-input-response' => __( 'The response parameter is missing.', 'um-recaptcha' ),
			'invalid-input-response' => __( 'The response parameter is invalid or malformed.', 'um-recaptcha' ),
		);


		if (is_array( $response )) {

			$result = json_decode( $response['body'] );

			if (isset( $result->{'error-codes'} ) && !$result->success) {
				foreach ($result->{'error-codes'} as $key => $error_code) {

					if ($error_code == 'missing-input-response') {
						UM()->form()->add_error( 'recaptcha', __( 'Please confirm you are not a robot', 'um-recaptcha' ) );
					} else {
						UM()->form()->add_error( 'recaptcha', $error_codes[$error_code] );
					}
				}
			}

		}

	}

	/**
     * reCAPTCHA scripts/styles enqueue
	 *
	 * @uses   hook actions: um_pre_register_shortcode
     *                       um_pre_login_shortcode
	 */
	function um_recaptcha_enqueue_scripts( $args ){
		if (!UM()->reCAPTCHA_API()->captcha_allowed( $args ))
			return;

	    UM()->reCAPTCHA_API()->enqueue()->wp_enqueue_scripts();

    }
	add_action( 'um_pre_register_shortcode', 'um_recaptcha_enqueue_scripts' );
	add_action( 'um_pre_login_shortcode', 'um_recaptcha_enqueue_scripts' );

