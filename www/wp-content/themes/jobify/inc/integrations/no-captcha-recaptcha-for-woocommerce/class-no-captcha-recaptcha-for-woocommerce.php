<?php
/**
 * No Captcha Recaptcha for WooCommerce
 *
 * @package Jobify
 * @category Integration
 * @since 3.8.0
 */
class Jobify_No_Captcha_Recaptcha_For_Woocommerce extends Jobify_Integration {

	/**
	 * Google ReCaptcha Site Key
	 *
	 * @since 3.8.0
	 *
	 * @var string
	 */
	var $site_key = '';

	/**
	 * Constructor Class.
	 *
	 * @since 3.8.0
	 */
	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	/**
	 * Init Action.
	 *
	 * @since 3.8.0
	 */
	public function init() {}

	/**
	 * Setup Actions.
	 *
	 * @since 3.8.0
	 */
	public function setup_actions() {
		// Set defaults, so we don't need to check isset.
		$defaults = array(
			'site_key'                  => '',
			'captcha_wc_registration'   => '',
			'captcha_wc_login'          => '',
			'captcha_wc_password_reset' => '',
		);
		$option = wp_parse_args( (array)get_option( 'wc_ncr_options' ), $defaults );

		// Site key not set, bail.
		if ( ! $option['site_key'] ) {
			return;
		}

		// Set Site Key.
		$this->site_key = $option['site_key'];

		// Load if enabled.
		if ( 'yes' === $option['captcha_wc_login'] ) {
			add_action( 'woocommerce_login_form', array( $this, 'reload_captcha' ), 11 );
		}
		if ( 'yes' === $option['captcha_wc_registration'] ) {
			add_action( 'register_form', array( $this, 'reload_captcha' ), 11 );
		}
	}

	public function reload_captcha() {
?>
<style>
.g-recaptcha {
	margin-bottom: 30px;
}
</style>
<script type="text/javascript">
jQuery( document ).ready( function($) {
	grecaptcha.render( $( '.g-recaptcha' )[0], {
		sitekey: '<?php echo esc_attr( $this->site_key ); ?>',
	} );
} );
</script>
<?php
	}

}
