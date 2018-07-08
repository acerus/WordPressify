<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@extend mycred settings
	***/
	add_filter('um_mycred_extend_award_settings', 'um_social_login_mycred_settings_award');
	function um_social_login_mycred_settings_award( $settings ) {
		$networks = UM()->Social_Login_API()->networks;
		foreach( $networks as $id => $arr ) {
			$settings[$id] = sprintf(__('user connects with %s','um-social-login'), $arr['name']);
		}
		return $settings;
	}
	
	/***
	***	@extend mycred settings
	***/
	add_filter('um_mycred_extend_deduct_settings', 'um_social_login_mycred_settings_deduct');
	function um_social_login_mycred_settings_deduct( $settings ) {
		$networks = UM()->Social_Login_API()->networks;
		foreach( $networks as $id => $arr ) {
			$settings[$id] = sprintf(__('user disconnects from %s','um-social-login'), $arr['name']);
		}
		return $settings;
	}
	
	/***
	***	@extend settings
	***/
add_filter( 'um_settings_structure', 'um_social_login_settings', 10, 1 );

function um_social_login_settings( $settings ) {

    $networks = UM()->Social_Login_API()->networks;

    $settings['licenses']['fields'][] = array(
        'id'      		=> 'um_social_login_license_key',
        'label'    		=> __( 'Social Login License Key', 'um-social-login' ),
        'item_name'     => 'Social Login',
        'author' 	    => 'Ultimate Member',
        'version' 	    => um_social_login_version,
    );

    $key = ! empty( $settings['extensions']['sections'] ) ? 'social-login' : '';
    $settings['extensions']['sections'][$key] = array(
        'title'     => __( 'Social Login','um-social-login'),
        'fields'    => array(
            array(
                'id'       		=> 'account_tab_social',
                'type'     		=> 'checkbox',
                'label'   		=> __( 'Social Account Tab','um-social-login' ),
                'tooltip' 	=> __('Enable/disable the Social account tab in account page','um-social-login'),
            ),
            array(
                'id'       		=> 'register_show_social',
                'type'     		=> 'checkbox',
                'label'    		=> __( 'Show social connect on registration forms','um-social-login' ),
                'tooltip' 	=> __('Show/hide social connect on all registration forms by default','um-social-login'),
            ),
            array(
                'id'       		=> 'login_show_social',
                'type'     		=> 'checkbox',
                'label'    		=> __( 'Show social connect on login forms','um-social-login' ),
                'tooltip' 	=> __('Show/hide social connect on all login forms by default','um-social-login'),
            )
        )
    );

    $i = 0;
    foreach( $networks as $id => $arr ) {
        $i++;
        $sort[$i] = $id;
    }

    foreach ( $networks as $network_id => $array ) {

        $options = array();

        $options[] = array(
            'id'       		=> 'enable_' . $network_id,
            'type'     		=> 'checkbox',
            'label'    		=> sprintf(__('%s Social Connect','um-social-login'), $array['name'] ),
        );

        if ( isset( $array['opts'] ) ) {
            foreach ( $array['opts'] as $opt_id => $title ) {
                $options[] = array(
                    'id'       		=> $opt_id,
                    'type'     		=> 'text',
                    'label'    		=> $title,
                    'conditional'		=> array( "enable_$network_id", '=', '1' ),
                );
            }
        }

        $settings['extensions']['sections'][$key]['fields'] = array_merge( $settings['extensions']['sections'][$key]['fields'], $options );
    }

    return $settings;
}