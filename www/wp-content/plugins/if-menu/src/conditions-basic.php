<?php

add_filter('if_menu_conditions', 'if_menu_basic_conditions');

function if_menu_basic_conditions(array $conditions) {
	global $wp_roles;


	// User roles
	foreach ($wp_roles->role_names as $roleId => $role) {
		$conditions[] = array(
			'id'		=>	'user-is-' . $roleId,
			'name'		=>	sprintf(__('Is %s', 'if-menu'), $role),
			'alias'		=>	sprintf(__('User is %s', 'if-menu'), $role),
			'condition'	=>	function() use($roleId) {
				global $current_user;
				return is_user_logged_in() && in_array($roleId, $current_user->roles);
			},
			'group'		=>	__('User', 'if-menu')
		);
	}

	if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE === true) {
		$conditions[] = array(
			'id'		=>	'user-is-super-admin',
			'name'		=>	sprintf(__('Is %s', 'if-menu'), 'Super Admin'),
			'condition'	=>	'is_super_admin',
			'group'		=>	__('User', 'if-menu')
		);
	}


	// User state
	$conditions[] = array(
		'id'		=>	'user-logged-in',
		'name'		=>	__('Is logged in', 'if-menu'),
		'alias'		=>	__('User is logged in', 'if-menu'),
		'condition'	=>	'is_user_logged_in',
		'group'		=>	__('User', 'if-menu')
	);

	if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE === true) {
		$conditions[] = array(
			'id'		=>	'user-logged-in-current-site',
			'name'		=>	__('Is logged in for current site', 'if-menu'),
			'condition'	=>	function() {
				return current_user_can('read');
			},
			'group'		=>	__('User', 'if-menu')
		);
	}


	// Page type
	$conditions[] = array(
		'id'		=>	'front-page',
		'name'		=>	__('Front Page', 'if-menu'),
		'condition'	=>	'is_front_page',
		'group'		=>	__('Page type', 'if-menu')
	);

	$conditions[] = array(
		'id'		=>	'single-post',
		'name'		=>	__('Single Post', 'if-menu'),
		'condition'	=>	'is_single',
		'group'		=>	__('Page type', 'if-menu')
	);

	$conditions[] = array(
		'id'		=>	'single-page',
		'name'		=>	__('Page', 'if-menu'),
		'condition'	=>	'is_page',
		'group'		=>	__('Page type', 'if-menu')
	);


	// Devices
	$conditions[] = array(
		'id'		=>	'is-mobile',
		'name'		=>	__('Mobile', 'if-menu'),
		'condition'	=>	'wp_is_mobile',
		'group'		=>	__('Device', 'if-menu')
	);


	// Language
	$conditions[] = array(
		'id'		=>	'language-is-rtl',
		'name'		=>	__('Is RTL', 'if-menu'),
		'condition'	=>	'is_rtl',
		'group'		=>	__('Language', 'if-menu')
	);


	return $conditions;
}
