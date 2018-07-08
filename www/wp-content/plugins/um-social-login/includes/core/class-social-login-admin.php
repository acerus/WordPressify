<?php
namespace um_ext\um_social_login\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Social_Login_Admin {

	function __construct() {
	
		$this->slug = 'ultimatemember';
		$this->pagehook = 'toplevel_page_ultimatemember';
		
		add_action('um_extend_admin_menu',  array(&$this, 'um_extend_admin_menu'), 100);

		add_action('admin_menu', array(&$this, 'prepare_metabox'), 20);

        add_action( 'load-post.php', array(&$this, 'add_metabox'), 9 );
        add_action( 'load-post-new.php', array(&$this, 'add_metabox'), 9 );
	}
	
	/***
	***	@prepare metabox
	***/
	function prepare_metabox() {
		
		add_action('load-'.$this->pagehook, array(&$this, 'load_metabox'));
		
	}
	
	/***
	***	@load metabox
	***/
	function load_metabox() {
		wp_register_script('um-chart', '//www.gstatic.com/charts/loader.js');
		wp_enqueue_script('um-chart');

		add_meta_box('um-metaboxes-social', __('Social Signups','um-social-login'), array(&$this, 'metabox_content'), $this->pagehook, 'normal', 'core');

	}
	
	/***
	***	@metabox content
	***/
	function metabox_content() {
		include_once um_social_login_path . 'includes/admin/templates/metabox.php';
	}
	
	/***
	***	@extends the admin menu
	***/
	function um_extend_admin_menu() {
	
		add_submenu_page( $this->slug, __('Social Login', $this->slug), __('Social Login', $this->slug), 'manage_options', 'edit.php?post_type=um_social_login', '', '' );
		
	}


    /***
     ***	@Init the metaboxes
     ***/
    function add_metabox() {
        global $current_screen;

        if( $current_screen->id == 'um_form' ){
            add_action( 'save_post', array(&$this, 'set_social_login_form_id'), 10, 2 );
        }

        if( $current_screen->id == 'um_social_login' ){
            add_action( 'add_meta_boxes', array(&$this, 'add_metabox_form'), 1 );
            add_action( 'save_post', array(&$this, 'save_metabox_form'), 11, 2 );
        }



    }

    /***
     ***	@add form metabox
     ***/
    function add_metabox_form() {

        add_meta_box('um-admin-social-login-buttons', __('Options','um-social-login'), array(&$this, 'load_metabox_form'), 'um_social_login', 'normal', 'default');
        add_meta_box('um-admin-social-login-shortcode', __('Shortcode','um-social-login'), array(&$this, 'load_metabox_form'), 'um_social_login', 'side', 'default');

    }

    /***
     ***	@load a form metabox
     ***/
    function load_metabox_form( $object, $box ) {
        $box['id'] = str_replace('um-admin-social-login-','', $box['id']);
        include_once um_social_login_path . 'includes/admin/templates/'. $box['id'] . '.php';
        wp_nonce_field( basename( __FILE__ ), 'um_admin_metabox_social_login_form_nonce' );
    }

    /***
     ***	@save form metabox
     ***/
    function save_metabox_form( $um_post_id, $um_post ) {
        global $wpdb;


        // validate nonce
        if ( !isset( $_POST['um_admin_metabox_social_login_form_nonce'] ) || !wp_verify_nonce( $_POST['um_admin_metabox_social_login_form_nonce'], basename( __FILE__ ) ) ) return $um_post_id;

        // validate post type
        if ( $um_post->post_type != 'um_social_login' ) return $um_post_id;

        // validate user
        $post_type = get_post_type_object( $um_post->post_type );
        if ( ! current_user_can( $post_type->cap->edit_post, $um_post_id ) )
            return $um_post_id;

        // save
        foreach ( $_POST['social_login'] as $k => $v ) {
            if ( strstr( $k, '_um_' ) )
                update_post_meta( $um_post_id, $k, $v );
        }

    }

    /***
     *** @assign registration form as overlay fields
     ***/
    function set_social_login_form_id( $um_post_id, $um_post ) {
        global $wpdb;

        if( $um_post->post_type == 'um_form' ) {

            if( isset( $_POST['_um_social_login_form'] ) && $_POST['_um_social_login_form'] > 0 ){
                $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s ", $um_post_id, '_um_social_login_form') );
                update_option('um_social_login_form_installed', $um_post_id );
            }

        }
    }

}