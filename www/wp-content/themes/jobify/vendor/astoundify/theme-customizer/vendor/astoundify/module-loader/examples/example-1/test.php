<?php
/**
 * Example PHP 5.2 compatible module.
 */
class Your_Plugin_Test extends Astoundify_ModuleLoader_Module {

	/**
	 * @since 1.0.0
	 * @var array $modules
	 * @access protected
	 */
	protected $modules = array(
		// create a `foo` module from file `/foo/manager.php`
		'foo' => 'Your_Plugin_Foo_Manager',
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

}
