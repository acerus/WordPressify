<?php

class UM_Social_Login_Twitter {

	public $login_url_called = 0;

	function __construct() {
		
		add_action('init', array(&$this, 'load'));
		
		add_action('init', array(&$this, 'get_auth'));
		
		add_action('template_redirect', array( &$this,'redirect_authentication'), 1 );

	}

	function redirect_authentication(){
		
		if( isset( $_REQUEST['um_social_login'] ) &&  $_REQUEST['um_social_login'] == "twitter" ){

			$_SESSION['tw_oath_url'] =  $this->login_url();
			
			return wp_redirect( $this->login_url() );
		}

	}

	/***
	***	@load
	***/
	function load() {
		$this->consumer_key = trim( UM()->options()->get('twitter_consumer_key') );
		$this->consumer_secret = trim( UM()->options()->get('twitter_consumer_secret') );
		if( method_exists ( 'UM_Social_Login_API','get_redirect_url' ) ){
			$this->oauth_callback =  UM()->Social_Login_API()->get_redirect_url() ;
		}
		$this->oauth_callback = add_query_arg( 'provider', 'twitter', $this->oauth_callback );
		$this->oauth_callback = remove_query_arg( 'oauth_token', $this->oauth_callback );
		$this->oauth_callback = remove_query_arg( 'error_message', $this->oauth_callback );
		$this->oauth_callback = remove_query_arg( 'oauth_verifier', $this->oauth_callback );
	}

