<?php
namespace um_ext\um_social_login\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Social_Login_Shortcode {

	function __construct() {
	
		add_shortcode( 'ultimatemember_social_login', array( &$this, 'ultimatemember_social_login' ) );

		add_filter( 'um_registration_user_role', array( &$this,'change_registration_role' ), 10, 2 );
	}

    /**
     * Social Login Shortcode
     *
     * @param array $args
     * @return string
     */
    function ultimatemember_social_login( $args = array() ) {
        $key = wp_generate_password( 5 , false );

	    if ( ! um_is_core_page( 'login' ) || ! isset( $_SESSION['_um_shortcode_id'] ) ) {
		    $_SESSION['_um_shortcode_id'] = $key;
		    foreach ( $_SESSION as $k => $value ) {
			    if ( strpos( $k, '_um_social_login_key_' ) === 0 ) {
				    unset( $_SESSION[ $k ] );
			    }
		    }
		    $_SESSION[ '_um_social_login_key_' . $key ] = $args['id'];
	    }

        $redirect_url = UM()->permalinks()->get_current_url();
        $redirect_url = remove_query_arg( array( 'code' , 'state' ), $redirect_url );
        $_SESSION['um_social_login_redirect'] = $redirect_url;

        UM()->Social_Login_API()->shortcode_id = $key;

        return $this->load( $args );
    }


    /**
     * Get shortcode post meta
     *
     * @param $id
     * @return mixed
     */
    function get_meta( $id ) {
        $meta = get_post_custom( $id );
        if ( $meta && is_array( $meta ) ) {
            foreach( $meta as $k => $v ) {
                $k = str_replace('_um_','',$k);
                $array[$k] = $v[0];
            }
        }
        return $array;
    }


    /**
     * Load a module with global function
     *
     * @param $args
     * @return string|void
     */
    function load( $args ) {
        $once = false;

        $networks = UM()->Social_Login_API()->networks;
        $postmeta = $this->get_meta( $args['id'] );

        foreach( $networks as $provider => $arr ) {
            if ( isset( $postmeta['enable_'.$provider][0] ) && $postmeta['enable_'.$provider][0] != 1 ) {
                unset( $networks[$provider] );
            }
        }

        if ( !$networks ) return;

        $o_networks = $networks;

        $defaults = array();

        $args = wp_parse_args( $args, $defaults );
        $args = array_merge( $args, $postmeta );
        extract( $args, EXTR_SKIP );

        if ( !$show_for_members && is_user_logged_in() ) return;

        ob_start();

        $file       = um_social_login_path . 'templates/buttons.php';
        $theme_file = get_stylesheet_directory() . '/ultimate-member/templates/social-login/buttons.php';

        if( file_exists( $theme_file ) ) {
            $file = $theme_file;
        }

        if( file_exists( $file ) ) {
            if( $once ) {
                require_once $file;
            }
            else {
                require $file;
            }
        }

        $output = ob_get_contents();
        ob_end_clean();
        return $output;

    }


    /**
     * Additional arguments for user registration
     *
     * @param string $role
     * @param array $args
     * @return string
     */
	function change_registration_role( $role, $args ) {
		
		if ( isset( $_SESSION[ '_um_shortcode_id' ] ) && isset( $_SESSION['um_social_is_shortcode'] ) ) {
			$key = $_SESSION[ '_um_shortcode_id' ];
			$um_post_id = $_SESSION[ '_um_social_login_key_'.$key ];
			$assigned_role = get_post_meta( intval( $um_post_id ), '_um_assigned_role', true );
			if ( ! empty( $assigned_role ) && $_SESSION['um_social_is_shortcode'] == true ) {
                $role = $assigned_role;
			}

		}

		return $role;
	}
}