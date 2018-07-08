<?php
/**
 * Plugin Name:       Fast User Switching
 * Description:       Fast user switching between users and roles directly from the admin bar - switch from a list or search for users/roles by id, username, mail etc.
 * Version:           1.4.7
 * Author:            Tikweb
 * Author URI:        http://www.tikweb.dk/
 * Plugin URI:        http://www.tikweb.com/wordpress/plugins/fast-user-switching/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fast-user-switching
 * Domain Path:       /languages
*/

/*
Fast User Switching is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Fast User Switching is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Fast User Switching. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

if(!defined('ABSPATH')) exit;

/**
 * Options page
 */
include __DIR__.'/admin-option.php';

/**
 * Register Activation hook
 */
register_activation_hook( __FILE__, 'tikemp_installation' );

/**
 * Run after installtion
 */
function tikemp_installation(){

	$fus_settings = get_option('fus_settings',[]);

	if ( empty($fus_settings) ){

		$fus_settings['fus_name'] = 1;
		$fus_settings['fus_role'] = 1;
		$fus_settings['fus_username'] = 1;

		update_option( 'fus_settings', $fus_settings );
	}
}


if ( !class_exists('Tikweb_Impersonate') ):

	class Tikweb_Impersonate {
		/**
		 * Register all the hooks and filters for the plugin
		 */
		public function __construct() {
			// WP logout hook
			add_action('wp_logout',	array($this, 'unimpersonate'), 1);
			
			// Only admins can use this plugin (for obvious reasons)
			if(!role_can_impersonate()) return;
			
			// Add a column to the user list table which will allow you to impersonate that user
			add_filter('manage_users_columns', array($this, 'user_table_columns'));
			add_action('manage_users_custom_column', array($this, 'user_table_columns_value'), 10, 3);
			
			// Is this request attempting to impersonate someone?
			if(isset($_GET['impersonate']) && !empty($_GET['impersonate'])){
				$this->impersonate($_GET['impersonate']);
			}

		}//End of __construct
		
		/**
		 * Add an additional column to the users table
		 * @param $columns - An array of the current columns
		 */
		public function user_table_columns($columns) {
			$columns['Tikweb_Impersonate']	= __('Switch user', 'fast-user-switching');
			return $columns;
		}
		
		/**
		 * Return the value for custom columns
		 * @param String $value		- Current value, not used
		 * @param String $column	- The name of the column to return the value for
		 * @param Integer $user_id	- The ID of the user to return the value for
		 * @return String
		 */
		function user_table_columns_value($value, $column, $user_id) {
			switch($column) {
				case 'Tikweb_Impersonate':
					$impersonate_url	= admin_url("?impersonate=$user_id");
					return "<a href='$impersonate_url'>".__('Switch user','fast-user-switching')."</a>";
				default: 
					return $value;
			}
		}

		public function saveRecentUser($user){

			$user_id = get_current_user_id();

			if ( current_user_can('manage_options') ){
				$recent_user_opt = get_option('tikemp_recent_imp_users',[]);
			} else {
				$recent_user_opt = get_user_meta( $user_id, 'tikemp_recent_imp_users', true );

				if ( empty($recent_user_opt) ){
					$recent_user_opt = [];
				}
			}

			$wp_date_format = get_option('date_format');
			$fus_settings = get_option('fus_settings',[]);
			
			$roles = tikemp_get_readable_rolename(array_shift($user->roles));

			if ( isset($fus_settings['fus_name']) ){
				$name_display = $user->first_name.' '.$user->last_name;				
			}else {
				$name_display = ' ';
			}

			if ( isset($fus_settings['fus_role']) ){
				$role_display = $roles;
			} else {
				$role_display = '';
			}

			if ( isset($fus_settings['fus_username']) ){
				$role_display .= ' - '.$user->user_login;
			}

			$date_display = date($wp_date_format);

			$keep = $user->data->ID.'&'.$name_display.'&'.$role_display.'&'.$date_display;

			if ( !in_array($keep, $recent_user_opt) ){
				array_unshift( $recent_user_opt, $keep );
			}

			if ( in_array($keep,$recent_user_opt) && $recent_user_opt[0] !== $keep ){
				$key = array_search($keep, $recent_user_opt);
				unset($recent_user_opt[$key]);
				array_unshift($recent_user_opt, $keep);
			}

			$recent_user_opt = array_slice($recent_user_opt, 0,5);
			
			if ( current_user_can('manage_options') ){
				update_option('tikemp_recent_imp_users',$recent_user_opt);
			} else {
				update_user_meta( $user_id, 'tikemp_recent_imp_users', $recent_user_opt, '' );
			}

		}//End saveRecentUser

		/**
		 * Get get user id and switch to
		 */
		public function impersonate($user_id){

			global $current_user;
			
			$block_attempt = false;
			$user = get_userdata( $user_id );

			if ( $user == false ){

				$block_attempt = true;
			}

			if ( !current_user_can( 'manage_options' ) ){
				
				if ( in_array('administrator', (array) $user->roles) ){
					$block_attempt = true;
				}

			}

			if( $block_attempt === true ){
				$redirect = add_query_arg( 'impna', 'true2', admin_url() );
				return wp_redirect( $redirect );
			}

			$this->saveRecentUser($user);
			
			// We need to know what user we were before so we can go back
			$hashed_id = $this->encryptDecrypt('encrypt', $current_user->ID);
			setcookie('impersonated_by_'.COOKIEHASH, $hashed_id, 0, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
			
			// Login as the other user
			wp_set_auth_cookie($user_id, false);

			// If impresonate user is vendor than set vendor cookies.
			if( class_exists('WC_Product_Vendors_Utils') ){
				if ( WC_Product_Vendors_Utils::is_vendor( $user_id ) ){
					$vendor_data = WC_Product_Vendors_Utils::get_all_vendor_data( $user_id );
					$vendor_id = key($vendor_data);
					setcookie('woocommerce_pv_vendor_id_' . COOKIEHASH, absint($vendor_id), 0, SITECOOKIEPATH, COOKIE_DOMAIN);
				}				
			}//End if

			if ( isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ){

				$redirect_url = $_SERVER['HTTP_REFERER'];

				if ( strpos($redirect_url, '/wp-admin/') != false ){
					$redirect_url = admin_url();
				}

			} else {

				$redirect_url = admin_url();
				
			}

			// add impersonatting param with url to detect this request is impersonatting.
			$redirect_url = $redirect_url.'?imp=true';

			
			wp_redirect( $redirect_url );
			exit;
		}//End impersonate
		
		/**
		 * Switch back to old user
		 */
		public function unimpersonate(){
			$impersonated_by = self::impersonatedBy();
			if(!empty($impersonated_by)){
				wp_set_auth_cookie($impersonated_by, false);
				// Unset the cookie
				setcookie('impersonated_by_'.COOKIEHASH, 0, time()-3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);

				if ( isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ){
					$redirect_url = $_SERVER['HTTP_REFERER'];
				} else {
					$redirect_url = admin_url();
				}

				wp_redirect( $redirect_url );
				exit;
			}
		}//End unimpersonate

		/**
		 * Initialize
		 */
		public static function init(){
			$instance = new self;
			return $instance;
		}

		/**
		 * Get impersonated user from cookie
		 */
		private static function impersonatedBy(){
			$key = 'impersonated_by_'.COOKIEHASH;
			if(isset($_COOKIE[$key]) && !empty($_COOKIE[$key])){
				$user_id = self::encryptDecrypt('decrypt', $_COOKIE[$key]);
				return $user_id;
			}else{
				return false;
			}
		}//impersonatedBy

		/**
		 * Change logout text
		 */
		public static function changeLogoutText($wp_admin_bar){
			// If user is impersonating, change the logout text
			$impersonatedBy = self::impersonatedBy();
			if(!empty($impersonatedBy)){
				$args = array(
					'id'    => 'logout',
					'title' => __('Switch to own user', 'fast-user-switching'),
					'meta'  => array( 'class' => 'logout' )
				);
				$wp_admin_bar->add_node($args);
			}
		}//End changeLogoutText

		/**
		 * Encript and Decrypt
		 */
		private static function encryptDecrypt($action, $string){
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$secret_key = 'This is fus hidden key';
			$secret_iv = 'This is fus hidden iv';
			// hash
			$key = hash('sha256', $secret_key);

			// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			if ($action == 'encrypt'){
				$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
				$output = base64_encode($output);
			}else if($action == 'decrypt'){
				$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			}
			return $output;
		}// End encryptDecrypt


	} // Class end
	
	// Initialize the class
	add_action('init', array('Tikweb_Impersonate', 'init'));

	// Admin bar hook
	add_action('admin_bar_menu', array('Tikweb_Impersonate', 'changeLogoutText'));
