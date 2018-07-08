<?php
namespace um_ext\um_instagram\libs\instagram;
/**
 * The Instagram API library
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * class Instagram_Connect
 * 
 * @since  1.0.0
 */
class Instagram_Connect {

    
	public $client_id;
	public $client_secret;
	public $callback_url;
	public $login_url;
	public $auth_called = 0;

	/**
	 *  init
	 * 
	 * @since  1.0.0
	 */
	function __construct() {
		
        add_action('template_redirect', array(&$this, 'load'),99);
        add_action('template_redirect', array(&$this, 'get_auth'),100);

	}


    function instagram_api( $api_data ) {
        if ( ! class_exists( '\Instagram' ) )
            require_once plugin_dir_path( dirname( __FILE__ ) ) . '../libs/instagram/api/Instagram.php';

        return new \Instagram( $api_data );
    }


	/**
	 * Prepare variables
	 * action hook: template_redirect
	 * 
	 * @since  1.0.0
	 */
	function load() {
		
		$this->client_id = UM()->options()->get( 'instagram_photo_client_id' );
		$this->client_secret = UM()->options()->get( 'instagram_photo_client_secret' );
		$this->callback_url = site_url('/');
		$this->callback_url = add_query_arg( 'um-connect-instagram', 'true', $this->callback_url );
		$this->callback_url = apply_filters( "um_instagram_callback_url", $this->callback_url );
	
	}

	/**
	 * Get authorization callback response
	 * action hook: template_redirect
	 * 
	 * @since  1.0.0
	 */
	function get_auth() {

		if ( isset( $_REQUEST['um-connect-instagram'] ) && $_REQUEST['um-connect-instagram'] == 'true' && isset( $_REQUEST['code'] ) && $this->auth_called == 0 ) {
			
			if ( $this->is_session_started() === FALSE )
				session_start();

			$instagram = $this->instagram_api( array(
				'apiKey'      => $this->client_id,
				'apiSecret'   => $this->client_secret,
				'apiCallback' => $this->callback_url
			));
			
			$token = false;
			
			if ( isset( $_SESSION['insta_access_token'] ) ) {
				
				$token = $_SESSION['insta_access_token'];
				$user = $_SESSION['insta_user'];
				  
			} else {

				$code = $_REQUEST['code'];
				$data = $instagram->getOAuthToken($code);
				//echo "<script>console.log(".json_encode( array( $data, $code, $this->client_id, $this->client_secret, $this->callback_url ) ).");</script>";
				$token = $data->access_token;
				$_SESSION['insta_access_token'] = $token;
				$_SESSION['insta_user'] = $data->user;
				
				$user = $_SESSION['insta_user'];
			
			}

			 

			if( ! empty( $token ) ){
				$profile_url = add_query_arg('profiletab','main', um_user_profile_url() );
				$profile_url = add_query_arg('um_action','edit', $profile_url );
				$profile_url = add_query_arg('um_ig_code', $code, $profile_url );
				
				update_user_meta( um_user('ID'), 'um_instagram_code', $token );
				
				wp_redirect( $profile_url );
			}

		    $this->auth_called++;
			
		}
		
	}
		
	
	/**
	 * Get Authorization URL
	 * @return string Login url for App authorization
	 * 
	 * @since  1.0.0
	 */
	function connect_url() {

		$instagram = $this->instagram_api( array(
			'apiKey'      => $this->client_id,
			'apiSecret'   => $this->client_secret,
			'apiCallback' => $this->callback_url
		));
		
		$this->login_url = $instagram->getLoginUrl();
		
		return $this->login_url;
		
	}

	/**
	 * Get current user's access token
	 * @param  string $metakey field meta key
	 * @param  int $user_id User ID
	 * @return string | boolean  returns token strings on success, otherwise return false when empty token
	 * 
	 * @since  1.0.0
	 */
	function get_user_token( $metakey = '', $user_id = 0 ) {

		if ( $this->is_session_started() === FALSE )
			session_start();

		if( um_user('ID') && empty( $user_id ) ){
			$user_id = um_user('ID');
		}
		
		$token = get_user_meta( $user_id, $metakey, true );

		$um_instagram_code = apply_filters('um_instagram_code_in_user_meta', true );
		if( ! $token && $um_instagram_code ){
			$token = get_user_meta( um_user('ID'), 'um_instagram_code', true );
		}

		if ( ! empty( $token ) || ! empty( $_REQUEST['um_ig_code'] ) || ! empty( $_SESSION['insta_access_token'] ) ) {

			if ( isset( $_SESSION['insta_access_token'] ) ){
				$token = $_SESSION['insta_access_token'];
				//unset( $_SESSION['insta_access_token'] );
			}
				
			if ( $token )
				return $token;

		}

		return false;
	}

	/**
	 * Checks if session has been started
	 * @return bool
	*/
	function is_session_started(){

		if ( php_sapi_name() !== 'cli' ) {
		        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
		            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
		        } else {
		            return session_id() === '' ? FALSE : TRUE;
		        }
		}

		return FALSE;
	}
		
}