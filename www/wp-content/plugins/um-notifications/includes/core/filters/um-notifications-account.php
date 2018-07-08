<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@add tab to account page
	***/
	add_filter('um_account_page_default_tabs_hook', 'um_notification_account_tab', 100 );
	function um_notification_account_tab( $tabs ) {

		$tabs[445]['webnotifications']['icon'] = 'um-faicon-bell';
		$tabs[445]['webnotifications']['title'] = __('Web notifications','um-notifications');
		$tabs[445]['webnotifications']['submit_title'] = __('Update Settings','um-notifications');

		return $tabs;
	}


	/***
	***	@add content to account tab
	***/
	add_filter( 'um_account_content_hook_webnotifications', 'um_account_content_hook_webnotifications' );
	function um_account_content_hook_webnotifications( $output ) {
		ob_start();

		$user_id = get_current_user_id();

		$logs = UM()->Notifications_API()->api()->get_log_types();

		?>

		<div class="um-field" data-key="">
			<div class="um-field-label"><strong><?php _e( 'Receiving Notifications', 'um-notifications' ); ?></strong></div>
			<div class="um-field-area">

				<?php foreach( $logs as $key => $array ) {

					if ( ! UM()->options()->get( 'log_' . $key ) )
						continue;

					$enabled = UM()->Notifications_API()->api()->user_enabled( $key, $user_id );

				if ( $enabled ) { // get notified automatically? ?>

					<label class="um-field-checkbox active">
						<input type="checkbox" name="um-notifyme[<?php echo $key; ?>]" value="1" checked />
						<span class="um-field-checkbox-state"><i class="um-icon-android-checkbox-outline"></i></span>
						<span class="um-field-checkbox-option"><?php echo $array['account_desc']; ?></span>
					</label>

					<?php } else { ?>

					<label class="um-field-checkbox">
						<input type="checkbox" name="um-notifyme[<?php echo $key; ?>]" value="1"  />
						<span class="um-field-checkbox-state"><i class="um-icon-android-checkbox-outline-blank"></i></span>
						<span class="um-field-checkbox-option"><?php echo $array['account_desc']; ?></span>
					</label>

					<?php } ?>

				<?php } wp_reset_postdata(); ?>

				<div class="um-clear"></div>

			</div>
		</div>

		<?php

		$output .= ob_get_contents();
		ob_end_clean();

		return $output;
	}
