<?php

class UM_Social_Login_Facebook {

	public $login_url = '';

	public $login_url_called = 0;


	function __construct() {
		
		/*
		 * maybe uncomment later
		 * if( is_feed() ){
			return;
		}*/
		
		if( method_exists ( UM()->Social_Login_API() ,'set_provider_session' ) ){
            UM()->Social_Login_API()->set_provider_session();
		}

		add_action('init', array( &$this, 'load') );

		add_action('init', array( &$this, 'get_auth') );

		add_action('template_redirect', array( &$this,'redirect_authentication'), 1 );

	}

	function redirect_authentication(){
		
		if( isset( $_REQUEST['um_social_login'] ) &&  $_REQUEST['um_social_login'] == "facebook" ){
			return wp_redirect( $this->login_url() );
		}

	}

	/***
	***	@load
	***/
	function load() {

		$app_id = ( UM()->options()->get('facebook_app_id') ) ? UM()->options()->get('facebook_app_id') : 'APP_ID';
		$app_secret = ( UM()->options()->get('facebook_app_secret') ) ? UM()->options()->get('facebook_app_secret') : 'APP_SECRET';

		$this->app_id             	= trim( $app_id );
		$this->app_secret         	= trim( $app_secret );
		$this->required_scope     	= 'public_profile,email';
		
		if( method_exists ( UM()->Social_Login_API() ,'get_redirect_url' ) ){
			$this->redirect_url 		= UM()->Social_Login_API()->get_redirect_url();
		}

		$this->redirect_url 		= add_query_arg('provider', 'facebook', $this->redirect_url);
		
		

	}

	/***
	***	@Get auth
	***/
	function get_auth() {
		if (  isset( $_REQUEST['provider'] ) && $_REQUEST['provider'] == 'facebook' ) {
			
		    // Initialize the Facebook PHP SDK v5.
			$fb = new Facebook\Facebook([
			  'app_id'                => $this->app_id,
			  'app_secret'            => $this->app_secret,
			  'default_graph_version' => 'v2.8',
			  'persistent_data_handler'=>'session',
			]);


			if( ! isset( $_POST ) && empty(  $_POST  ) ||  empty(  $_SESSION['facebook_access_token'] ) ){
				

				try {
					$helper = $fb->getRedirectLoginHelper();
					$accessToken = $helper->getAccessToken( $this->redirect_url );
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
					wp_die( '<strong>UM Social Login - Facebook: Graph returned an error:</strong> '.$e->getMessage(),'UM Social Login - Facebook Error'.'<br/>Redirect URL:'.$this->redirect_url, array('back_link' => true ) );
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					wp_die( '<strong>UM Social Login - Facebook: SDK returned an error:</strong><Br/> '.$e->getMessage().'<br/>Redirect URL:'.$this->redirect_url.'<br/>Login URL:'.$this->login_url.'<br/>Hostname: '.$_SERVER['SERVER_NAME'],'UM Social Login - Facebook Error', array('back_link' => true ) );
				}

			}

			if ( empty( $accessToken ) ) {
				$accessToken = ! empty( $_SESSION['facebook_access_token'] ) ? $_SESSION['facebook_access_token'] : '';
			}

			if ( ! empty( $accessToken ) ) {
				$_SESSION['facebook_access_token'] = (string) $accessToken;
				
				// Set default access token
				$fb->setDefaultAccessToken( $accessToken );
				// Set fields for a permission
				$fields = array('id','name','email','link');
				// filter
				$fields = apply_filters('um_social_login_filter_facebook_fields', $fields );
				// Get all fields
				$res = $fb->get('/me?fields='.implode(",",$fields ) );
				
				$profile = $res->getGraphObject()->asArray();

				$profile = apply_filters('um_social_login_filter_facebook_response', $profile );
				
				if ( isset( $profile['name'] ) && $profile['name'] ) {
					$name = $profile['name'];
					$name = explode(' ', $name);
					$profile['first_name'] = $name[0];
					$profile['last_name'] = $name[1];
				}

				$profile_email = isset( $profile['email'] )? $profile['email'] : '';

				// prepare the array that will be sent
				$profile['user_email'] = $profile_email;

				// username/email exists
				$profile['email_exists'] = $profile_email;
				$profile['username_exists'] = $profile['id'];
				
				if( empty( $profile_email ) &&  empty( $_POST ) ){
					UM()->form()->add_error( 'user_email' , __('The email field was not returned. This may be because the email was missing, invalid or hasn\'t been confirmed.') );
						
				}
				
				// provider identifier
				$profile['_uid_facebook'] = $profile['id'];

				$profile['_save_synced_profile_photo'] = 'http://graph.facebook.com/'.$profile['id'].'/picture?width=200&height=200';
				$profile['_save_facebook_handle'] = $profile['name'];
				$profile['_save_facebook_link'] = $profile['link'];

				// have everything we need?
                UM()->Social_Login_API()->resume_registration( $profile, 'facebook' );

				unset( $_SESSION['um_is_login_url_set'] );


			}

		}
		

	}

	/***
	***	@get login uri
	***/
	function login_url() {
		if( is_feed() ){
			return;
		}

		if( ! isset( $_REQUEST['um_social_login'] ) ){
			$this->login_url = um_get_core_page('login');
			$this->login_url = add_query_arg('um_social_login','facebook', $this->login_url );
			$this->login_url = add_query_arg('um_social_login_ref', UM()->Social_Login_API()->shortcode_id, $this->login_url );
			if( isset( $_SESSION['um_social_login_redirect'] ) ){
				if ( ! isset( $_REQUEST['code'] ) && ! isset( $_REQUEST['state'] )  ) {
				$this->login_url = add_query_arg('redirect_to', $_SESSION['um_social_login_redirect'], $this->login_url );
					$_SESSION['um_social_login_redirect_after'] = $_SESSION['um_social_login_redirect'];
				}
			}
		}else{

			if( ! isset( $_REQUEST['provider'] ) &&  empty( $this->login_url ) && $this->login_url_called == 0 ){
							

				$fb = new Facebook\Facebook([
					  'app_id'                => $this->app_id,
					  'app_secret'            => $this->app_secret,
					  'default_graph_version' => 'v2.8',
					  'persistent_data_handler'=>'session',
				]);

				$helper = $fb->getRedirectLoginHelper();
				$permissions = array( 'public_profile','email' ); // optional
				$permissions = apply_filters('um_social_login_filter_facebook_permissions', $permissions );
				$callback = $this->redirect_url;

				$this->login_url = $helper->getLoginUrl($callback, $permissions);
				//error_log("Social Login Error: ".$this->login_url_called."# - ".$this->login_url );
				if( method_exists ( UM()->Social_Login_API() ,'set_provider_session' ) ){
                    UM()->Social_Login_API()->set_provider_session();
				}
			}	
			
			$this->login_url_called++;
		}

		return $this->login_url;
	}



}