endif; 

function tikemp_load_plugin_textdomain() {
    load_plugin_textdomain('fast-user-switching', FALSE, basename( dirname( __FILE__ ) ) . '/languages/');
}
add_action('plugins_loaded', 'tikemp_load_plugin_textdomain');


/**
 * plugin script to be enqueued in admin and frontend.
 * @return [type] [description]
 */
function tikemp_scripts(){
	wp_enqueue_script('tikemp_search_scroll', plugins_url( '/js/jquery.nicescroll.min.js', __FILE__ ), array( 'jquery' ),'1.1',true);
	wp_enqueue_script('tikemp_script', plugins_url( '/js/script.js', __FILE__ ), array( 'jquery','tikemp_search_scroll' ),'1.2',true);
}

add_action( 'admin_enqueue_scripts', 'tikemp_scripts' );
add_action( 'wp_enqueue_scripts', 'tikemp_scripts' );

/**
 * Return list of impersonated recent users list.
 * @return string [description]
 */
function tikemp_impersonate_rusers(){

	$ret = '';
	
	if ( current_user_can('manage_options') ){
		$opt = get_option('tikemp_recent_imp_users',[]);
	} else {
		$opt = get_user_meta( get_current_user_id(), 'tikemp_recent_imp_users', true );
	}

	$fus_settings = get_option('fus_settings',[]);

	if ( !empty($opt) ){
		foreach ($opt as $key => $value) {
			
			$user_role_display = '';

			$user = explode('&', $value);
			$user = array_filter($user);
			$user_id = isset($user[0]) ? $user[0] : '';
			$user_name = isset($user[1]) ? trim($user[1]) : '';
			$user_role = isset($user[2]) ? trim($user[2]) : '';
			$last_login_date = isset($user[3]) ? trim($user[3]) : '';

			if ( !empty($user_name) && !empty($user_role) ){
				$user_role_display = sprintf('( %s )', $user_role );
			} else {

				$rc = explode('-', $user_role);
				$rc = array_map('trim',$rc);
				$rc = array_filter($rc);

				if ( count($rc) < 2 ){
					$user_role = str_replace('-', '', $user_role);
				}

				$user_role_display = $user_role;
			}

			if ( !empty($last_login_date) && isset($fus_settings['fus_showdate']) ){
				$last_login_date = sprintf('<span class="small-date">%s</span>',$last_login_date);
			} else {
				$last_login_date = '';
			}

			$ret .= '<a href="'.admin_url("?impersonate=$user_id").'">'.$user_name.' '.$user_role_display.' '.$last_login_date.'</a>'.PHP_EOL;

			
		}
	}

	return $ret;
}


