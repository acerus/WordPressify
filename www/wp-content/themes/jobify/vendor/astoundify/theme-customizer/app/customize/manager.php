<?php
/**
 * Interface with the existing Customize API.
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap the backend.
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Customize_Manager extends Astoundify_ModuleLoader_Module {

	/**
	 * @since 1.1.0
	 * @var array $modules
	 * @access protected
	 */
	protected $modules = array(
		'register' => 'Astoundify_ThemeCustomizer_Customize_Register',
		'scripts' => 'Astoundify_ThemeCustomizer_Customize_Scripts',
	);

	/**
	 * Hook in to WordPress.
	 *
	 * @since 1.2.0
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		add_filter( 'customize_dynamic_setting_class', array( $this, 'filter_customize_dynamic_setting_class' ), 5, 3 );

		$this->hooked = true;
	}

	/**
	 * Filters customize_dynamic_setting_class.
	 *
	 * @param string $class Setting class.
	 * @param string $setting_id Setting ID.
	 * @param array  $args Setting args.
	 *
	 * @return string
	 */
	public function filter_customize_dynamic_setting_class( $class, $setting_id, $args ) {
		unset( $setting_id );

		if ( isset( $args['type'] ) ) {
			if ( 'post' === $args['type'] ) {
				$class = 'WP_Customize_Post_Setting';
			} elseif ( 'postmeta' === $args['type'] ) {
				if ( isset( $args['setting_class'] ) ) {
					$class = $args['setting_class'];
				} else {
					$class = 'Astoundify_ThemeCustomizer_TermMetaSetting';
				}
			}
		}

		return $class;
	}

}
