<?php

class UM_Social_Login_VK {

	function __construct() {
		
		add_action('init', array(&$this, 'load'));
		
		add_action('init', array(&$this, 'get_auth'));

		add_action('template_redirect', array( &$this,'redirect_authentication'), 1 );

	}

	function redirect_authentication(){
		
		if( isset( $_REQUEST['um_social_login'] ) &&  $_REQUEST['um_social_login'] == "vk" ){
			return wp_redirect( $this->login_url() );
		}

	}

	/***
	***	@load
	***/
	function load() {
		require( um_social_login_path . 'includes/libs/vk/api/VK.php');
		require( um_social_login_path . 'includes/libs/vk/api/VKException.php');
		
		$this->api_key = trim( UM()->options()->get('vk_api_key') );
		$this->api_secret = trim( UM()->options()->get('vk_api_secret') );
		$this->api_settings = 'offline';
		if( method_exists ( 'UM_Social_Login_API','get_redirect_url' ) ){
			$this->callback_url = UM()->Social_Login_API()->get_redirect_url();
		}
		$this->callback_url = add_query_arg( 'provider', 'vk', $this->callback_url );
		$this->callback_url = remove_query_arg( 'code',  $this->callback_url );
		
	}

	/***
	***	@Get auth
	***/
	function get_auth() {
		if ( isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'vk' && isset($_REQUEST['code']) ) {
	
			$vk = new VK\VK( $this->api_key, $this->api_secret );
			
			if ( isset( $_SESSION['vk_token'] ) ) {
				$access_token = $_SESSION['vk_token'];
			} else {
				$access_token = $vk->getAccessToken($_REQUEST['code'], $this->callback_url);
				$_SESSION['vk_token'] = $access_token;
			}

			$uid = $access_token['user_id'];
			$token = $access_token['access_token'];

			$profile = $vk->api('users.get', array(
					'user_ids'       	=> $uid,
					'fields'        	=> 'uid, first_name, last_name, nickname, screen_name, photo, photo_big, about, country, sex, contacts',
					'https'				=> ( is_ssl() ) ? 1 : 0,
					'v'					=> 5.73
			));
			
			$profile = $profile['response'][0];
			
			// prepare the array that will be sent
			$profile['username'] = $profile['screen_name'];
			$profile['user_login'] = $profile['screen_name'];
			$profile['first_name'] = $profile['first_name'];
			$profile['last_name'] = $profile['last_name'];
			$profile['description'] = $profile['about'];
			$profile['country'] = $profile['country']["title"];

			$arr_gender = array( 1 => "Female", 2 => "Male", 0 => "" );
			$profile['gender'] = $arr_gender[ $profile['sex'] ];

			if( isset( $profile['contacts']['mobile_phone'] ) && ! empty( $profile['contacts']['mobile_phone'] ) ){
				$profile['mobile_number'] = $profile['contacts']['mobile_phone'] ;
			}

			if( isset( $profile['contacts']['home_phone'] ) && ! empty( $profile['contacts']['home_phone'] ) ){
				$profile['phone_number'] = $profile['contacts']['home_phone'] ;
			}

			// username/email exists
			$profile['email_exists'] = '';
			$profile['username_exists'] = '';
				
			// provider identifier
			$profile['_uid_vk'] = $profile['id'];
				
			if ( isset( $profile['photo_big'] ) ) {
				$profile['_save_synced_profile_photo'] = $profile['photo_big'];
			}
				
			$profile['_save_vk_handle'] = $profile['first_name'] . ' ' . $profile['last_name'];
			$profile['_save_vk_link'] = 'https://vk.com/' . $profile['screen_name'];
			$profile['_save_vk_photo_url_dyn'] = $profile['photo'];

			// have everything we need?
            UM()->Social_Login_API()->resume_registration( $profile, 'vk' );
			
		}
		
	}
		
	/***
	***	@get login uri
	***/
	function login_url() {
		if( ! isset( $_REQUEST['um_social_login'] ) ){
			$this->login_url = um_get_core_page('login');
			$this->login_url = add_query_arg('um_social_login','vk', $this->login_url );
			$this->login_url = add_query_arg('um_social_login_ref', UM()->Social_Login_API()->shortcode_id, $this->login_url );
			if( isset( $_SESSION['um_social_login_redirect'] ) ){
				if ( ! isset( $_REQUEST['code'] ) && ! isset( $_REQUEST['state'] )  ) {
				$this->login_url = add_query_arg('redirect_to', $_SESSION['um_social_login_redirect'], $this->login_url );
					$_SESSION['um_social_login_redirect_after'] = $_SESSION['um_social_login_redirect'];
				}
			}
		}else{
			$vk = new VK\VK( $this->api_key, $this->api_secret );
			
			$this->login_url = $vk->getAuthorizeURL( $this->api_settings, $this->callback_url );
		}
		return $this->login_url;
		
	}
		
}