<?php
namespace um_ext\um_instagram\admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ultimatemember.com/
 * @since      1.0.0
 *
 * @package    Um_Instagram
 * @subpackage Um_Instagram/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Um_Instagram
 * @subpackage Um_Instagram/admin
 * @author     Ultimate Member <support@ultimatemember.com>
 */
class Instagram_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        // Assets
        add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

        add_filter( 'um_core_fields_hook', array( &$this, 'register_builder_field' ), 10, 1 );
        add_filter( 'um_settings_structure', array( &$this, 'um_instagram_settings' ), 10, 1 );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'um-instagram', plugin_dir_url( __FILE__ ) . 'assets/css/um-instagram-admin.css', array(), um_instagram_version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'um-instagram', plugin_dir_url( __FILE__ ) . 'assets/js/um-instagram-admin.js', array( 'jquery' ), um_instagram_version, false );

	}


	/**
	 * Register Field with a UM filter hook called 'um_core_fields_hook'
	 * @param  array $core_fields returns built-in fields
	 * @return array     
	 * @since  1.0.0     
	 */
	public function register_builder_field( $core_fields ) {
		
		$core_fields['instagram_photo'] =  array(
				'name' => __('Instagram Photos','um-instagram'),
				'col1' => array('_title','_metakey','_help'),
				'col2' => array('_label','_public','_roles','_visibility'),
				'col3' => array('_required','_editable'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','um-instagram')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
		);

		return $core_fields;
	}


	public function um_instagram_settings( $settings ) {
        $settings['licenses']['fields'][] = array(
            'id'      		=> 'um_instagram_license_key',
            'label'    		=> __( 'Instagram License Key', 'um-instagram' ),
            'item_name'     => 'Instagram',
            'author' 	    => 'Ultimate Member',
            'version' 	    => um_instagram_version,
        );

        $key = ! empty( $settings['extensions']['sections'] ) ? 'instagram' : '';
        $settings['extensions']['sections'][$key] = array(
            'title'     => __( 'Instagram Photos', 'um-instagram' ),
            'fields'    => array(
                array(
                    'id'       		=> 'enable_instagram_photo',
                    'type'     		=> 'checkbox',
                    'label'   		=> __( 'Enable Instagram Photos','um-instagram' ),
                    'tooltip' 	=> __('Enable/disable the Instagram Photos field in the Form Builder and Profile page','um-instagram'),
                ),
                array(
                    'id'       		=> 'instagram_photo_client_id',
                    'type'     		=> 'text',
                    'label'    		=> __( 'Client ID','um-instagram' ),
                    'conditional'   => array( "enable_instagram_photo", '=', '1' ),
                    'size'  => 'medium',
                ),
                array(
                    'id'       		=> 'instagram_photo_client_secret',
                    'type'     		=> 'text',
                    'label'    		=> __('Client Secret','um-instagram' ),
                    'conditional'   => array( "enable_instagram_photo", '=', '1' ),
                    'size'  => 'medium',
                )
            )
        );

        return $settings;
    }

}
