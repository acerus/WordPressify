<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class UM_Online_API {
    var $users;
    private static $instance;

    static public function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


	function __construct() {
        // Global for backwards compatibility.
        $GLOBALS['um_online'] = $this;
        add_filter( 'um_call_object_Online_API', array( &$this, 'get_this' ) );

        if ( UM()->is_request( 'frontend' ) ) {
            $this->enqueue();
            $this->shortcode();
        }

        $this->users = get_option( 'um_online_users' );
        $this->schedule_update();

		add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );
		add_action( 'init', array( &$this, 'log' ), 1 );

        add_filter( 'um_rest_api_get_stats', array( &$this, 'rest_api_get_stats' ), 10, 1 );

		require_once um_online_path . 'includes/core/um-online-widget.php';
		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

        add_action( 'um_messaging_conversation_list_name', array( &$this, 'messaging_show_online_dot' ) );

		add_action( 'um_delete_user',  array( $this, 'clear_online_user' ), 10, 1 );
	}


	/**
	 * @param $user_id
	 */
	function clear_online_user( $user_id ) {
		$online_users = get_option( 'um_online_users', array() );

		if ( ! empty( $online_users[ $user_id ] ) ) {
			unset( $online_users[ $user_id ] );

			update_option( 'um_online_users', $online_users );

			update_option( 'um_online_users_last_updated', time() );
		}

	}


    function messaging_show_online_dot() {
        if ( $this->is_online( um_user('ID') ) ) {
            echo '<span class="um-online-status online"><i class="um-faicon-circle"></i></span>';
        } else {
            echo '<span class="um-online-status offline"><i class="um-faicon-circle"></i></span>';
        }
    }


    function rest_api_get_stats( $response ) {
        $total_online = count( $this->get_users() );
        $response['stats']['total_online'] = $total_online;

        return $response;
    }



    function get_this() {
        return $this;
    }



    /**
     * @return um_ext\um_online\core\Online_Enqueue()
     */
    function enqueue() {
        if ( empty( UM()->classes['um_online_enqueue'] ) ) {
            UM()->classes['um_online_enqueue'] = new um_ext\um_online\core\Online_Enqueue();
        }
        return UM()->classes['um_online_enqueue'];
    }


    /**
     * @return um_ext\um_online\core\Online_Shortcode()
     */
    function shortcode() {
        if ( empty( UM()->classes['um_online_shortcode'] ) ) {
            UM()->classes['um_online_shortcode'] = new um_ext\um_online\core\Online_Shortcode();
        }
        return UM()->classes['um_online_shortcode'];
    }



	/***
	***	@Init
	***/
	function init() {

		// Actions
		require_once um_online_path . 'includes/core/actions/um-online-profile.php';
		
		// Filters
		require_once um_online_path . 'includes/core/filters/um-online-fields.php';

	}
	
	/***
	***	@Logs online user
	***/
	function log() {
		
		// Guest or not on frontend
		if ( is_admin() || !is_user_logged_in() )
			return;
		
		// User privacy do not allow that
		$_hide_online_status = get_user_meta( get_current_user_id(), '_hide_online_status', true );
		if ( $_hide_online_status == 1 ) {
			return;
		}
		
		// We have a logged in user
		// Store the user as online with a timestamp of last seen
		$this->users[ get_current_user_id() ] = current_time('timestamp');
		
		// Save the new online users
		update_option('um_online_users', $this->users );
	
	}


	/**
	 * Gets users online
	 *
	 * @return bool|mixed|void
	 */
	function get_users() {
		if ( isset( $this->users ) && is_array( $this->users ) && ! empty( $this->users ) ) {
			arsort( $this->users ); // this will get us the last active user first
			return $this->users;
		}
		return false;
	}
	
	/***
	***	@Checks if user is online
	***/
	function is_online( $user_id ) {
		if ( isset( $this->users[ $user_id ] ) )
			return true;
		return false;
	}
	
	/***
	***	@Update the online users
	***/
	private function schedule_update() {
		$this->run_update();
	}

	/***
	***	@Execute updating the list every x interval
	***/
	public function run_update() {

		// Send a maximum of once per period
		$minute_interval = apply_filters('um_online_interval', 15 ); // minutes
		$last_send = $this->get_last_update();
		if( $last_send && $last_send > strtotime( "-{$minute_interval} minutes" ) )
			return;
			
		// We have to check if each user was last seen in the previous x
		if ( is_array( $this->users ) ) {
			foreach( $this->users as $user_id => $last_seen ) {
				if ( ( current_time('timestamp') - $last_seen ) > ( 60 * $minute_interval ) ) {
					// Time now is more than x since he was last seen
					// Remove user from online list
					unset( $this->users[ $user_id ] );
				}
			}
			update_option('um_online_users', $this->users );
		}
	
		update_option( 'um_online_users_last_updated', time() );

	}
	
	private function get_last_update() {
		return get_option( 'um_online_users_last_updated' );
	}
	
	function widgets_init() {
		register_widget( 'um_online_users' );
	}
	
}

//create class var
add_action( 'plugins_loaded', 'um_init_online', -10, 1 );
function um_init_online() {
    if ( function_exists( 'UM' ) ) {
        UM()->set_class( 'Online_API', true );
    }
}
