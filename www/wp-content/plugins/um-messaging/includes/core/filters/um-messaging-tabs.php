<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@messaging profile tab
	***/
	add_filter('um_profile_tabs', 'um_messaging_add_tab', 200 );
	function um_messaging_add_tab( $tabs ) {
		if ( um_profile_id() == get_current_user_id() && um_user('enable_messaging') ) {
			
			$tabs['messages'] = array(
				'name' => __('Messages','um-messaging'),
				'icon' => 'um-faicon-envelope-o',
			);

			$count = UM()->Messaging_API()->api()->get_unread_count( um_profile_id() );
			$tabs['messages']['notifier'] = ( $count > 10 ) ? 10 . '+' : $count;

		}
		return $tabs;
	}