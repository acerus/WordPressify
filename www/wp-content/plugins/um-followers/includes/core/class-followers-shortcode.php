<?php
namespace um_ext\um_followers\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Followers_Shortcode {

	function __construct() {
	
		add_shortcode( 'ultimatemember_followers', array( &$this, 'ultimatemember_followers' ) );
		add_shortcode( 'ultimatemember_following', array( &$this, 'ultimatemember_following' ) );
		
		add_shortcode( 'ultimatemember_followers_bar', array( &$this, 'ultimatemember_followers_bar' ) );
		//add_filter('um_user_profile_tabs', array($this,'profile_restriction'),10,1);
		
		

	}

	public function profile_restriction( $args ){
		$can_view = true;

		$user_id = um_get_requested_user();

		if ( !is_user_logged_in() || get_current_user_id() != $user_id ) { // Everyone 

			// Followers only
			$is_private_follower_case = UM()->user()->is_private_case( $user_id, __( 'Followers', 'um-followers' ) );
			if ( $is_private_follower_case ) { // only followers can view my profile
				$can_view = false;
			}
			
			$is_private_case = UM()->user()->is_private_case( $user_id, __( 'Only people I follow can view my profile', 'um-followers' ) );
			if ( $is_private_case ) { // only people i follow can view my profile
				$can_view = false;
			}

			$wall_privacy = um_user( 'wall_privacy' );
			$has_followed = UM()->Followers_API()->api()->followed( get_current_user_id(), $user_id );
			if ( $wall_privacy == 3 &&  $has_followed ) {
				// can view by followers only
				foreach ( $args as $key => $value ) {
					if ( $key == 'activity' ) {
						unset( $args['activity'] );
					}
				}
			}
		}

		$profile_privacy = um_user("profile_privacy");
		if ( $profile_privacy === "Followers" ) {
			// can view by followers only
			foreach ( $args as $key => $value ) {
				if ( $key == 'activity' ) {
					unset( $args['activity'] );
				}
			}
		}

			
		
		echo "<script>console.log(".json_encode( array( $args, $has_followed, $profile_privacy  ) ).");</script>";

		if( ! $can_view ) {
			$args = array();
		}

		return $args;

	}
	
	/**
	 * Follow bar Shortcode
	 * @var $user_id
	 */
	function ultimatemember_followers_bar( $args = array() ) {
		$defaults = array(
			'user_id' => get_current_user_id()
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );

		ob_start();
		$can_view = true;

		if ( ! is_user_logged_in() || get_current_user_id() != $user_id ) {
			$is_private_case = UM()->user()->is_private_case( $user_id, __( 'Followers', 'um-followers' ) );
			if ( $is_private_case ) { // only followers can view my profile
				$can_view = false;
			}
			
			$is_private_case = UM()->user()->is_private_case( $user_id, __('Only people I follow can view my profile','um-followers') );
			if ( $is_private_case ) { // only people i follow can view my profile
				$can_view = false;
			}

		} ?>

		<div class="um-followers-rc">
			<?php if ( $can_view ) { ?>
				<a href="<?php echo UM()->Followers_API()->api()->followers_link( $user_id ); ?>" class="<?php if ( isset( $_REQUEST['profiletab'] ) && $_REQUEST['profiletab'] == 'followers' ) { echo 'current'; } ?>"><?php _e('followers','um-followers'); ?><?php echo UM()->Followers_API()->api()->count_followers( $user_id ); ?></a>
			<?php } ?>
		</div>

		<div class="um-followers-rc">
			<?php if ( $can_view ) { ?>
				<a href="<?php echo UM()->Followers_API()->api()->following_link( $user_id ); ?>" class="<?php if ( isset( $_REQUEST['profiletab'] ) && $_REQUEST['profiletab'] == 'following' ) { echo 'current'; } ?>"><?php _e('following','um-followers'); ?><?php echo UM()->Followers_API()->api()->count_following( $user_id ); ?></a>
			<?php } ?>
		</div>

		<?php if ( UM()->Followers_API()->api()->can_follow( $user_id, get_current_user_id() ) ) { ?>
			<div class="um-followers-btn">
				<?php echo UM()->Followers_API()->api()->follow_button( $user_id, get_current_user_id() ); ?>
			</div>
		<?php } ?>

		<?php $output = ob_get_contents();
		ob_end_clean();
		return $output;
	}


	/***
	***	@shortcode
	***/
	function ultimatemember_followers( $args = array() ) {
		$defaults = array(
			'user_id' 		=> ( um_is_core_page('user') ) ? um_profile_id() : get_current_user_id(),
			'style' 		=> 'default',
			'max'			=> 11
		);
		$args = wp_parse_args( $args, $defaults );

		/**
		 * @var $user_id;
		 * @var $style;
		 * @var $max;
		 */
		extract( $args );

		$current_user = um_user('ID');

		ob_start();
		
		if ( $style == 'avatars' ) {
			$tpl = 'followers-mini';
		} else {
			$tpl = 'followers';
		}
		
		$file       = um_followers_path . 'templates/'.$tpl.'.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/'.$tpl.'.php';
		
		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			$followers = UM()->Followers_API()->api()->followers( $user_id, $max );
			include_once $file;
		}

		$output = ob_get_contents();
		ob_end_clean();

		um_fetch_user( $current_user );
		
		return $output;
	}
	
	/***
	***	@shortcode
	***/
	function ultimatemember_following( $args = array() ) {
		$defaults = array(
			'user_id' 		=> ( um_is_core_page('user') ) ? um_profile_id() : get_current_user_id(),
			'style' 		=> 'default',
			'max'			=> 11
		);
		$args = wp_parse_args( $args, $defaults );

		/**
		 * @var $user_id;
		 * @var $style;
		 * @var $max;
		 */
		extract( $args );

		$current_user = um_user('ID');

		ob_start();
		
		if ( $style == 'avatars' ) {
			$tpl = 'following-mini';
		} else {
			$tpl = 'following';
		}
		
		$file       = um_followers_path . 'templates/'.$tpl.'.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/'.$tpl.'.php';
		
		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			$following = UM()->Followers_API()->api()->following( $user_id );
			include_once $file;
		}

		$output = ob_get_contents();
		ob_end_clean();

		um_fetch_user( $current_user );

		return $output;
	}

}