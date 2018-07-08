<?php

/**
 * Fired during plugin activation
 *
 * @link       http://aum.im
 * @since      1.0.0
 *
 * @package    Featured_Image_Generator
 * @subpackage Featured_Image_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Featured_Image_Generator
 * @subpackage Featured_Image_Generator/includes
 * @author     Aum Watcharapon <aum_kub@hotmail.com>
 */
class Featured_Image_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$upload_dir = wp_upload_dir();
		$fig_dirname = $upload_dir['basedir'].'/fig_uploads';
		if ( ! file_exists( $fig_dirname ) ) {
			if( ! wp_mkdir_p( $fig_dirname ) ){
				new WP_Error;
			}
		}

		if ( ! get_option('fig_unsplash_api') ){
			update_option('fig_unsplash_api', '3ec4204d6826baa361216f3055e68bb4bb0f5b82095ba81c8ee6a8b690481c3c');
		}

		if ( ! get_option('fig_thumb_defaut_size_width') ){
			update_option('fig_thumb_defaut_size_width', 500);
		}

		if ( ! get_option('fig_thumb_defaut_size_height') ){
			update_option('fig_thumb_defaut_size_height', 300);
		}

		if ( ! get_option('fig_font_family') ){
			update_option('fig_font_family', 'Open Sans');
		}				
		
	}

}
