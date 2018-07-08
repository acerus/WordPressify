<?php
/**
 * A dependent class of Your\Plugin\test, which is a module itself.
 *
 * @since 1.0.0
 */

namespace Your\Plugin\Bar;

/**
 * Submodule which is a module (to enable hooking and loading)
 * but does not include any of its own submodules.
 *
 * @since 1.0.0
 */
class Manager extends \Astoundify_ModuleLoader_Module {

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		add_action( 'init', array( $this, 'hello' ) );

		$this->is_hooked = true;
	}

	/**
	 * Print another message
	 *
	 * @since 1.0.0
	 */
	public function hello() {
		var_dump( 'Hello' );
	}

}
