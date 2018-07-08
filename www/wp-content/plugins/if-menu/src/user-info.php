<?php


// Get visitor's IP

if (!function_exists('get_user_ip')) {
	function get_user_ip() {
		return apply_filters('user_ip', '');
	}
}

add_filter('user_ip', 'if_menu_user_ip');

function if_menu_user_ip($ip = '') {
	if (empty($ip)) {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						return $ip;
					}
				}
			}
		}
	}

	return $ip;
}



// Get visitor's Country Code, ex: US, ES, etc     /    XX or empty = Unknown

if (!function_exists('get_user_country_code')) {
	function get_user_country_code() {
		return strtoupper(apply_filters('user_country_code', ''));
	}
}

add_filter('user_country_code', 'if_menu_user_country_code_woocommerce');
add_filter('user_country_code', 'if_menu_user_country_code_headers');
add_filter('user_country_code', 'if_menu_user_country_code_metaapis');

function if_menu_user_country_code_woocommerce($countryCode = '') {
	if (!$countryCode && class_exists('WC_Geolocation')) {
		$location = WC_Geolocation::geolocate_ip();
		if ($location['country'] && !in_array($location['country'], array('A1', 'A2', 'EU', 'AP'))) {
			$countryCode = $location['country'];
		}
	}

	return $countryCode;
}

function if_menu_user_country_code_headers($countryCode = '') {
	if (empty($countryCode)) {
		foreach (array('HTTP_CF_IPCOUNTRY', 'X-AppEngine-country', 'CloudFront-Viewer-Country', 'GEOIP_COUNTRY_CODE', 'HTTP_X_COUNTRY_CODE', 'HTTP_X_GEO_COUNTRY') as $key) {
			if (isset($_SERVER[$key]) && $_SERVER[$key] && !in_array($_SERVER[$key], array('XX', 'ZZ', 'A1', 'A2', 'EU', 'AP'))) {
				return $_SERVER[$key];
			}
		}
	}

	return $countryCode;
}

function if_menu_user_country_code_metaapis($countryCode = '') {
	if (!$countryCode) {
		$ip = get_user_ip();

		if (false === ($countryCode = get_transient('ip-country-code-' . sanitize_key($ip)))) {
			$request = wp_remote_get('https://apis.blue/ip/' . $ip . '?key=METAce6b9c6c28e4f536b49b9dbaAPIS');
			$data = json_decode(wp_remote_retrieve_body($request) ?: '[]');
			if (isset($data->country) && $data->country) {
				$countryCode = $data->country;
				set_transient('ip-country-code-' . sanitize_key($ip), $countryCode, WEEK_IN_SECONDS);
			} else {
				$countryCode = '';
			}
		}
	}

	return $countryCode;
}