function user_can_switch( $user_data = null ){

	$can_switch = false;

	// check if admin , directy give him access
	if ( current_user_can( 'manage_options' ) ){
		return true;
	}

	// if no user_data passed to function, get user data.
	if ( empty($user_data) ){
		$user_data = wp_get_current_user();
	}

	// if user isn't exists ( case visitor ) return false
	if ( ! $user_data->exists() ){
		return false;
	}

	if ( current_user_can( 'edit_users' ) || current_user_can( 'list_users' ) ){
		return true;
	}

}

function role_can_impersonate( ){

	if ( current_user_can( 'manage_options' ) ){
		return true;
	}

	$cur_user = wp_get_current_user();

	if ( !$cur_user->exists() ){
		return false;
	}

	$settings = get_option('fus_settings');

	if ( isset($settings['fus_roles']) && !empty($settings['fus_roles']) ){

		
		$cur_user_roles = (array) $cur_user->roles;
		$matched = array_intersect($cur_user_roles, $settings['fus_roles']);

		if ( count( $matched ) > 0 ){
			return true;
		} else {
			return false;
		}

	} else{
		return false;
	}
}

/**
 * Rendar user search function in wp admin bar. 
 */
function tikemp_adminbar_rendar(){

	// if admin_bar is showing.
	if(is_admin_bar_showing()){

		global $wp_admin_bar;

		// if current user can edit_users than he can see this.
		if( role_can_impersonate() ){

			$wp_admin_bar->add_menu(
				array(
					'id'    => 'tikemp_impresonate_user',
					'title' => __('Switch user','fast-user-switching'),
					'href'  => '#',
				)
			);

			// search form
			$html = '<div id="tikemp_search">';
				$html .= '<form action="#" method="POST" id="tikemp_usearch_form" class="clear">';
					$html .= '<input type="text" name="tikemp_username" id="tikemp_username" placeholder="'.__('Username or ID','fast-user-switching').'">';
					$html .= '<input type="submit" value="'.__('Search','fast-user-switching').'" id="tikemp_search_submit">';
					$html .= '<input type="hidden" name="tikemp_search_nonce" value="'.wp_create_nonce( "tikemp_search_nonce" ).'">';
					$html .= '<div class="wp-clearfix"></div>';
				$html .= '</form>';
				$html .= '<div id="tikemp_usearch_result"></div>';
				$html .= '<div id="tikemp_recent_users">';
					$html .= '<strong>'.__('Recent Users','fast-user-switching').'</strong>';
					$html .= '<hr>'.tikemp_impersonate_rusers();
				$html .= '</div>';

				if ( current_user_can('manage_options') ):
					$html .= '<div id="tikemp_settings_wrap">';
						$html .= '<a href="'.admin_url("options-general.php?page=fast_user_switching").'">'.__('Settings','fast-user-switching').'</a>';
					$html .= '</div>';
				endif;
			$html .= '</div>';

			$wp_admin_bar->add_menu(
				array(
					'id'		=> 'tikemp_impresonate_user_search',
					'parent'	=> 'tikemp_impresonate_user',
					'title'		=> $html,
				)
			);

		}//if(current_user_can('manage_optiona'))

	}//if(is_admin_bar_showing())	
}
add_action( 'wp_before_admin_bar_render', 'tikemp_adminbar_rendar', 9999, 1 );