	/***
	***	@Get auth
	***/
	function get_auth() {
		if ( isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'twitter' && isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']) ) {
				
				if( ! $this->is_session_started() ){
					session_start();
				}

				$access_token =  null;
				if( ! isset( $_SESSION['tw_access_token'] ) ){
					$_SESSION['tw_landed_callback_url'] = $this->oauth_callback;
					try{ 
						$request_token['oauth_token'] = $_SESSION['tw_oauth_token'];
						$request_token['oauth_token_secret'] = $_SESSION['tw_oauth_token_secret'];
						
						$connection = new Abraham\TwitterOAuth\TwitterOAuth( $this->consumer_key, $this->consumer_secret,$request_token['oauth_token'] , $request_token['oauth_token_secret']);
						$access_token = $connection->oauth("oauth/access_token", 
							array(
								"oauth_verifier" => $_GET['oauth_verifier'], 
								"oauth_callback" => $this->oauth_callback
							)
						);
						$_SESSION['tw_access_token'] = $access_token;

					}catch(Exception $e ){
						wp_die( 'UM Social Login - Twitter Access Token: <br/><strong>'.$e->getMessage().'</strong>'
							.'<br/>Callback URL: '.$this->oauth_callback
							.'<br/>oAuth Verifier: '.esc_html($_GET['oauth_verifier'])
							.'<br/>Access Token: '.esc_html( empty( $access_token ) ? $access_token : 'None'  ) 
							.'<br/>Has oAuth Token: '.esc_html( empty($_SESSION['tw_oauth_token']) ?'No':'Yes' )
							.'<br/>Has oAuth Secret: '.esc_html( empty($_SESSION['tw_oauth_token_secret']) ?'No':'Yes' )
							.'<br/>Referrer URL: '.esc_html( $_SESSION['tw_referral_callback_url'] )
							.'<br/>Landed URL: '.esc_html( $_SESSION['tw_landed_callback_url'] )
							.'<br/>oAuth URL: '.esc_html( $_SESSION['tw_oath_url'] )
							,'UM Social Login - Twitter Error', array('back_link' => true ) );
					}
				}

				if( isset( $_SESSION['tw_access_token'] ) ){
					try{ 
						
						$access_token = $_SESSION['tw_access_token'];
						$connection = new Abraham\TwitterOAuth\TwitterOAuth( $this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
						$arr_cred_param = [ 'include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true' ];
						$profile = $connection->get("account/verify_credentials", $arr_cred_param );
				
						$profile = json_decode(json_encode($profile), true);

					}catch(Exception $e ){
						wp_die( 'UM Social Login - Twitter Verify Credentials: '.$e->getMessage().' - '.$this->oauth_callback,'UM Social Login - Twitter Error', array('back_link' => true ) );
					}
				}

				
				if( isset($profile['errors']) && count($profile['errors']) > 0 ){
					unset( $_SESSION['tw_access_token'] );
					wp_die( 'UM Social Login - Twitter SDK Error: '.$profile['errors'][0]['message'].' - '.$this->oauth_callback,'UM Social Login - Twitter Error', array('back_link' => true ) );
				}

				$name = $profile['name'];
				$name = explode(' ', $name);
				
				// prepare the array that will be sent
				$profile['username'] = $profile['screen_name'];
				$profile['user_login'] = $profile['screen_name'];
				$profile['first_name'] = isset( $name[0] ) ? $name[0]: '';
				$profile['last_name'] =  isset( $name[1] ) ? $name[1]: '';

				// username/email exists
				$profile['email_exists'] = isset( $profile['email'] ) ? $profile['email']: '';
				$profile['user_email'] = isset( $profile['email'] ) ? $profile['email']: '';
				$profile['username_exists'] = '';
				
				// provider identifier
				$profile['_uid_twitter'] = $profile['id'];
				
				if ( isset( $profile['profile_image_url'] ) && strstr( $profile['profile_image_url'], '_normal' ) ) {
					$profile['_save_synced_profile_photo'] = str_replace('_normal','',$profile['profile_image_url']);
				}
				
				$profile['_save_twitter_handle'] = '@' . $profile['screen_name'];
				$profile['_save_twitter_link'] = 'https://twitter.com/' . $profile['screen_name'];
				$profile['_save_twitter_photo_url_dyn'] = $profile['profile_image_url'];

				// have everything we need?
                UM()->Social_Login_API()->resume_registration( $profile, 'twitter' );
			
			
		}
		
	}
		
	/***
	***	@get login uri
	***/
	function login_url() {
		if( ! isset( $_REQUEST['um_social_login'] ) ){
			if( um_is_core_page('login') ){
				$this->current_auth_url = um_get_core_page('login');
			}elseif( um_is_core_page('register') ){
				$this->current_auth_url = um_get_core_page('register');
			}elseif( um_is_core_page('account') ){
				$this->current_auth_url = um_get_core_page('account').'social/';
			}else{
				$this->current_auth_url = get_the_permalink();
			}

			$this->current_auth_url = add_query_arg('um_social_login','twitter', $this->current_auth_url );
			$this->current_auth_url = add_query_arg('um_social_login_ref', UM()->Social_Login_API()->shortcode_id, $this->current_auth_url );
			if( isset( $_SESSION['um_social_login_redirect'] ) ){
				if ( ! isset( $_REQUEST['code'] ) && ! isset( $_REQUEST['state'] )  ) {
				$this->current_auth_url = add_query_arg('redirect_to', $_SESSION['um_social_login_redirect'], $this->current_auth_url );
					$_SESSION['um_social_login_redirect_after'] = $_SESSION['um_social_login_redirect'];
				}
			}
		}else{
			if( ! isset($_REQUEST['oauth_token']) && ! isset($_REQUEST['oauth_verifier']) && $this->login_url_called == 0 ){
				if( ! $this->is_session_started() ){
						session_start();
				}
				
				if( ! is_user_logged_in() ){
					unset( $_SESSION['tw_access_token'] );
				}

				try{
					$connection = new Abraham\TwitterOAuth\TwitterOAuth( $this->consumer_key, $this->consumer_secret );
					$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $this->oauth_callback ));
					
					
						$_SESSION['tw_oauth_token'] = $request_token['oauth_token'];
						$_SESSION['tw_oauth_token_secret'] = $request_token['oauth_token_secret'];
						$_SESSION['tw_referral_callback_url'] = $this->oauth_callback;
					if( $connection->getLastHttpCode() ==200 ){
						$this->current_auth_url = $connection->url('oauth/authenticate', array('oauth_token' => $request_token['oauth_token']));
					}else{
						$this->current_auth_url = '?error=400';
					}

				} catch (Exception $e) {
						$this->current_auth_url = '?error_message='.$e->getMessage();

				}
			}
			$this->login_url_called++;
		}
		return $this->current_auth_url;
		
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