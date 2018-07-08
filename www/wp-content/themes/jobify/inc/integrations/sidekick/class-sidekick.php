<?php
/**
 * Sidekick integration.
 *
 * @package Jobify
 * @category Integration
 * @since Jobify 3.0.0
 */
class Jobify_Sidekick extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );

		define( 'SK_PRODUCT_ID', 423 );
		define( 'SK_ENVATO_PARTNER', 'l0+H4H71qrXslK8wNC1lpdDR1NAAs/TcvGAu7MKmfn8=' );
		define( 'SK_ENVATO_SECRET', 'dozca5IuACSFRz35udPSLiGejSpGtgs0P1UM0NG9PKo=' );
	}

}