/**
 * User search on ajax request
 */
function tikemp_user_search(){

	$query = isset($_POST['username']) ? trim($_POST['username']) : '';
	$nonce = $_POST['nonce'];

	if ( !wp_verify_nonce($nonce,'tikemp_search_nonce') ){
		exit();
	}

	$args = array(
		'search'	=> is_numeric( $query ) ? $query : '*' . $query . '*'
	);

	if ( !is_email( $query ) && strpos($query, '@') !== false ){
		$args['search_columns'] = ['user_login','user_email'];
	}

	if ( !current_user_can( 'manage_options' ) ){
		$args['role__not_in'] = 'Administrator';
	}

	$user_query = new WP_User_Query( $args );

	$ret = '';

	$site_roles = tikemp_get_roles();

	if ( !empty($user_query->results) ){
		
		$fus_settings = get_option('fus_settings',[]);

		foreach ( $user_query->results as $user ) {

			if( $user->ID == get_current_user_id() ) {
				continue;
			}

			if ( empty($fus_settings) ){
				$name_display = $user->first_name.' '.$user->last_name;
				$user_role_display = ' ('.$site_roles[array_shift($user->roles)].' - '.$user->user_login.')';
			}

			if ( isset($fus_settings['fus_name']) ){
				$name_display = $user->first_name.' '.$user->last_name;
			} else {
				$name_display = '';
			}

			if ( isset($fus_settings['fus_role']) ){
				$user_role_display = $site_roles[array_shift($user->roles)];
			} else {
				$user_role_display = '';
			}

			if ( isset($fus_settings['fus_username']) ){

				if ( empty($user_role_display) ){
					$user_role_display = $user->user_login;
				} else {
					$user_role_display .= ' - '.$user->user_login;
				}

				$user_role_display = trim($user_role_display);

				if ( !empty($name_display) && !empty($user_role_display) ){
					$user_role_display = sprintf('( %s )', $user_role_display);
				}
			}

			$ret .= '<a href="'.admin_url("?impersonate={$user->ID}").'">'.$name_display.' '.$user_role_display.'</a>'.PHP_EOL;
		}
	} else {
		$ret .= '<strong>'.__('No user found!','fast-user-switching').'</strong>'.PHP_EOL;
	}

	echo $ret;
	die();
}
add_action( 'wp_ajax_tikemp_user_search', 'tikemp_user_search' );
add_action( 'wp_ajax_nopriv_tikemp_user_search', 'tikemp_user_search' );

