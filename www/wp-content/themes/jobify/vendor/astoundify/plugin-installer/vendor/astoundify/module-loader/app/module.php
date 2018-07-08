<?php
/**
 * Astoundify_ModuleLoader_Module class.
 *
 * @package Astoundify
 * @subpackage ModuleLoader
 * @since 1.0.0
 */

if ( ! class_exists( 'Astoundify_ModuleLoader_Module' ) ) :
	/**
	 * A single moudle.
	 *
	 * This is an abstract class, so it is unusable on its own. It must be extended by another class.
	 *
	 * @since 1.0.0
	 */
	abstract class Astoundify_ModuleLoader_Module implements Astoundify_ModuleLoader_HookInterface, Astoundify_ModuleLoader_LoadInterface {

		/**
		 * @since 1.0.0
		 * @var bool $is_loaded
		 * @access protected
		 */
		protected $is_loaded = false;

		/**
		 * @since 1.0.0
		 * @var bool $is_hooked
		 * @access protected
		 */
		protected $is_hooked = false;

		/**
		 * @since 1.0.0
		 * @var array $modules
		 * @access protected
		 */
		protected $modules = array();

		/**
		 * @since 1.0.0
		 * @var Astoundify_ModuleLoader_Loader $loader
		 * @access protected
		 */
		protected $loader;

		/**
		 * Bootstrap
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// load any file non-class file dependencies
			$this->load();

			// load all submodules
			$this->loader = new Astoundify_ModuleLoader_SubModuleLoader();
			$this->loader->set_submodules( $this->get_modules() );
			$this->loader->load_submodules();

			// hook in to WordPress
			$this->hook();
		}

		/**
		 * Get a specific submodule directly if available.
		 *
		 * @since 1.0.0
		 *
		 * @param string $module_name
		 * @param array  $arguments
		 * @return mixed
		 */
		public function __call( $name, $args ) {
			if ( $this->loader->has_submodule_instance( $name ) ) {
				return $this->loader->get_submodule( $name, $args );
			}

			return call_user_func_array( $name, $args );
		}

		/**
		 * Load dependencies
		 *
		 * @since 1.0.0
		 */
		public function load() {
			if ( $this->is_loaded() ) {
				return;
			}

			$this->is_loaded = true;
		}

		/**
		 * Have the dependencies been loaded?
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public function is_loaded() {
			return (bool) $this->is_loaded;
		}

		/**
		 * Hook in to WordPress
		 *
		 * @since 1.0.0
		 */
		public function hook() {
			if ( $this->is_hooked() ) {
				return;
			}

			$this->is_hooked = true;
		}

		/**
		 * Is hooked in to WordPress?
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public function is_hooked() {
			return (bool) $this->is_hooked;
		}

		/**
		 * Get all assign submodules
		 *
		 * @since 1.0.0
		 *
		 * @return array $modules
		 */
		public function get_modules() {
			return (array) $this->modules;
		}

	}
endif;
