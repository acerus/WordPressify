<?php

class UM_Social_Login_LinkedIn {

	public $login_url_called = 0;

	function __construct() {
		
		add_action('init', array(&$this, 'load'));
		
		add_action('init', array(&$this, 'get_auth'));

		add_action('template_redirect', array( &$this,'redirect_authentication'), 1 );

	}

	function redirect_authentication(){
		
		if( isset( $_REQUEST['um_social_login'] ) &&  $_REQUEST['um_social_login'] == "linkedin" ){
			return wp_redirect( $this->login_url() );
		}

	}

	/***
	***	@load
	***/
	function load() {
		if( ! class_exists( 'LinkedIn' ) ){
			require_once um_social_login_path . 'includes/libs/linkedin/api/LinkedIn.php';
		}
		
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

		if ( isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'linkedin' && isset( $_REQUEST['code']  ) ) {

				try{
					$provider = new LinkedIn(
						  array(
						    'api_key' => $this->api_key, 
						    'api_secret' => $this->api_secret, 
						    'callback_url' => $this->oauth_callback
						  )
					);

				}catch( Exception $e ){
					exit( wp_redirect( $this->oauth_callback ) );
				}

			$i = 0;

			
			if( ! isset( $_POST ) && empty(  $_POST  ) ||  empty(  $_SESSION['um_social_login_linked_code'] )  ){
				$i = 1;
				$code = $_REQUEST['code'];
				$_SESSION['um_social_login_linked_code'] = $code;

			}

			if( ! isset( $code ) ){
				$i = 2;
				$code = $_SESSION['um_social_login_linked_code'];
			}

			// invalid token: abort
			if ( isset( $code ) ) {
				
				if( ! isset( $_SESSION['um_social_login_linked_info'] ) ){
					try{					
						
	            		$token = $provider->getAccessToken( $code  );
	            		$token = apply_filters('um_social_login_linked_token', $token );
						

					}catch( Exception $e ){
					 	wp_die(  "UM Social Login - LinkedIn SDK Error Message:"
					 		."<br/>".$e->getMessage()
					 		."<br/>Error Code: ".$i
					 		."<br/>Callback URL: ".$this->oauth_callback 
					 		."<br/>Session Code: <pre>".$code."</pre>"
					 		."<br/>Request Code: <pre>".(isset( $_REQUEST['code'] ) ? $_REQUEST['code']: '-')."</pre>" 
					 	);
					}

						$request_data = array(
							'id',
							'last-name',
							'first-name',
							'picture-url',
							'email-address', 
							'public-profile-url',
							'picture-urls::(original)', 
						);

						$request_data = apply_filters('um_social_login_linked_request_data', $request_data );

						$info = $provider->get('/people/~:('.implode( ",", $request_data ).')');
						$_SESSION['um_social_login_linked_info'] = serialize( $info );


				}else{
					$info = unserialize( $_SESSION['um_social_login_linked_info'] );
				}


				if ( isset ( $info['pictureUrls'] ) 
					&& isset( $info['pictureUrls']['values'] ) 
					&& isset( $info['pictureUrls']['values'][0] ) ) {
						$profile['picture-original'] = $info['pictureUrls']['values'][0];
				} else if ( isset( $info['pictureUrl'] ) ) {
						$profile['picture-original'] = $info['pictureUrl'];
				}else{
					$profile['picture-url'] = um_get_default_avatar_uri();
					$profile['picture-original'] = um_get_default_avatar_uri();
				}

				if ( isset( $profile['picture-original'] ) ) {
						$profile['_save_synced_profile_photo'] = $profile['picture-original'];
				}

				if ( isset( $profile['picture-url'] ) ) {
						$profile['_save_linkedin_photo_url_dyn'] = $profile['picture-url'];
				}
				
				
				// prepare the array that will be sent
				$profile['user_email'] = $info['emailAddress'];
				$profile['first_name'] = $info['firstName'];
				$profile['last_name']  = $info['lastName'];
				
				// username/email exists
				$profile['email_exists'] = $info['emailAddress'];
				$profile['username_exists'] = $info['emailAddress'];
				
				// provider identifier
				$profile['_uid_linkedin'] = $info['id'];
				
				$profile['_save_linkedin_handle'] = $info['firstName'] . ' ' . $info['lastName'];
				$profile['_save_linkedin_link'] = $info['publicProfileUrl'];

				if ( isset( $profile['picture-original'] ) ) {
					$profile['_save_synced_profile_photo'] = $profile['picture-original'];
				}
			
				if ( isset( $profile['picture-url'] ) ) {
					$profile['_save_linkedin_photo_url_dyn'] = $profile['picture-url'];
				}
				
				$profile = apply_filters('um_social_login_linked_profile', $profile, $info );
				// have everything we need?
                UM()->Social_Login_API()->resume_registration( $profile, 'linkedin' );
				
			}
			
		}

	}
		
	/***
	***	@get login uri
	***/
	function login_url() {

		if( ! isset( $_REQUEST['um_social_login'] ) ){
			$this->login_url = um_get_core_page('login');
			$this->login_url = add_query_arg('um_social_login','linkedin', $this->login_url );
			$this->login_url = add_query_arg('um_social_login_ref', UM()->Social_Login_API()->shortcode_id, $this->login_url );
			if( isset( $_SESSION['um_social_login_redirect'] ) ){
				if ( ! isset( $_REQUEST['code'] ) && ! isset( $_REQUEST['state'] )  ) {
				$this->login_url = add_query_arg('redirect_to', $_SESSION['um_social_login_redirect'], $this->login_url );
					$_SESSION['um_social_login_redirect_after'] = $_SESSION['um_social_login_redirect'];
				}
			}
		}else{

			if ( ! isset( $_REQUEST['code'] ) && ! isset( $_REQUEST['state'] )  ) {
					
					if(  $this->login_url_called == 0  && (! isset(  $_SESSION['um_social_login_linked_code']  ) || ! isset( $_POST ) ) ){
							
							$provider = new LinkedIn(
									  array(
									    'api_key' => $this->api_key, 
									    'api_secret' => $this->api_secret, 
									    'callback_url' => $this->oauth_callback
									  )
							);
							$scope = array(
								    LinkedIn::SCOPE_BASIC_PROFILE, 
								    LinkedIn::SCOPE_EMAIL_ADDRESS, 
							);

							$scope = apply_filters('um_social_login_linked_scope', $scope );
							$url = $provider->getLoginUrl( $scope );

							$this->login_url = $url;

					}

					$this->login_url_called++;
					unset( $_SESSION['um_social_login_linked_info'] );


					
			}
		}

		unset( $_SESSION['um_social_login_linked_code'] );
	
		
		
		return $this->login_url;
		
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