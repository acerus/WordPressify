<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: view.php 139 2014-01-23 10:09:44Z phonglq $
 * @author               thimpress.com
 * @copyright            2014 thimpress.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
require_once dirname( dirname( dirname( __FILE__ ) ) ) . DS . 'includes' . DS . 'view.php';

class PIPESViewPipes extends View {
	public $items = array();
	public $itemsTable = null;

	public function __construct() {
		parent::__construct();
	}

	public function display() {
		$model            = $this->getModel();
		$this->itemsTable = $model->getTable();
		do_action( 'wppipes_loaded_ads' );
		//$this->_layout= 'bs3';
		parent::display();
	}

	public function on_load_page() {
		$user   = get_current_user_id();
		$screen = get_current_screen();

		// Add Help Tabs
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab0',
			'title'   => __( 'Manage your Pipes' ),
			'content' => '<p>' . __( '<strong>A Pipe</strong> - is a pipeline of data migration from SOURCE to DESTINATION. This screen contains the list of created or imported Pipes. Basically, you can do all management actions on this screen.' ) . '</p>' .
				'<p>' . __( '<strong>Edit</strong> - click on the Pipe title to go to Pipe Edit form. You can also hover on a pipe to see the Edit link.' ) . '</p>' .
				'<p>' . __( '<strong>Delete</strong> - delete a pipe permanently out of your WP Pipes plugin.' ) . '</p>' .
				'<p>' . __( '<strong>Test</strong> - test your Pipe manually.' ) . '</p>' .
				'<p>' . __( '<strong>Test in Update mode</strong> - test your Pipe manually in force-update mode.' ) . '</p>' .
				'<p>' . __( '<strong>Export</strong> - export a pipe. See more at Import Pipes and Export Pipes tabs.' ) . '</p>'
		) );
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab1',
			'title'   => __( 'Export Pipes' ),
			'content' => '<p>' . __( 'You can export the settings of a single Pipe or multiple Pipes to share / sell it OR <strong>Import it later</strong>.' ) .
				'<p>' . __( '<strong>Export single Pipe</strong> - by hover on a pipe and select Export, you will be able to download a .pipe file to your local computer which contains your Pipe settings.' ) .
				'<p>' . __( '<strong>Export multiple Pipes</strong> - by checking some pipes and select Bulk Actions > Export, you will be able to download a .pipe file to your local computer which contains settings for your selected Pipes.' ) . '</p>'
		) );
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab2',
			'title'   => __( 'Import Pipes' ),
			'content' => '<p>' . __( 'You can import .pipe file which contains settings from single or multiple Pipes over this feature. These .pipe files can be got from your export action before or from friend or <a href="http://foob.la/pipestore" target="_blank">Pipe Marketplace</a>.' ) .
				'<p>' . __( '<strong>To Import A Pipe</strong> - click Import button on the top side of the page > browse the .pipe file from your local computer > click Import and single or multiple Pipes will be imported to your WP Pipes plugin.' )
		) );

		// Help Sidebar
		$screen->set_help_sidebar(
			'<p>' . __( '<strong>For more information:</strong>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.youtube.com/watch?v=zLHFqAHQj2A">Video on Creating a Pipe</a>' ) . '</p>' .
			'<p>' . __( '<a href="http://thimpress.com/forums/forum/plugins/forum-pipes/">Support Forums</a>' ) . '</p>'
//			. '<p>' . __( '<a href="http://www.youtube.com/v/TO3g-_wErEI?autoplay=1&vq=hd1080" class="button button-primary"><span class="fa fa-youtube-play" title=""></span> Video Tutorial</a>' ) . '</p>'
		);


		if ( isset( $_POST['wp_screen_options'] ) && is_array( $_POST['wp_screen_options'] ) ) {
			$default = sanitize_text_field($_POST['wp_screen_options']['value']);
			update_user_meta( $user, 'pipes_per_page', $default );
		} else {
			$default = 10;
		}

		// Add meta box
		add_meta_box( 'pipes-items-helpbox-1',
			__( 'Check this if you are new to Pipes' ),
			array( $this, 'metabox_help' ),
			$screen,
			'items_top'
		);

		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		$args = array(
			'label'   => __( 'Pipes per page', 'pipes' ),
			'default' => $default,
			'option'  => 'pipes_per_page'
		);
		add_screen_option( 'per_page', $args );
	}

	function metabox_help( $data = '' ) {
		$exist_db = ogb_common::check_existed_db();
		$area = ogb_common::ip_info("Visitor", "Country");
		require_once( OBGRAB_HELPERS . 'requirements.php' );
		$requirements = new AppRequirements;
		?>
		<div class="welcome-panel" id="welcome-panel">
			<input type="hidden" value="d497f3bcac" name="welcomepanelnonce" id="welcomepanelnonce">
			<a id="dismiss_help_btn" href="#" class="welcome-panel-close">Dismiss</a>

			<div class="welcome-panel-content">
				<h3>Hello, I'm WP Pipes! I'm here to do automatic data migration jobs for you.</h3>

				<p class="about-description">Includes: post to social networks, post to wordpress.com or blogspot, create RSS Feed, CSV to WooCommerce, ...</p>

				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4>Get Started</h4>
						<a href="admin.php?page=pipes.pipe" class="button button-primary button-hero">Create a new Pipe</a>

						<p class="hide-if-no-customize">
							or
						</p>
						<a href="admin.php?page=pipes.pipes&task=import_from_file&url=<?php _e(plugins_url()); ?>/wp-pipes/sample_pipes/sample-with-techcrunch.pipe" class="button button-primary button-default">Import Sample Pipe</a>
						<?php if($exist_db): ?>
							<p style="color: red;">
								Notice: the install process was not completed! Please click below button to create necessary tables.
							</p>
							<a href="admin.php?page=pipes.pipes&task=create_tables" class="button button-primary button-default">Create Tables</a>
						<?php endif; ?>
					</div>
					<div class="welcome-panel-column">
						<h4>Next Steps</h4>
						<ul>
							<?php if ( is_array($requirements->checkRequirements()) ):?>
								<li style="color:red;">
									<i class="fa fa-wrench fa-fw fa-lg"></i>
									<a href="admin.php?page=pipes.settings" style="color:red;">Check requirements</a>
								</li>
							<?php endif; ?>
							<li>
								<i class="fa fa-flash fa-fw fa-lg"></i>
								<a href="admin.php?page=pipes.settings">Control cronjob/schedule</a>
							</li>
							<li>
								<i class="fa fa-download fa-fw fa-lg"></i>
								<a href="javascript:void();" onclick="jQuery('#contextual-help-link').click();jQuery('#tab-link-my_help_tab1 a').click();">Export your Pipes to share or sell</a>
							</li>
							<li>
								<i class="fa fa-puzzle-piece fa-fw fa-lg"></i>
								<a href="admin.php?page=pipes.plugins">Empower me by adding more addons.</a>
							</li>
							<li>
								<i class="fa fa-shopping-cart fa-fw fa-lg"></i>
								<a href="http://foob.la/pipestore" target="_blank">Checkout Pipes Marketplace</a>
							</li>
							<?php if($area != '' && $area != 'Vietnam'){?>
								<li>
									<i class="fa fa-hourglass-start fa-fw fa-lg"></i>
									<a href="http://foob.la/sgcron" style="color: red;" target="_blank">How to Set up Schedule for each pipe!</a>
								</li>
							<?php } ?>
						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h4>Explore my power</h4>
						<ul>
							<li>
								Create Posts from RSS Feed, Youtube, Email...
							</li>
							<li>
								Create RSS Feed from Posts, WooCommerce...
							</li>
							<li>
								Create WooCommerce Products from Amazon, CDV files...
							</li>
							<li>
								Tweet on Twitter from RSS feeds, post...
							</li>
							<li>
								Post content onto WordPress.com, Blogspot...
							</li>
							<li>

							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

}