<?php

class UM_Social_Login_LinkedIn {

	function __construct() {
		
		add_action('init', array(&$this, 'load'));
		
		add_action('init', array(&$this, 'get_auth'));

	}

	/***
	***	@load
	***/
	function load() {
		require_once um_social_login_path . 'includes/libs/linkedin/api/linkedinoauth.php';

		$this->api_key = trim( UM()->options()->get('linkedin_api_key') );
		$this->api_secret = trim( UM()->options()->get('linkedin_api_secret') );
		if( method_exists ( 'UM_Social_Login_API','get_redirect_url' ) ){
			$this->oauth_callback = UM()->Social_Login_API()->get_redirect_url();
		}
		$this->oauth_callback = add_query_arg( 'provider', 'linkedin', $this->oauth_callback );

		$this->login_url = '';

	}

	/***
	***	@Get auth
	***/
	function get_auth() {
		if ( isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'linkedin' && isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']) ) {

			$request_token['oauth_token'] = $_SESSION['li_oauth_token'];
			$request_token['oauth_token_secret'] = $_SESSION['li_oauth_token_secret'];

			// invalid token: abort
			if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
				
			} else {
				
				// if session already stored
				if ( isset($_SESSION['li_access_token']) ) {
					
					// get access token
					$access_token = $_SESSION['li_access_token'];
					$connection = new linkedino( $this->api_key, $this->api_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

				} else {
					
					$connection = new linkedino( $this->api_key, $this->api_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
					$access_token = $connection->getAccessToken( $_REQUEST['oauth_verifier'] );
					$_SESSION['li_access_token'] = $access_token;
					
					// get access token
					$access_token = $_SESSION['li_access_token'];
					$connection = new linkedino( $this->api_key, $this->api_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

				}
				
				$profile = $connection->get('people/~', array('id','first-name','last-name','email-address', 'public-profile-url', 'picture-url' ) );
				$original_picture = $connection->get('people/~', array('picture-urls::(original)' ) );

				$profile = (array)simplexml_load_string($profile);

				if ( isset( $original_picture ) && $original_picture ) {
					$desired_array = json_decode(json_encode((array) simplexml_load_string($original_picture)), 1);
					if ( isset ( $desired_array['picture-urls']['picture-url'] ) ) {
						$profile['picture-original'] = $desired_array['picture-urls']['picture-url'];
					}
				}
				
				// prepare the array that will be sent
				$profile['user_email'] = $profile['email-address'];
				$profile['first_name'] = $profile['first-name'];
				$profile['last_name'] = $profile['last-name'];
				
				// username/email exists
				$profile['email_exists'] = $profile['email-address'];
				$profile['username_exists'] = $profile['email-address'];
				
				// provider identifier
				$profile['_uid_linkedin'] = $profile['id'];
				
				$profile['_save_linkedin_handle'] = $profile['first-name'] . ' ' . $profile['last-name'];
				$profile['_save_linkedin_link'] = $profile['public-profile-url'];

				if ( isset( $profile['picture-original'] ) ) {
					$profile['_save_synced_profile_photo'] = $profile['picture-original'];
				}
			
				if ( isset( $profile['picture-url'] ) ) {
					$profile['_save_linkedin_photo_url_dyn'] = $profile['picture-url'];
				}
				
				// have everything we need?
                UM()->Social_Login_API()->resume_registration( $profile, 'linkedin' );
				
			}
			
		}

	}
		
	/***
	***	@get login uri
	***/
	function login_url() {
		$connection = new linkedino( $this->api_key, $this->api_secret );
		$request_token = $connection->getRequestToken( $this->oauth_callback );
		
		if ( isset( $request_token['oauth_token'] ) ) {

		$_SESSION['li_oauth_token'] = $request_token['oauth_token'];
		$_SESSION['li_oauth_token_secret'] = $request_token['oauth_token_secret'];

		$this->login_url = $connection->getAuthorizeURL( $request_token['oauth_token'] );
		
		}
		
		return $this->login_url;
		
	}
		
}