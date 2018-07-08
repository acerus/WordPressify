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

class PIPESViewSettings extends View {
	public $configs = array();
	
	public function __construct(){
		parent::__construct();
	}
	
	public function display(){
		$model = $this->getModel();
		$this->configs = $model->getTable();
		$this->area = ogb_common::ip_info("Visitor", "Country");
		do_action( 'wppipes_loaded_ads' );
		parent::display();
	}
}