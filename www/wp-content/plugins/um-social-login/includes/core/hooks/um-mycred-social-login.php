<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Social Login Connect Hooks
 */
class UM_myCRED_Social_Login_Connect extends myCRED_Hook {

	/**
	 * Construct
	 */
	function __construct( $hook_prefs, $type ) {

		$networks = UM()->Social_Login_API()->available_networks();
		$arr_defaults = array();

		foreach( $networks as $provider => $network ){

			$arr_defaults[ $provider ] = array(
				'creds'   => 1,
				'log'     => "%plural% for connecting {$network['name']} account.",
				'limit'  => '0/x',
				'provider' => $provider,
				'notification_tpl' => '',
			);

		}

		parent::__construct( array(
			'id'       => 'um-mycred-social-login-connect',
			'defaults' => $arr_defaults
		), $hook_prefs, $type );
	}

	/**
	 * Hook into WordPress
	 */
	public function run() {
	
		add_action('um_social_login_after_connect', array( $this,'user_connects_social_network' ), 10, 2);
	
	}

	/**
	 * Check if the user qualifies for points
	 */
	public function user_connects_social_network( $provider, $user_id  ) {
		// Check for exclusion
		if ( $this->core->exclude_user( $user_id ) ) return;

		// Limit
		if ( $this->over_hook_limit( $provider, 'um-mycred-social-login-connect', $user_id ) ) return;

		// Execute
		$this->core->add_creds(
			'um-mycred-social-login-connect',
			$user_id,
			$this->prefs[ $provider ]['creds'],
			$this->prefs[ $provider ]['log'],
			0,
			'',
			$this->mycred_type
		);
	}

	/**
	 * Add Settings
	 */
	public function preferences() {
		// Our settings are available under $this->prefs
		$prefs = $this->prefs;

		$networks = UM()->Social_Login_API()->available_networks();

		if( empty( $networks ) ) {
			echo __('No networks available.','um-mycred');
		}
		 ?>

			<?php foreach( $networks as $provider => $network ):?> 
				<hr/>
				<h2><i class='<?php echo $network['icon'];?>'></i><?php echo $network['name']; ?></h2>
				<!-- First we set the amount -->
				<label class="subheader">Award <?php echo $this->core->plural(); ?></label>
				<ol>
					<li>
						<div class="h2"><input type="text" name="<?php echo $this->field_name( array( $provider, 'creds' ) ); ?>" id="<?php echo $this->field_id( array( $provider, 'creds' ) ); ?>" value="<?php echo $this->core->format_number( $prefs[ $provider ]['creds'] ); ?>" size="8" /></div>
					</li>
				</ol>
				<!-- Then the log template -->
				<label class="subheader"><?php _e( 'Log template', 'mycred' ); ?></label>
				<ol>

					<li>
						<div class="h2"><input type="text" name="<?php echo $this->field_name(  array( $provider, 'log' )  ); ?>" id="<?php echo $this->field_id(  array( $provider, 'log' ) ); ?>" value="<?php echo $prefs[ $provider ]['log']; ?>" class="long" /></div>
					</li>
					<li>
						<label for="<?php echo $this->field_id(  array( $provider, 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
						<?php echo $this->hook_limit_setting( $this->field_name(  array( $provider, 'limit' ) ), $this->field_id(   array( $provider, 'limit' )  ), $prefs[ $provider ]['limit'] ); ?>
					</li>
					<input type="hidden" name="<?php echo $this->field_name( array( $provider, 'provider' ) ); ?>" value="<?php echo $provider;?>"/>
					<li class="empty">&nbsp;</li>
				</ol>

				<?php do_action( 'um_mycred_hooks_option_extended', $provider, $network, $prefs, $this ); ?>
			<?php endforeach; ?>
			<?php
			
	}

	/**
	 * Sanitize Preferences
	 */
	public function sanitise_preferences( $data ) {
		$new_data = $data;
   
		$networks = UM()->Social_Login_API()->available_networks();
		
		foreach( $networks as $provider => $d ){
			// Apply defaults if any field is left empty
			$new_data[ $provider ]['creds'] = ( !empty( $data[ $provider ]['creds'] ) ) ? $data[ $provider ]['creds'] : $this->defaults[ $provider ]['creds'];
			$new_data[ $provider ]['log'] 	= ( !empty( $data[ $provider ]['log'] ) ) 	? sanitize_text_field( $data[ $provider ]['log'] ) : $this->defaults[ $provider ]['log'];
			$limit = ( !empty( $data[ $provider ]['limit'] ) ) ? sanitize_text_field( $data[ $provider ]['limit'] ) : $this->defaults[ $provider ]['limit'];
			$new_data[ $provider ]['limit_by'] = ( !empty( $data[ $provider ]['limit_by'] ) ) ? sanitize_text_field( $data[ $provider ]['limit_by'] ) : $this->defaults[ $provider ]['limit_by'];
			
			$new_data[ $provider ]['notification_tpl'] 	= ( !empty( $data[ $provider ]['notification_tpl'] ) ) 	? sanitize_text_field( $data[ $provider ]['notification_tpl'] ) : $this->defaults[ $provider ]['notification_tpl'];
			
			if ( $limit != '' ){
				$new_data[ $provider ]['limit'] = $limit . '/' . $new_data[ $provider ]['limit_by'];
				unset( $new_data[ $provider ]['limit_by'] );
			}

		}

		return $new_data;
	}
}


