<?php
/**
 * Example PHP 5.3+ compatible module with non-autoloadable dependencies.
 */

namespace Your\Plugin;

class Test extends \Astoundify_ModuleLoader_Module {

	/**
	 * @since 1.0.0
	 * @var array $modules
	 * @access protected
	 */
	protected $modules = array(
		// create a `bar` module from file `/bar/manager.php`
		'bar' => 'Your\Plugin\Bar\Manager',
	);

	/**
	 * Bootstrap
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// autoload submodules
		parent::__construct();
	}

	/**
	 * Load non-autoloadable dependencies.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		if ( $this->is_loaded() ) {
			return;
		}

		require_once( dirname( __FILE__ ) . '/foo-functions.php' );

		$this->is_loaded = true;
	}

}
