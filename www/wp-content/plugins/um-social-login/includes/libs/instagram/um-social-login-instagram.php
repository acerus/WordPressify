<?php

if( ! class_exists('Instagram') )
require_once um_social_login_path . 'includes/libs/instagram/api/Instagram.php';


class UM_Social_Login_Instagram {

	function __construct() {
		
		add_action('init', array(&$this, 'load'));
		
		add_action('init', array(&$this, 'get_auth'));

		add_action('template_redirect', array( &$this,'redirect_authentication'), 1 );

	}

	function redirect_authentication(){
		
		if( isset( $_REQUEST['um_social_login'] ) &&  $_REQUEST['um_social_login'] == "instagram" ){
			return wp_redirect( $this->login_url() );
		}

	}


	/***
	***	@load
	***/
	function load() {
		
		$this->client_id = trim( UM()->options()->get('instagram_client_id') );
		$this->client_secret = trim( UM()->options()->get('instagram_client_secret') );
		if( method_exists ( 'UM_Social_Login_API','get_redirect_url' ) ){
			$this->callback_url = UM()->Social_Login_API()->get_redirect_url();
		}
		$this->callback_url = add_query_arg( 'provider', 'instagram', $this->callback_url );
		$this->callback_url = remove_query_arg( 'code', $this->callback_url );
		
	}

	/***
	***	@Get auth
	***/
	function get_auth() {
		if ( isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'instagram' && isset($_REQUEST['code']) ) {
	
			$instagram = new Instagram( array(
				'apiKey'      => $this->client_id,
				'apiSecret'   => $this->client_secret,
				'apiCallback' => $this->callback_url
			));
			
			$token = false;
			
			if (isset($_SESSION['insta_access_token'])) {
				
				$token = $_SESSION['insta_access_token'];
				$user = $_SESSION['insta_user'];
				  
			} else {
					$code = $_REQUEST['code'];
					$data = $instagram->getOAuthToken($code);
					
					if( isset( $data->access_token ) ){
						$token = $data->access_token;
						$_SESSION['insta_access_token'] = $token;
						$_SESSION['insta_user'] = $data->user;
					}
					$user = $_SESSION['insta_user'];

					if ( isset( $data->code ) && $data->code == 400 ) {
						wp_die( 'UM Social Login - Instagram: '.$data->error_type.' - '.$data->error_message,'UM Social Login - Instagram Error', array('back_link' => true ) );
					}
				
				
			}
			
			$instagram->setAccessToken($token);

			$profile = (array) $user;
			

			$username = isset( $profile['username'] )? $profile['username']:'';
			$profile_id = isset( $profile['id'] )? $profile['id']:'';
			$profile_picture = isset( $profile['profile_picture'] )? $profile['profile_picture']:'';

			// prepare the array that will be sent
			$profile['username'] = $username;
			$profile['user_login'] = $username;
			$profile['email_exists'] = '';
			$profile['username_exists'] = '';
				
			// provider identifier
			$profile['_uid_instagram'] = $profile_id;
				
			$profile['_save_synced_profile_photo'] = $profile_picture;
				
			$profile['_save_instagram_handle'] = '@' . $username;
			$profile['_save_instagram_link'] = 'https://instagram.com/' . $username;
			$profile['_save_instagram_photo_url_dyn'] = $profile_picture;

			// have everything we need?
            UM()->Social_Login_API()->resume_registration( $profile, 'instagram' );
			
		}
		
	}
		
	/***
	***	@get login uri
	***/
	function login_url() {
		if( ! isset( $_REQUEST['um_social_login'] ) ){
			$this->login_url = um_get_core_page('login');
			$this->login_url = add_query_arg('um_social_login','instagram', $this->login_url );
			$this->login_url = add_query_arg('um_social_login_ref', UM()->Social_Login_API()->shortcode_id, $this->login_url );
			if( isset( $_SESSION['um_social_login_redirect'] ) ){
				if ( ! isset( $_REQUEST['code'] ) && ! isset( $_REQUEST['state'] )  ) {
				$this->login_url = add_query_arg('redirect_to', $_SESSION['um_social_login_redirect'], $this->login_url );
					$_SESSION['um_social_login_redirect_after'] = $_SESSION['um_social_login_redirect'];
				}
			}
		}else{
			$instagram = new Instagram(array(
				'apiKey'      => $this->client_id,
				'apiSecret'   => $this->client_secret,
				'apiCallback' => $this->callback_url
			));
			
			$this->login_url = $instagram->getLoginUrl();
		}
		
		return $this->login_url;
		
	}
		
}