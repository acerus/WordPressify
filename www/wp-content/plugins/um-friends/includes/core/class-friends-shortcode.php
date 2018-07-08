<?php
namespace um_ext\um_friends\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


class Friends_Shortcode {

	function __construct() {
	
		add_shortcode( 'ultimatemember_friends', array(&$this, 'ultimatemember_friends') );
		add_shortcode( 'ultimatemember_friend_reqs', array(&$this, 'ultimatemember_friend_reqs') );
		add_shortcode( 'ultimatemember_friend_reqs_sent', array(&$this, 'ultimatemember_friend_reqs_sent') );
		
		add_shortcode( 'ultimatemember_friends_bar', array(&$this, 'ultimatemember_friends_bar') );
		//add_filter('um_user_profile_tabs', array($this,'profile_restriction'),10,1);
		
		

	}

	public function profile_restriction( $args ){
		$can_view = true;

		$user_id = um_get_requested_user();

		if ( !is_user_logged_in() || get_current_user_id() != $user_id ) { // Everyone 

			// friends only
			$is_private_friend_case = UM()->user()->is_private_case( $user_id, __( 'Friends only', 'um-friends' ) );
			if ( $is_private_friend_case ) { // only friends can view my profile
				$can_view = false;
			}

			$wall_privacy = um_user('wall_privacy');
			$has_friended = UM()->Friends_API()->api()->is_friend( get_current_user_id(), $user_id );
			if( $wall_privacy == 3 &&  $has_friended ){ 
				// can view by friends only
				foreach ($args as $key => $value) {
						if( $key == 'activity' ){
							unset( $args['activity'] );
						}
				}
			}
		}
			$profile_privacy = um_user("profile_privacy");
			if( $profile_privacy === "Friends only" ){
				// can view by friends only
				foreach ($args as $key => $value) {
						if( $key == 'activity' ){
							unset( $args['activity'] );
						}
				}
			}

			
		

		echo "<script>console.log(".json_encode( array( $args, $has_friended, $profile_privacy  ) ).");</script>";


		if( ! $can_view ){
			$args = array();
		}

		return $args;

	}
	
	/***
	***	@shortcode
	***/
	function ultimatemember_friends_bar( $args = array() ) {
		$defaults = array(
			'user_id' => get_current_user_id()
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );

		ob_start();
		$can_view = true;

		if ( !is_user_logged_in() || get_current_user_id() != $user_id ) {
			
			$is_private_case = UM()->user()->is_private_case( $user_id, __('Friends only','um-friends') );
			if ( $is_private_case ) { // only friends can view my profile
				$can_view = false;
			}

		}

	


		?>


		
		<div class="um-friends-bar">
			
			
				<div class="um-friends-rc">
					<?php if( $can_view ){ ?>
						<a href="<?php echo UM()->Friends_API()->api()->friends_link( $user_id ); ?>" class="<?php if ( isset( $_REQUEST['profiletab'] ) && $_REQUEST['profiletab'] == 'friends' ) { echo 'current'; } ?>"><?php _e('friends','um-friends'); ?><?php echo UM()->Friends_API()->api()->count_friends( $user_id ); ?></a>
					<?php } ?>
				</div>
			
			
			<?php if ( UM()->Friends_API()->api()->can_friend( $user_id, get_current_user_id() ) ) { ?>
			<div class="um-friends-btn">
				<?php echo UM()->Friends_API()->api()->friend_button( $user_id, get_current_user_id() ); ?>
				<?php do_action('um_after_friend_button_profile', $user_id ); ?>
			</div>
			<?php } ?>
			<div class="um-clear"></div>
		</div>
		
		<?php
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/***
	***	@shortcode
	***/
	function ultimatemember_friends( $args = array() ) {
		$defaults = array(
			'user_id' 		=> ( um_is_core_page('user') ) ? um_profile_id() : get_current_user_id(),
			'style' 		=> 'default',
			'max'			=> 11
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );

		ob_start();
		
		if ( $style == 'avatars' ) {
			$tpl = 'friends-mini';
		} else {
			$tpl = 'friends';
		}
		
		$file       = um_friends_path . 'templates/'.$tpl.'.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/'.$tpl.'.php';
		
		if( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if( file_exists( $file ) ) {
			$friends = UM()->Friends_API()->api()->friends( $user_id );
			include_once $file;
		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/***
	***	@shortcode
	***/
	function ultimatemember_friend_reqs( $args = array() ) {
		$defaults = array(
			'user_id' 		=> ( um_is_core_page('user') ) ? um_profile_id() : get_current_user_id(),
			'style' 		=> 'default',
			'max'			=> 999
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );

		ob_start();
		
		if ( $style == 'avatars' ) {
			$tpl = 'friends-mini';
		} else {
			$tpl = 'friends';
		}
		
		$file       = um_friends_path . 'templates/'.$tpl.'.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/'.$tpl.'.php';
		
		if( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if( file_exists( $file ) ) {
			$friends = UM()->Friends_API()->api()->friend_reqs( $user_id );
			$_is_reqs = true;
			include_once $file;
		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/***
	***	@shortcode
	***/
	function ultimatemember_friend_reqs_sent( $args = array() ) {
		$defaults = array(
			'user_id' 		=> ( um_is_core_page('user') ) ? um_profile_id() : get_current_user_id(),
			'style' 		=> 'default',
			'max'			=> 999
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );

		ob_start();
		
		if ( $style == 'avatars' ) {
			$tpl = 'friends-mini';
		} else {
			$tpl = 'friends';
		}
		
		$file       = um_friends_path . 'templates/'.$tpl.'.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/'.$tpl.'.php';
		
		if( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if( file_exists( $file ) ) {
			$friends = UM()->Friends_API()->api()->friend_reqs_sent( $user_id );
			$_sent = true;
			include_once $file;
		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

}