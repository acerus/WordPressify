<?php
namespace um_ext\um_online\core;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class Online_Shortcode
 * @package um_ext\um_online\core
 */
class Online_Shortcode {


	/**
	 * Online_Shortcode constructor.
	 */
	function __construct() {
		add_shortcode( 'ultimatemember_online', array( &$this, 'ultimatemember_online' ) );
	}


	/**
	 * Shortcode
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	function ultimatemember_online( $args = array() ) {
		$defaults = array(
			'max'   => 11,
			'roles' => 'all'
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );

		ob_start();

		$template = null;
		$online = UM()->Online_API()->get_users();

		if ( $online ) {
			$template = um_online_path . 'templates/online.php';
		} else {
			$template = um_online_path . 'templates/nobody.php';
		}

		include $template;

		$output = ob_get_clean();
		return $output;
	}

}