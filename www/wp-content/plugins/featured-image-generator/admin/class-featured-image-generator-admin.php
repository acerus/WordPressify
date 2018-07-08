<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://aum.im
 * @since      1.0.0
 *
 * @package    Featured_Image_Generator
 * @subpackage Featured_Image_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Featured_Image_Generator
 * @subpackage Featured_Image_Generator/admin
 * @author     Aum Watcharapon <aum_kub@hotmail.com>
 */
class Featured_Image_Generator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->init_hooks();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Featured_Image_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Featured_Image_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/featured-image-generator-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Featured_Image_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Featured_Image_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/featured-image-generator-admin.js', array( 'jquery' ), $this->version, false );        
		wp_enqueue_media();
        wp_localize_script( 
            $this->plugin_name, 
            'figAjax', 
            array(
                'url'   => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'fig_nonce' ),
                'fig_downloaded' => get_option( 'fig_downloaded' ),
                'fig_gen_url' => plugin_dir_url( __FILE__ ) . 'gd-lib.php?',
                'fig_unsplash_api' => get_option( 'fig_unsplash_api' ),
                'fig_thumb' => get_option( 'fig_thumb_defaut_size_width' ),
                'fig_thumb_defaut_size_width' => get_option( 'fig_thumb_defaut_size_width' ),
                'fig_thumb_defaut_size_height' => get_option( 'fig_thumb_defaut_size_height' ),
                'fig_font_family' => get_option( 'fig_font_family' ),
            )
        );

	}

	public static function init_hooks() {	
		add_action( 'admin_menu', array( 'Featured_Image_Generator_Admin', 'admin_create_menu' ) );	
		add_action( 'admin_init', array( 'Featured_Image_Generator_Admin', 'fig_setting_fields' ) );				

		add_action( 'wp_ajax_fig_load_popup', array( 'Featured_Image_Generator_Admin', 'fig_load_popup' ) ); 		
		add_action( 'wp_ajax_fig_save_image', array( 'Featured_Image_Generator_Admin', 'fig_save_image' ) ); 		
		add_action( 'wp_ajax_fig_save_after_generate_image', array( 'Featured_Image_Generator_Admin', 'fig_save_after_generate_image' ) ); 				
	}

	public static function admin_create_menu(){
		add_options_page( 
			'Featured Image Generator',
			'Featured Image Generator',
			'administrator',
			'featured-image-generator-setting-page',  
			array( 'Featured_Image_Generator_Admin', 'fig_setting_page' )
			);
	}

	public static function fig_load_popup(){	

		$upload_dir = wp_upload_dir();
		$fig_dirname = $upload_dir['basedir'].'/fig_uploads';
		if ( ! file_exists( $fig_dirname ) ) {
			if( ! wp_mkdir_p( $fig_dirname ) ){
			}
		}

		if ( ! get_option('fig_thumb_defaut_size_width') ){
			update_option('fig_thumb_defaut_size_width', 500);
		}

		if ( ! get_option('fig_thumb_defaut_size_height') ){
			update_option('fig_thumb_defaut_size_height', 300);
		}

		?>
		<div class="fig_wrap">
			<div class="fig_tab">
				<ul>
					<li><a href="#" for="editor"><i class="flaticon flaticon-layers"></i> Editor</a></li>
					<li><a href="#" for="unsplash" class="active">Unsplash</a></li>
					<li><a href="https://designilcode.com/" for="pixabay" target="_blank">Pixabay</a></li>
				</ul>
				<input type="hidden" name="fig_active" value="unsplash">
			</div>
			<div class="fig_container" id="fig_container">		
				<div class="fig_sub_container">
					<div class="fig_search_body">
						<div class="fig_search_result"></div>
						<input type="text" name="fig_search_txt" class="fig_search_txt"  value="" placeholder="Enter Keyword"  tabindex="1">
						<a class="button fig_search" tabindex="2">Search Photos</a>
						<div class="fig_or">OR</div>
						<a class="button fig_set_button" id="upload-btn" href="#" tabindex="3">Select / Upload Image</a>						
						<div class="fig_document">
							<a href="https://www.designilcode.com/free-fig-doc/" target="_blank">Document</a>
						</div>
						<button type="button" class="button-link media-modal-close fig_popup_close" tabindex="4"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span></button>
					</div>

					<div class="fig_editor">

						<div class="fig_editor_wrap">

							<ul class="layer-list">						

								<li>
									<a href="#" class="layer active" for="filter-1">Filter</a>

									<div class="layer-container" id="filter-1">
										
										<div class="fig_editor_filter">
											<input type="checkbox" name="fig_filter" class="fig_value" value="1"> Filter
											<div class="fig_filter_container">
												<div class="container--filter">
													<h4>Filter Color</h4>
													<input type="color" name="fig_filter_color" value="#000000" class="fig_value">		
												</div>
												<div class="container--filter">
													<h4>Filter Opacity</h4>								
													<div class="slider-wrap">
														<em>0</em> 
														<input type="range" min="0" max="100" value="50" name="fig_filter_opacity" class="fig_value" step="5"> 
														<em>100</em>
													</div>
												</div>
											</div>						
										</div>

									</div>		
								</li>
								<li>						
									<a href="#" class="layer active" for="caption-1">Caption</a>

									<div class="layer-container" id="caption-1">

										<input type="text" name="fig_caption" value="" class="fig_value" placeholder="Caption">

											<h4>Left<h4/>
											<div class="slider-wrap">
												<em>10px</em> 
												<input type="range" min="10" max="1000" value="<?php echo ( get_option( 'fig_thumb_defaut_size_width' ) / 2 ); ?>" name="fig_caption_x" class="fig_value" step="5"> 
												<em>1000px</em>
											</div>

											<h4>Top</h4>
											<div class="slider-wrap">
												<em>10px</em> 
												<input type="range" min="10" max="1000" value="<?php echo ( get_option( 'fig_thumb_defaut_size_height' ) / 2 ); ?>" name="fig_caption_y" class="fig_value" step="5"> 
												<em>1000px</em>
											</div>

											<h4>Font Size</h4>
											<div class="slider-wrap">
												<em>10px</em> 
												<input type="range" min="10" max="150" value="30" name="fig_caption_size" class="fig_value" step="5"> 
												<em>250px</em>
											</div>

											<h4>Color</h4>
											<input type="color" name="fig_caption_color" value="#ffffff" class="fig_value">

										</div>
									</li>

								</ul>
								
								<a href="https://www.designilcode.com/" target="_blank" class="fig_add_layer">Add Layer</a>

							</div>

							<div class="thumb_area">

								<div class="thumb_size--container">
									<div class="container--thumb">
										<h4>Thumb Width</h4>
										<input type="number" value="<?php echo get_option( 'fig_thumb_defaut_size_width' ); ?>" name="fig_canvas_width" class="fig_value fig_thumb_size"> 						
									</div>

									<div class="container--thumb">
										<h4>Thumb Height</h4>
										<input type="number" value="<?php echo get_option( 'fig_thumb_defaut_size_height' ); ?>" name="fig_canvas_height" class="fig_value fig_thumb_size" step="10"> 											
									</div>
								</div>

								<canvas id="fig_canvas" width="<?php echo get_option( 'fig_thumb_defaut_size_width' ); ?>" height="<?php echo get_option( 'fig_thumb_defaut_size_height' ); ?>" style="border: 1px solid #ccc;"></canvas>								

								<div class="fig_editor_button">					
									<input type="button" class="button button-primary submit fig_save" value="Save to Media">									
								</div>

								<a href="https://www.designilcode.com/" target="_blank" class="fig_template_area">
									<div class="fig_template_load--container">
										<select name="fig_template" class="fig_template">
										<option value="">Select Template</option>
										</select>
									</div>
									<div class="fig_template_save--container">
										<span class="button button-save-template">
											<i class="flaticon flaticon-save"></i> 
											<span>GO</span>
										</span>
									</div>
								</a>

							</div>

							<input type="hidden" name="fig_canvas_image" value="">
						</div>

						<div class="fig_select_container">
							<div class="fig_result">						
							</div>
							<div class="fig_result_pagination">
								<button href="#" disabled class="button fig_prev"> < Prev </button>
								<button href="#" disabled class="button fig_next"> Next > </button>
							</div>
							<div class="fig_bg">
								<div>
									<div class="spinner-load">
										<div class="double-bounce1"></div>
										<div class="double-bounce2"></div>
									</div>				
								</div>
							</div>
						</div>						
					</div>	
				</div>	
			<div class="fig_popup_bg"></div>	
		</div>
		<?php
		wp_die();
	}

	public static function fig_setting_page()
	{
		?>
		<div class="wrap">
			<h1>Featured Image Generator Setting</h1>
			<form method="post" action="options.php">
				<?php					
				settings_fields("section");
				do_settings_sections("theme-option");      
				submit_button(); 
				?>
			</form>
		</div>
		<?php
	}

	public static function fig_setting_fields(){
		add_settings_section("section", "All Settings", null, "theme-option");
		add_settings_field("fig_unsplash_api", "Unsplash API", array("Featured_Image_Generator_Admin", "display_unsplash_api_element"), "theme-option", "section");
		add_settings_field("fig_thumb_defaut_size_width", "Thumb Size ( Width x Height )", array("Featured_Image_Generator_Admin", "display_thumb_size_element"), "theme-option", "section");
		add_settings_field("fig_font_family", "Font Family ( Google Font ) ", array("Featured_Image_Generator_Admin", "display_font_family_element"), "theme-option", "section");
		register_setting("section", "fig_unsplash_api");
    	register_setting("section", "fig_thumb_defaut_size_width");
    	register_setting("section", "fig_thumb_defaut_size_height");
    	register_setting("section", "fig_font_family");
	}

	public static function display_unsplash_api_element(){
	?>
    	<input type="text" name="fig_unsplash_api" id="fig_unsplash_api" placeholder="Application ID" class="regular-text" value="<?php echo get_option('fig_unsplash_api'); ?>" />
    	<div class="fig_register_here">
    		<a href="https://unsplash.com/developers" target="_blank">Get API Here</a>
    	</div>
    <?php		
	}

	public static function display_thumb_size_element(){
	?>
		<input name="fig_thumb_defaut_size_width" type="number" id="fig_thumb_defaut_size_width" placeholder="Width" value="<?php echo get_option('fig_thumb_defaut_size_width'); ?>" step="1" class="fig-size-text"> px 
		<input name="fig_thumb_defaut_size_height" type="number" id="fig_thumb_defaut_size_height" placeholder="Height" value="<?php echo get_option('fig_thumb_defaut_size_height'); ?>" step="1" class="fig-size-text text-height"> px 
    <?php		
	}

	public static function display_font_family_element(){
		$font_family = array(
			"Open+Sans" => "Open Sans",
			"Roboto" => "Roboto",
			"Slabo+27px" => "Slabo 27px",
			"Oswald" => "Oswald",
			"Roboto+Condensed" => "Roboto Condensed",
			"Source+Sans Pro" => "Source Sans Pro",
			"Montserrat" => "Montserrat",
			"Raleway" => "Raleway",
			"PT+Sans" => "PT Sans",
			"Kanit" => "Kanit",
			"Trirong" => "Trirong",
			"Prompt" => "Prompt",
			"Athiti" => "Athiti",
			"Mitr" => "Mitr",
			"Maitree" => "Maitree",
			"Sriracha" => "Sriracha",
			);
	?>
		<select name="fig_font_family" id="fig_font_family">
			<?php
			foreach ($font_family as $key => $val) {
				if ( get_option('fig_font_family') == $key )
					$sel = "selected";
				else
					$sel = "";
			?>
				<option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $val; ?></option>
			<?php
			}
			?>
		</select>	
		
		<div>
			<a href="https://www.paypal.me/watcharapon/0usd" target="_blank" style="text-decoration: none; display:inline-block; margin-top:5px;">Support me :)</a>
		</div>		
    <?php		
	}

	public static function fig_save_image(){

		check_ajax_referer( 'fig_nonce', 'nonce' );

		if ( true ) {

			$data = $_POST["data"];
			$id = $data["id"];
			$url = $data["url"];

			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $url, $matches ); 

			$file_array['name'] = basename( $matches[0] );
			$file_array['tmp_name'] = download_url( $url ); 

			if ( empty($file_array['name']) ) {
				if ( strpos($url, '?') !== false) {
					$file_name = explode('?', basename( $url ) );
					$file_array['name'] = $file_name[0] . '.jpg';
				}
			}

			if ( is_wp_error( $file_array['tmp_name'] ) ) { 
				return $file_array['tmp_name']; 
			} 
			
			$upload_dir = wp_upload_dir();
			$new_file =  $upload_dir['basedir'] . '/fig_uploads/' . $file_array["name"];
			$new_file_uri = "";

			if( $move_new_file = @ copy( $file_array['tmp_name'], $new_file ) ) {
				$new_file_uri = $upload_dir['baseurl'] . '/fig_uploads/' . $file_array["name"];
				unlink( $file_array['tmp_name'] );	

				$downloaded = get_option( 'fig_downloaded' );

				if ( empty( $downloaded ) ) {
					$downloaded = array( $id );
				}else{
					array_push( $downloaded, $id );
					array_unique( $downloaded );
				}

				update_option( 'fig_downloaded', $downloaded ); 

				if ( is_wp_error( $id ) ) { 
					@unlink( $file_array['tmp_name'] ); 
					return $id; 
				} 

				$response['file'] = $new_file_uri;
				$response['success'] = true;		
				wp_send_json( $response );
			}else{
				wp_send_json_error( array( 'error' => $custom_error ) );		
			}					

		}

		wp_die();
	}

	public static function fig_save_after_generate_image()
	{

		check_ajax_referer( 'fig_nonce', 'nonce' );

		if ( true ) {

			$img = $_POST['imgBase64'];
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$fileData = base64_decode($img);

			$upload_dir = wp_upload_dir();
			$file_name = 'fig-'.date("d-m-Y_H-i-s").'.jpg';

			$new_file =  $upload_dir['basedir'] . '/fig_uploads/' . $file_name;
			file_put_contents($new_file, $fileData);

			$file_array['tmp_name'] = $new_file;
			$file_array['name'] = $file_name;
			$media_id = media_handle_sideload( $file_array, '', '' ); 
			
		}

		wp_die();
	}	
}