/**
 * Social Login Disconnect Hooks
 */
class UM_myCRED_Social_Login_Disconnect extends myCRED_Hook {

	/**
	 * Construct
	 */
	function __construct( $hook_prefs, $type ) {

		$networks = UM()->Social_Login_API()->available_networks();
		$arr_defaults = array();

		foreach( $networks as $provider => $network ){

			$arr_defaults[ $provider ] = array(
				'creds'   => 1,
				'log'     => "%plural% for disconnecting {$network['name']} account.",
				'limit'  => '0/x',
				'provider' => $provider,
				'notification_tpl' => '',
			);

		}

		parent::__construct( array(
			'id'       => 'um-mycred-social-login-disconnect',
			'defaults' => $arr_defaults
		), $hook_prefs, $type );
	}

	/**
	 * Hook into WordPress
	 */
	public function run() {
	
		add_action('um_social_login_after_disconnect', array( $this,'user_disconnects_social_network' ), 10, 2);
	
	}

	/**
	 * Check if the user qualifies for points
	 */
	public function user_disconnects_social_network( $provider, $user_id  ) {
		
		// Check for exclusion
		if ( $this->core->exclude_user( $user_id ) ) return;

		// Limit
		if ( $this->over_hook_limit( $provider, 'um-mycred-social-login-disconnect', $user_id ) ) return;

		// Execute
		mycred_subtract( 
			'um-mycred-social-login-disconnect', 
			$user_id, 
			$this->prefs[ $provider ]['creds'], 
			$this->prefs[ $provider ]['log'],
			0,
			'',
			$this->mycred_type
		);

	}

	/**
	 * Add Settings
	 */
	public function preferences() {
		// Our settings are available under $this->prefs
		$prefs = $this->prefs;

		$networks = UM()->Social_Login_API()->available_networks();

		if( empty( $networks ) ) {
			echo __('No networks available.','um-mycred');
		}
		 ?>

		<?php foreach( $networks as $provider => $network ):?> 
			<hr/>
			<h2><i class='<?php echo $network['icon'];?>'></i><?php echo $network['name']; ?></h2>
			<!-- First we set the amount -->
			<label class="subheader">Deduct <?php echo $this->core->plural(); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo $this->field_name( array( $provider, 'creds' ) ); ?>" id="<?php echo $this->field_id( array( $provider, 'creds' ) ); ?>" value="<?php echo $this->core->format_number( $prefs[ $provider ]['creds'] ); ?>" size="8" /></div>
				</li>
			</ol>
			<!-- Then the log template -->
			<label class="subheader"><?php _e( 'Log template', 'mycred' ); ?></label>
			<ol>

				<li>
					<div class="h2"><input type="text" name="<?php echo $this->field_name(  array( $provider, 'log' )  ); ?>" id="<?php echo $this->field_id(  array( $provider, 'log' ) ); ?>" value="<?php echo $prefs[ $provider ]['log']; ?>" class="long" /></div>
				</li>
				<li>
					<label for="<?php echo $this->field_id(  array( $provider, 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
					<?php echo $this->hook_limit_setting( $this->field_name(  array( $provider, 'limit' ) ), $this->field_id(   array( $provider, 'limit' )  ), $prefs[ $provider ]['limit'] ); ?>
				</li>
				<input type="hidden" name="<?php echo $this->field_name( array( $provider, 'provider' ) ); ?>" value="<?php echo $provider;?>"/>
			</ol>

			<?php do_action( 'um_mycred_hooks_option_extended', $provider, $network, $prefs, $this ); ?>
			<ol>
				<li class="empty">&nbsp;</li>
			</ol>

		<?php endforeach; ?>
		<?php
	
	}

	/**
	 * Sanitize Preferences
	 */
	public function sanitise_preferences( $data ) {
		$new_data = $data;
   
		$networks = UM()->Social_Login_API()->available_networks();
		
		foreach( $networks  as $provider => $d ){
			// Apply defaults if any field is left empty
			$new_data[ $provider ]['creds'] = ( !empty( $data[ $provider ]['creds'] ) ) ? $data[ $provider ]['creds'] : $this->defaults[ $provider ]['creds'];
			$new_data[ $provider ]['log'] 	= ( !empty( $data[ $provider ]['log'] ) ) 	? sanitize_text_field( $data[ $provider ]['log'] ) : $this->defaults[ $provider ]['log'];
			$limit = ( !empty( $data[ $provider ]['limit'] ) ) ? sanitize_text_field( $data[ $provider ]['limit'] ) : $this->defaults[ $provider ]['limit'];
			$new_data[ $provider ]['limit_by'] = ( !empty( $data[ $provider ]['limit_by'] ) ) ? sanitize_text_field( $data[ $provider ]['limit_by'] ) : $this->defaults[ $provider ]['limit_by'];
			
			$new_data[ $provider ]['notification_tpl'] 	= ( !empty( $data[ $provider ]['notification_tpl'] ) ) 	? sanitize_text_field( $data[ $provider ]['notification_tpl'] ) : $this->defaults[ $provider ]['notification_tpl'];
			
			

			if ( $limit != '' ){
				$new_data[ $provider ]['limit'] = $limit . '/' . $new_data[ $provider ]['limit_by'];
				unset( $new_data[ $provider ]['limit_by'] );
			}

		}

		$new_data = apply_filters("um_mycred_sanitise_pref", $new_data );

		return $new_data;
	}
}