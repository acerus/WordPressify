<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: view.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
require_once dirname( dirname( dirname( __FILE__ ) ) ) . DS . 'includes' . DS . 'view.php';
require_once OBGRAB_HELPERS.'plugins.php';
class PIPESViewPlugins extends View {
	public $items = array();
	public $itemsTable = null;
	public $data = null;
	public function __construct(){
		parent::__construct();
	}
	
	public function display(){
		$model = $this->getModel();
		$this->data = $model->getPipesPlugins();
		do_action( 'wppipes_loaded_ads' );
		parent::display();
	}

	public function on_load_page() {
		$user             = get_current_user_id();
		//$screen           = get_current_screen();
		if ( isset( $_POST['wp_screen_options'] ) && is_array( $_POST['wp_screen_options'] ) ) {
			$default = sanitize_text_field($_POST['wp_screen_options']['value']);
			update_user_meta( $user, 'addons_per_page', $default );
		} else {
			$default = 10;
		}


		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes

		$args = array(
			'label'   => __( 'Addons per page', 'addons' ),
			'default' => $default,
			'option'  => 'addons_per_page'
		);
		add_screen_option( 'per_page', $args );
	}
}