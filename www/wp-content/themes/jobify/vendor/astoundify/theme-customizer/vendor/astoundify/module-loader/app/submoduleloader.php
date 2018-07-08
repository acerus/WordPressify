<?php
/**
 * Load a module's submodules.
 *
 * @package Astoundify
 * @subpackage ModuleLoader
 * @since 1.0.0
 */
class Astoundify_ModuleLoader_SubModuleLoader {

	/**
	 * @since 1.0.0
	 * @var array $submodules
	 * @access protected
	 */
	protected $submodules = array();

	/**
	 * Set the modules to load.
	 *
	 * @since 1.0.0
	 *
	 * @param array $submodules
	 */
	public function set_submodules( $submodules ) {
		$this->submodules = $submodules;
	}

	/**
	 * Get the modules to manage.
	 *
	 * @since 1.0.0
	 *
	 * @param array $submodules
	 */
	public function get_submodules() {
		return $this->submodules;
	}

	/**
	 * Load multiple modules.
	 *
	 * @since 1.0.0
	 *
	 * @param array $submodules The modules to load
	 */
	public function load_submodules() {
		foreach ( $this->get_submodules() as $submodule_name => $submodule_dependency ) {
			if ( $this->has_submodule_instance( $submodule_name ) ) {
				continue;
			}

			$submodule = $this->create_instance( $submodule_dependency );

			if ( $submodule ) {
				$this->add_submodule( $submodule_name, $submodule );
			}
		}
	}

	/**
	 * Get a submodule
	 *
	 * @since 1.0.0
	 *
	 * @param string $submodule_name
	 * @param string $submodule_dependency
	 * @return object Astoundify\Module
	 */
	public function get_submodule( $submodule_name, $args = false ) {
		$submodule = $this->modules[ $submodule_name ];

		if ( $args && ! empty( $args ) ) {
			$submodule = $args[0];
			return $submodule->$submodule();
		}

		return $this->modules[ $submodule_name ];
	}

	/**
	 * Add a module
	 *
	 * @since 1.0.0
	 *
	 * @param string $submodule_name
	 * @param string $submodule_dependency
	 * @return void
	 */
	public function add_submodule( $submodule_name, $submodule ) {
		// module instance exists
		if ( $this->has_submodule_instance( $submodule_name ) ) {
			return;
		}

		// add to the list
		$this->modules[ $submodule_name ] = $submodule;
	}

	/**
	 * Does this module exist?
	 *
	 * @since 1.0.0
	 *
	 * @param string $submodule_name
	 * @return bool
	 */
	public function has_submodule( $submodule_name ) {
		return isset( $this->modules[ $submodule_name ] );
	}

	/**
	 * Does this module exist and is it an object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $submodule_name
	 * @return bool
	 */
	public function has_submodule_instance( $submodule_name ) {
		return $this->has_submodule( $submodule_name );
	}

	/**
	 * Create an instance of a module.
	 *
	 * @since 1.0.0
	 *
	 * @param string $submodule_class
	 * @return object
	 */
	public function create_instance( $submodule_class ) {
		return new $submodule_class;
	}

}