/**
 * Adminbar search bar 
 */
function tikemp_styles(){
?>
<style type="text/css">
#wpadminbar .quicklinks #wp-admin-bar-tikemp_impresonate_user ul li .ab-item{height:auto}#wpadminbar .quicklinks #wp-admin-bar-tikemp_impresonate_user #tikemp_username{height:22px;font-size:13px !important;padding:2px;width:145px;border-radius:2px !important;float:left;box-sizing:border-box !important;line-height: 10px;}#tikemp_search{width:auto;box-sizing:border-box}#tikemp_search_submit{height:22px;padding:2px;line-height:1.1;font-size:13px !important;border:0 !important;float:right;background-color:#fff !important;border-radius:2px !important;width:74px;box-sizing:border-box;color:#000 !important;}#tikemp_usearch_result{width:100%;max-height: 320px;overflow-y: auto;margin-top:10px;float:left;}#tikemp_usearch_form{width: 226px}#tikemp_recent_users{width:100%;float:left;}form#tikemp_usearch_form input[type="text"]{background-color:#fff !important;}#tikemp_settings_wrap{width: 100%;float:left;border-top:1px solid #ccc;}#wpadminbar .quicklinks .menupop ul li a, #wpadminbar .quicklinks .menupop.hover ul li a {color: #b4b9be;}
</style>
<?php
}
add_action( 'wp_head', 'tikemp_styles' );
add_action( 'admin_head', 'tikemp_styles' );

/**
 * Get site user roles
 * @return array array of roles and capabilities.
 */
function tikemp_get_roles(){
	
	$all_roles = wp_roles()->roles;
    
    $return_array = [];
    
    foreach($all_roles as $key => $role){
        $return_array[$key] = $role['name'];
    }

    return $return_array;
}

/**
 * Return readable rolename
 */
function tikemp_get_readable_rolename($role){
	$all_roles = tikemp_get_roles();

	$ret = isset($all_roles[$role]) ? $all_roles[$role] : 'subscriber';

	return $ret;
}

/**
 * Set ajax url
 */
function tikemp_ajax_urls(){
	?>
	<script>
		var tikemp_ajax_url = "<?php echo admin_url('admin-ajax.php');?>";
	</script>
	<?php
}
add_action( 'wp_head', 'tikemp_ajax_urls' );
add_action( 'admin_head', 'tikemp_ajax_urls' );

/**
 * Add settings in plugin action links
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'tikemp_plugin_action_links' );
function tikemp_plugin_action_links( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=fast_user_switching') ) .'">Settings</a>';
   return $links;
}

/**
 * Empty Woocommerce Cart during switching
 */
add_action('init',function(){

	if ( !isset($_GET['imp']) && empty($_GET['imp']) ){
		return;
	}

	// disable on admin area, where we don't have access to $woocommerce global variable.
	if ( is_admin() ){
		return;
	}

	// if Woocommerce plugin is not activate than exit.
	if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ){
		return;
	}

	global $woocommerce;

	$settings = get_option('fus_settings',true);

	if ( isset($settings['fus_woo']) ){
		$woocommerce->cart->empty_cart();
	}
		

});

/**
 * Add User switching link on order detail page.
 */
add_action('woocommerce_admin_order_data_after_order_details',function( $order ){

	// if role can't impersonate than let it go!
	if ( !role_can_impersonate() ){
		return false;
	}

	$settings = get_option('fus_settings',true);

	if ( !isset($settings['fus_showon_woo_order']) || empty($settings['fus_showon_woo_order']) ){
		return false;
	}

	$user 		=	get_user_by( 'id', $order->get_user_id() );

	if( empty($user) ){
		return false;
	}

	echo '<p class="form-field form-field-wide"><a href="?impersonate='.$user->ID.'"> '.__('Switch to ','fast-user-switching').$user->data->display_name.' </a></p>';

});