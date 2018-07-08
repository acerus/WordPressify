<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@custom error
	***/
	add_filter('um_custom_error_message_handler', 'um_social_login_custom_error', 10, 2 );
	function um_social_login_custom_error( $msg, $err_t ) {
		$providers = UM()->Social_Login_API()->available_networks();
		
		foreach( $providers as $key => $info ) {
			if ( strstr( $err_t, $key ) && $err_t == $key . '_exist' ) {
				$msg = sprintf(__(' This %s account is already linked to another user.','um-social-login'), $info['name']);
			}
		}
		return $msg;
	}
	
	/***
	***	@sync user profile photo
	***/
	add_filter('um_user_avatar_url_filter', 'um_social_login_synced_profile_photo', 100, 2 );
	function um_social_login_synced_profile_photo( $url, $user_id ) {
		if ( get_user_meta( $user_id, 'synced_profile_photo', true ) ) {
			$url = get_user_meta( $user_id, 'synced_profile_photo', true );
			// ssl enabled?
			if ( is_ssl() && !strstr( $url, 'vk.me' ) ) {
				$url = str_replace('http://','https://', $url );
			}
		}
		return $url;
	}
	
	/***
	***	@add tab to account page
	***/
	add_filter('um_account_page_default_tabs_hook', 'um_social_login_account_tab', 100 );
	function um_social_login_account_tab( $tabs ) {
		$tabs[450]['social']['icon'] = 'um-faicon-sign-in';
		$tabs[450]['social']['title'] = __('Social Connect','um-social-login');
		$tabs[450]['social']['show_button'] = false;

		return $tabs;
	}


	/***
	***	@add content to account tab
	***/
	add_filter( 'um_account_content_hook_social', 'um_account_content_hook_social' );
	function um_account_content_hook_social( $output ) {
		// important to only show available networks
		$providers = UM()->Social_Login_API()->available_networks();

		if ( empty( $providers ) )
			return $output;

		ob_start();
		
		$user_id = get_current_user_id(); ?>
		
		<div class="um-field" data-key="">
	
			<?php foreach( $providers as $provider => $array ) { ?>
			
				<div class="um-provider">

					<div class="um-provider-title">
						<?php printf(__('Connect to %s','um-social-login'), $array['name']); ?>
						<?php do_action('um_social_login_after_provider_title', $provider, $array); ?>
					</div>

					<div class="um-provider-conn">

						<?php if ( UM()->Social_Login_API()->is_connected( $user_id, $provider ) ) { ?>

							<?php if ( UM()->Social_Login_API()->get_user_photo( $user_id, $provider ) ) { ?>

							<div class="um-provider-user-photo"><a href="<?php echo get_user_meta( $user_id, $providers[$provider]['sync']['link'], true ); ?>" target="_blank" title="<?php echo get_user_meta( $user_id, $providers[$provider]['sync']['handle'], true ); ?>"><img src="<?php echo UM()->Social_Login_API()->get_user_photo( $user_id, $provider ); ?>" class="um-provider-photo small" /></a></div>

							<?php } else if ( UM()->Social_Login_API()->get_dynamic_user_photo( $user_id, $provider ) ) { ?>

							<div class="um-provider-user-photo"><a href="<?php echo get_user_meta( $user_id, $providers[$provider]['sync']['link'], true ); ?>" target="_blank" title="<?php echo get_user_meta( $user_id, $providers[$provider]['sync']['handle'], true ); ?>"><img src="<?php echo UM()->Social_Login_API()->get_dynamic_user_photo( $user_id, $provider ); ?>" class="um-provider-photo small" /></a></div>

							<?php } ?>

							<div class="um-provider-user-handle"><a href="<?php echo get_user_meta( $user_id, $providers[$provider]['sync']['link'], true ); ?>" target="_blank"><?php echo get_user_meta( $user_id, $providers[$provider]['sync']['handle'], true ); ?></a></div>

							<div class="um-provider-disconnect">(<a href="<?php echo UM()->Social_Login_API()->disconnect_url( $provider ); ?>">Disconnect</a>)</div>

						<?php } else { ?>

							<a title="<?php printf(__('Connect to %s','um-social-login'), $array['name']); ?>" href="<?php echo UM()->Social_Login_API()->login_url( $provider ); ?>" class="um-social-btn um-social-btn-<?php echo $provider; ?>"><i class="<?php echo $array['icon']; ?>"></i><?php printf(__('Connect to %s','um-social-login'), $array['name']); ?></a>

						<?php } ?>

					</div>

					<div class="um-clear"></div>

				</div>
			
			<?php } ?>
			
		</div>
		
		<?php $output .= ob_get_clean();
		return $output;
	}