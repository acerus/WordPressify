<?php
/**
 * Function that creates the "Userlisting" custom post type
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_create_userlisting_forms_cpt(){
    $labels = array(
        'name' 					=> __( 'User Listing', 'profile-builder'),
        'singular_name' 		=> __( 'User Listing', 'profile-builder'),
        'add_new' 				=> __( 'Add New', 'profile-builder'),
        'add_new_item' 			=> __( 'Add new User Listing', 'profile-builder' ),
        'edit_item' 			=> __( 'Edit the User Listing', 'profile-builder' ) ,
        'new_item' 				=> __( 'New User Listing', 'profile-builder' ),
        'all_items' 			=> __( 'User Listing', 'profile-builder' ),
        'view_item' 			=> __( 'View the User Listing', 'profile-builder' ),
        'search_items' 			=> __( 'Search the User Listing', 'profile-builder' ),
        'not_found' 			=> __( 'No User Listing found', 'profile-builder' ),
        'not_found_in_trash' 	=> __( 'No User Listing found in trash', 'profile-builder' ),
        'parent_item_colon' 	=> '',
        'menu_name' 			=> __( 'User Listing', 'profile-builder' )
    );

    $args = array(
        'labels' 				=> $labels,
        'public' 				=> false,
        'publicly_queryable' 	=> false,
        'show_ui' 				=> true,
        'query_var'          	=> true,
        'show_in_menu' 			=> 'profile-builder',
        'has_archive' 			=> false,
        'hierarchical' 			=> false,
        'capability_type' 		=> 'post',
        'supports' 				=> array( 'title' )        
    );
    
    /* hide from admin bar for non administrators */
    if( !current_user_can( 'manage_options' ) )
        $args['show_in_admin_bar'] = false;

    $wppb_addonOptions = get_option('wppb_module_settings');
    if( $wppb_addonOptions['wppb_userListing'] == 'show' )
        register_post_type( 'wppb-ul-cpt', $args );
}
add_action( 'init', 'wppb_create_userlisting_forms_cpt');

/* Userlisting change classes based on Visible only to logged in users field start */
add_filter( 'wck_add_form_class_wppb_ul_page_settings', 'wppb_userlisting_add_form_change_class_based_on_visible_field', 10, 3 );
function wppb_userlisting_add_form_change_class_based_on_visible_field( $wck_update_container_css_class, $meta, $results ){
    if( !empty( $results ) ){
        if (!empty($results[0]["visible-only-to-logged-in-users"]))
            $votliu_val = $results[0]["visible-only-to-logged-in-users"];
        else
            $votliu_val = '';
        $votliu = Wordpress_Creation_Kit_PB::wck_generate_slug($votliu_val);
        return "update_container_$meta update_container_$votliu visible_to_logged_$votliu";
    }
}
/* Userlisting change classes based on Visible only to logged in users field end */


function wppb_userlisting_scripts() {
    global $wppb_userlisting_shortcode;
    if( isset( $wppb_userlisting_shortcode ) && $wppb_userlisting_shortcode === true ){
        wp_enqueue_script('wppb-userlisting-js', WPPB_PLUGIN_URL . '/modules/user-listing/userlisting.js', array('jquery', 'jquery-touch-punch'), PROFILE_BUILDER_VERSION, true);
        wp_enqueue_style('wppb-ul-slider-css', WPPB_PLUGIN_URL . '/modules/user-listing/jquery-ui-slider.min.css', array(), PROFILE_BUILDER_VERSION );
        wp_enqueue_script('jquery-ui-slider');
    }
}
add_action( 'wp_footer', 'wppb_userlisting_scripts' );

/**
 * Function that generates the merge tags for userlisting
 *
 * @since v.2.0
 *	
 * @param string $type The type of merge tags which we want to generate. It can be meta or sort, meaning the actual data or the links with which we can sort the data
 * @return array $merge_tags the array of merge tags and their details
 */
function wppb_generate_userlisting_merge_tags( $type, $template = '' ){
    $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

    $template_tags = array();
    if ( !empty($template) ){
        preg_match_all("/{{[^{}]+}}/", $template, $template_tags );
        foreach( $template_tags[0] as $key => $value){
            $template_tags[0][$key] = trim( $value, " {}/#&?^!>");
        }
    }

    $wppb_manage_fields = apply_filters('wppb_userlisting_merge_tags' , $wppb_manage_fields, $type);
	$merge_tags = array();

	if( $type == 'meta' ){
		$default_field_type = 'default_user_field';
		$user_meta = 'user_meta';
		$number_of_posts = 'number_of_posts';
	}
	else if( $type == 'sort' ){
		$default_field_type = $user_meta = $number_of_posts = 'sort_tag';
	}
	
	if ( $wppb_manage_fields != 'not_found' )
		foreach( $wppb_manage_fields as $key => $value ){
			if ( ( $value['field'] == 'Default - Name (Heading)' ) || ( $value['field'] == 'Default - Contact Info (Heading)' ) || ( $value['field'] == 'Default - About Yourself (Heading)' ) || ( $value['field'] == 'Heading' ) || ( $value['field'] == 'Default - Password' ) || ( $value['field'] == 'Default - Repeat Password' ) || ( $value['field'] == 'Select (User Role)' ) ){
				//do nothing for the headers and the password fields
				
			}elseif ( $value['field'] == 'Default - Username' )
				$merge_tags[] = array( 'name' => $type.'_user_name', 'type' => $default_field_type, 'label' => __( 'Username', 'profile-builder' ) );
				
			elseif ( $value['field'] == 'Default - Display name publicly as' )
				$merge_tags[] = array( 'name' => $type.'_display_name', 'type' => $default_field_type, 'label' => __( 'Display name as', 'profile-builder' ) );
				
			elseif ( $value['field'] == 'Default - E-mail' )
				$merge_tags[] = array( 'name' => $type.'_email', 'type' => $default_field_type, 'label' => __( 'E-mail', 'profile-builder' ) );
				
			elseif ( $value['field'] == 'Default - Website' )
				$merge_tags[] = array( 'name' => $type.'_website', 'type' => $default_field_type, 'label' => __( 'Website', 'profile-builder' ) );

            elseif ( $value['field'] == 'Default - Biographical Info' )
                $merge_tags[] = array( 'name' => $type.'_biographical_info', 'type' => $default_field_type, 'unescaped' => true, 'label' => __( 'Biographical Info', 'profile-builder' ) );

            elseif ( ( $value['field'] == 'Default - Blog Details' ) ) {
                if ( $type == 'meta' ) {
                    $merge_tags[] = array('name' => $type . '_blog_url', 'type' => $default_field_type, 'label' => __('Blog URL', 'profile-builder'));
                }
            }

            elseif ( $value['field'] == 'Upload' ){
				$merge_tags[] = array( 'name' => $type.'_'.$value['meta-name'], 'type' => $user_meta, 'label' => $value['field-title'] );
			}
            elseif ( $value['field'] == 'Textarea' ){
                $merge_tags[] = array( 'name' => $type.'_'.$value['meta-name'], 'type' => $user_meta, 'unescaped' => true, 'label' => $value['field-title'] );
            }
            elseif ( $value['field'] == 'WYSIWYG' ){
                if( $user_meta == 'user_meta' )
                    $wysiwyg_user_meta = 'user_meta_wysiwyg';
                else
                    $wysiwyg_user_meta = $user_meta;

                $merge_tags[] = array( 'name' => $type.'_'.$value['meta-name'], 'type' => $wysiwyg_user_meta, 'unescaped' => true, 'label' => $value['field-title'] );
            }
            elseif( ( $value['field'] == 'Checkbox' || $value['field'] == 'Radio' || $value['field'] == 'Select' || $value['field'] == 'Select (Multiple)' ) && ( $type == 'meta' ) ){
                $merge_tags[] = array( 'name' => $type.'_'.$value['meta-name'], 'type' => $user_meta, 'label' => $value['field-title'] );
                $merge_tags[] = array( 'name' => $type.'_'.$value['meta-name'].'_labels', 'type' => $user_meta.'_labels', 'label' => $value['field-title']. ' Labels' );
            }
            elseif( $value['field'] == 'Map' ) {
                if( $type == 'meta' )
                    $merge_tags[] = array( 'name' => $type . '_' . $value['meta-name'], 'type' => $user_meta . '_map', 'unescaped' => true, 'label' => $value['field-title'] );
            }
            else
				$merge_tags[] = array( 'name' => $type.'_'.$value['meta-name'], 'type' => $user_meta, 'label' => $value['field-title'] );
		}



	$merge_tags[] = array( 'name' => $type.'_role', 'type' => $default_field_type, 'label' => __( 'Role', 'profile-builder' ) );
	$merge_tags[] = array( 'name' => $type.'_role_slug', 'type' => $default_field_type, 'label' => __( 'Role Slug', 'profile-builder' ) );
	$merge_tags[] = array( 'name' => $type.'_registration_date', 'type' => $default_field_type, 'label' => __( 'Registration Date', 'profile-builder' ) );
	$merge_tags[] = array( 'name' => $type.'_number_of_posts', 'type' => $number_of_posts, 'unescaped' => true, 'label' => __( 'Number of Posts', 'profile-builder' ) );
	
	// we can't sort by this fields so only generate the meta
	if( $type == 'meta' ){
		$merge_tags[] = array( 'name' => 'more_info', 'type' => 'more_info', 'unescaped' => true, 'label' => __( 'More Info', 'profile-builder' ) );
		$merge_tags[] = array( 'name' => 'more_info_url', 'type' => 'more_info_url', 'unescaped' => true, 'label' => __( 'More Info Url', 'profile-builder' ) );
		$merge_tags[] = array( 'name' => 'avatar_or_gravatar', 'type' => 'avatar_or_gravatar', 'unescaped' => true, 'label' => __( 'Avatar or Gravatar', 'profile-builder' ) );
		$merge_tags[] = array( 'name' => 'user_id', 'type' => 'user_id', 'label' => __( 'User Id', 'profile-builder' ) );
		$merge_tags[] = array( 'name' => 'user_nicename', 'type' => 'user_nicename', 'unescaped' => true, 'label' => __( 'User Nicename', 'profile-builder' ) );
	}
	
	// for sort tags add unescaped true
	if( !empty( $merge_tags ) ){
		foreach( $merge_tags as $key => $merge_tag ){
			if( $merge_tag['type'] == 'sort_tag' )
				$merge_tags[$key]['unescaped'] = true;
		}
	}

    $merge_tags = apply_filters( 'wppb_userlisting_get_merge_tags', $merge_tags, $type );

	// return only the merge tags that are found inside the template
    if (!empty( $merge_tags ) && !empty( $template )){
        $merge_tags_based_on_template = array();
        foreach ( $merge_tags as $key => $merge_tag ) {
            if ( in_array($merge_tag['name'], $template_tags[0]) ) {
                $merge_tags_based_on_template[] = $merge_tag;
            }
        }
        return $merge_tags_based_on_template;
    }

    return $merge_tags;
}

/**
 * Function that generates the variable array that we give to mustache classes for the multiple user listing
 *
 * @since v.2.0
 *
 * @return array $mustache_vars the array of variable groups and their details
 */
function wppb_generate_mustache_array_for_user_list($userlisting_template = ''){


	$meta_tags = wppb_generate_userlisting_merge_tags( 'meta', $userlisting_template );
	$sort_tags = wppb_generate_userlisting_merge_tags( 'sort', $userlisting_template );

	$mustache_vars = array( 
						array(
							'group-title' => __( 'User Fields Tags', 'profile-builder' ),
							'variables' => array(
												array( 'name' => 'users', 'type' => 'loop_tag', 'children' => $meta_tags  ),
											)
						),
						array(
							'group-title' => __( 'Sort Tags', 'profile-builder' ),
							'variables' => $sort_tags
						),
						array(
							'group-title' => __( 'Extra Functions', 'profile-builder' ),
							'variables' => apply_filters( 'wppb_ul_extra_functions',
                                                array(
    												array( 'name' => 'pagination', 'type' => 'pagination', 'unescaped' => true, 'label' => __( 'Pagination', 'profile-builder' ) ),
    												array( 'name' => 'extra_search_all_fields', 'type' => 'extra_search_all_fields', 'unescaped' => true, 'label' => __( 'Search all Fields', 'profile-builder' ) ),
    												array( 'name' => 'faceted_menus', 'type' => 'faceted_menus', 'unescaped' => true, 'label' => __( 'Faceted Menus', 'profile-builder' ) ),
    												array( 'name' => 'user_count', 'type' => 'user_count', 'unescaped' => true, 'label' => __( 'User Count', 'profile-builder' ) ),
    											)
                                            )
						)
					);
					
	return $mustache_vars;
}

/**
 * Function that generates the variable array that we give to mustache classes for the single user listing
 *
 * @since v.2.0
 *
 * @return array $mustache_vars the array of variable groups and theyr details
 */
function wppb_generate_mustache_array_for_single_user_list(){
	$meta_tags = wppb_generate_userlisting_merge_tags( 'meta' );
	
	$mustache_vars = array(  
						array(
							'group-title' => 'User Fields Tags',
							'variables' => $meta_tags
						),
						array(
							'group-title' => __('Extra Functions', 'profile-builder'),
							'variables' => array(												
												array( 'name' => 'extra_go_back_link', 'type' => 'go_back_link', 'unescaped' => true, 'label' => __( 'Go Back Link', 'profile-builder' ) ),
											)
						)
					);
	return $mustache_vars;
}



/**
 * Function that ads the mustache boxes in the backend for userlisting
 *
 * @since v.2.0
 */
function wppb_userlisting_add_mustache_in_backend(){
	require_once( WPPB_PLUGIN_DIR.'modules/class-mustache-templates/class-mustache-templates.php' );
	
	// initiate box for multiple users listing
	new PB_Mustache_Generate_Admin_Box( 'wppb-ul-templates', __( 'All-userlisting Template', 'profile-builder' ), 'wppb-ul-cpt', 'core', wppb_generate_mustache_array_for_user_list(), wppb_generate_allUserlisting_content() );
	
	// initiate box for single user listing
	new PB_Mustache_Generate_Admin_Box( 'wppb-single-ul-templates', __( 'Single-userlisting Template', 'profile-builder' ), 'wppb-ul-cpt', 'core', wppb_generate_mustache_array_for_single_user_list(), wppb_generate_singleUserlisting_content() );
}
add_action( 'init', 'wppb_userlisting_add_mustache_in_backend' );

/**
 * Function that generates the default template for all user listing
 *
 * @since v.2.0
 * 
 */
function wppb_generate_allUserlisting_content(){
return '
<table class="wppb-table">
	<thead>
		<tr>
		  <th scope="col" colspan="2" class="wppb-sorting">{{{sort_user_name}}}</th>
		  <th scope="col" class="wppb-sorting">{{{sort_first_name}}}</th>
		  <th scope="col" class="wppb-sorting">{{{sort_role}}}</th>
		  <th scope="col" class="wppb-sorting">{{{sort_number_of_posts}}}</th>
		  <th scope="col" class="wppb-sorting">{{{sort_registration_date}}}</th>
		  <th scope="col">More</th>
		</tr>
	</thead>	
	<tbody>
		{{#users}}
		<tr>
		  <td data-label="' . __( 'Avatar', 'profile-builder' ) . '" class="wppb-avatar">{{{avatar_or_gravatar}}}</td>
		  <td data-label="' . __( 'Username', 'profile-builder' ) . '" class="wppb-login">{{meta_user_name}}</td>
		  <td data-label="' . __( 'Firstname', 'profile-builder' ) . '" class="wppb-name">{{meta_first_name}} {{meta_last_name}}</td>
		  <td data-label="' . __( 'Role', 'profile-builder' ) . '" class="wppb-role">{{meta_role}}</td>
		  <td data-label="' . __( 'Posts', 'profile-builder' ) . '" class="wppb-posts">{{{meta_number_of_posts}}}</td>
		  <td data-label="' . __( 'Sign-up Date', 'profile-builder' ) . '" class="wppb-signup">{{meta_registration_date}}</td>
		  <td data-label="' . __( 'More', 'profile-builder' ) . '" class="wppb-moreinfo">{{{more_info}}}</td>
		</tr>
		{{/users}}
	</tbody>
</table>
{{{pagination}}}';
}

/**
 * Function that generates the default template for single user listing
 *
 * @since v.2.0
 * 
 */
function wppb_generate_singleUserlisting_content(){
	return '
{{{extra_go_back_link}}}
<ul class="wppb-profile">
  <li>
    <h3>Name</h3>
  </li>
  <li class="wppb-avatar">
    {{{avatar_or_gravatar}}}
  </li>
  <li>
    <label>Username:</label>
    <span>{{meta_user_name}}</span>
  </li>
  <li>
    <label>First Name:</label>
    <span>{{meta_first_name}}</span>
  </li>
  <li>
    <label>Last Name:</label>
    <span>{{meta_last_name}}</span>
  </li>
  <li>
    <label>Nickname:</label>
    <span>{{meta_nickname}}</span>
  </li>
  <li>
    <label>Display name:</label>
	<span>{{meta_display_name}}</span>
  </li>
  <li>
    <h3>Contact Info</h3>
  </li>
  <li>
  	<label>Website:</label>
	<span>{{meta_website}}</span>
  </li>
  <li>
    <h3>About Yourself</h3>
  </li>
  <li>
	<label>Biographical Info:</label>
	<span>{{{meta_biographical_info}}}</span>
  </li>
</ul>
{{{extra_go_back_link}}}';
}


/**
 * Function that handles the userlisting shortcode
 *
 * @since v.2.0
 *
 * @param array $atts the shortcode attributs
 * @return the shortcode output
 */
function wppb_user_listing_shortcode( $atts ){
	global $roles;
    global $wppb_userlisting_shortcode;
    $wppb_userlisting_shortcode = true;

	//get value set in the shortcode as parameter, default to "public" if not set
	extract( shortcode_atts( array('meta_key' => '', 'meta_value' => '', 'name' => 'userlisting', 'include' => '', 'exclude' => '', 'single' => false, 'id' => '' ), $atts, 'wppb-list-users' ) );

    // so we can have [wppb-list-users single] without a value for single. Also works with value for single.
    if( !empty($atts) ) {
        foreach ($atts as $key => $value) {
            if ($value === 'single' && is_int($key)) $single = true;
        }
    }

	$userlisting_posts = get_posts( array( 'posts_per_page' => -1, 'post_status' =>'publish', 'post_type' => 'wppb-ul-cpt', 'orderby' => 'post_date', 'order' => 'ASC' ) );
	foreach ( $userlisting_posts as $key => $value ){
		if ( trim( Wordpress_Creation_Kit_PB::wck_generate_slug( $value->post_title ) ) == $name ){

            /* check here the visibility and roles for which to display the userlisting */
            $userlisting_args = get_post_meta( $value->ID, 'wppb_ul_page_settings', true );
            if( !empty( $userlisting_args[0]['visible-only-to-logged-in-users'] ) && $userlisting_args[0]['visible-only-to-logged-in-users'] == 'yes' ){
                if( !is_user_logged_in() )
                    return apply_filters( 'wppb_userlisting_no_permission_to_view', '<p>'. __( 'You do not have permission to view this user list.', 'profile-builder' ) .'</p>' );

                if( !empty( $userlisting_args[0]['visible-to-following-roles'] ) ){
                    if( strpos( $userlisting_args[0]['visible-to-following-roles'], '*' ) === false ){
                        $current_user = wp_get_current_user();
                        $roles = $current_user->roles;
                        if( empty( $roles ) )
                            $roles = array();

                        $visibility_for_roles = explode( ', ',$userlisting_args[0]['visible-to-following-roles'] );
                        $check_intersect_roles = array_intersect( $visibility_for_roles, $roles );

                        if( empty( $check_intersect_roles ) )
                            return apply_filters( 'wppb_userlisting_no_role_to_view', '<p>'. __( 'You do not have the required user role to view this user list.', 'profile-builder' ) .'</p>' );
                    }
                }
            }

			$userID = wppb_get_query_var( 'username' );

            // generate a single user template if "single" shortcode argument is set.
            if ( $single !== false ){
                if ( is_numeric( $id ) ){
                    $userID = $id;
                } else {
                    $userID = get_current_user_id();
                }
                $single = true;
            }

			if( !empty( $userID ) ){
                $user_object = new WP_User( $userID );
                $list_display_roles = explode( ', ', $userlisting_args[0]["roles-to-display"] );
                $role_present = array_intersect( $list_display_roles, $user_object->roles );

                $single_user_queryvar = wppb_get_query_var( 'username' );
                if( ( !empty( $exclude ) && in_array( $userID, wp_parse_id_list( $exclude ) ) ) || ( !empty( $include ) && !in_array( $userID, wp_parse_id_list( $include ) ) ) || ( !in_array( '*', $list_display_roles ) && empty( $role_present ) ) || (!empty( $single_user_queryvar ) && $single ) ) {
                    return __( 'User not found', 'profile-builder' );
                }
                else {
                    $single_userlisting_template = get_post_meta( $value->ID, 'wppb-single-ul-templates', true );
                    if( empty( $single_userlisting_template ) )
                        $single_userlisting_template = wppb_generate_singleUserlisting_content();
                    return apply_filters( 'wppb_single_userlisting_template', (string) new PB_Mustache_Generate_Template( wppb_generate_mustache_array_for_single_user_list(), $single_userlisting_template, array( 'userlisting_form_id' => $value->ID, 'meta_key' => $meta_key, 'meta_value' => $meta_value, 'include' => $include, 'exclude' => $exclude, 'user_id' => $userID, 'single' => true ) ), $userID );
                }
            }elseif( $single == true){
                // don't show anything for non-logged in users.
                return;
            }else{
                $userlisting_template = get_post_meta( $value->ID, 'wppb-ul-templates', true );
                if( empty( $userlisting_template ) )
                    $userlisting_template = wppb_generate_allUserlisting_content();
				return apply_filters( 'wppb_all_userlisting_template', '<div class="wppb-userlisting-container">'.(string) new PB_Mustache_Generate_Template( wppb_generate_mustache_array_for_user_list($userlisting_template), $userlisting_template, array( 'userlisting_form_id' => $value->ID, 'meta_key' => $meta_key, 'meta_value' => $meta_value, 'include' => $include, 'exclude' => $exclude, 'single' => false ) ) . '</div>' ) ;
            }
		}
	}
}



/**
 * Function that returns the meta-values for the default fields
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return the value for the meta-field
 */
function wppb_userlisting_show_default_user_fields( $value, $name, $children, $extra_info ){
	$userID = wppb_get_query_var( 'username' );

    if( !empty( $extra_info['user_id'] ) )
		$user_id = $extra_info['user_id'];
	else
		$user_id = '';
	
	if( empty( $userID ) )
		$user_info = get_userdata($user_id);
	else
		$user_info = get_userdata($userID);

    $returned_value = '';
	if( $name == 'meta_user_name' ){
        $wppb_general_settings = get_option( 'wppb_general_settings' );
        if( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) )
            $returned_value = apply_filters('wppb_userlisting_extra_meta_email', $user_info->user_email, new WP_User( $user_info->ID ) );
        else
		    $returned_value = apply_filters('wppb_userlisting_extra_meta_user_name', $user_info->user_login, new WP_User( $user_info->ID ) );
    }
    else if( $name == 'meta_email' )
        $returned_value = apply_filters('wppb_userlisting_extra_meta_email', $user_info->user_email, new WP_User( $user_info->ID ) );
	else if( $name == 'meta_display_name' )
		$returned_value = $user_info->display_name;
	else if( $name == 'meta_first_name' )
		$returned_value = $user_info->user_firstname;
	else if( $name == 'meta_last_name' )
		$returned_value = $user_info->user_lastname;
	else if( $name == 'meta_nickname' )
		$returned_value = $user_info->nickname;
	else if( $name == 'meta_website' )
		$returned_value = $user_info->user_url;
    else if( $name == 'meta_biographical_info' )
        $returned_value = apply_filters('wppb_userlisting_autop_biographical_info', wpautop($user_info->description), $user_info->description);
    else if ( $name == 'meta_blog_url' ){
            $returned_value = wppb_get_blog_url_of_user_id( $user_info->ID, false );
    }
	else if( $name == 'meta_role' ){
        if( !empty( $user_info->roles ) ){
			include_once(ABSPATH . 'wp-admin/includes/user.php');
			$editable_roles = array_keys( get_editable_roles() );
			if ( count( $user_info->roles ) <= 1 ) {
				$role = reset( $user_info->roles );
			} elseif ( $roles = array_intersect( array_values( $user_info->roles ), $editable_roles ) ) {
				$role = reset( $roles );
			} else {
				$role = reset( $user_info->roles );
			}
			$WP_Roles = new WP_Roles();
			$role_name = isset( $WP_Roles->role_names[$role] ) ? translate_user_role( $WP_Roles->role_names[$role] ) : __( 'None' );

			$returned_value = apply_filters('wppb_userlisting_extra_meta_role', $role_name, $user_info );
        }

	}
    else if( $name == 'meta_role_slug' ){
        if( !empty( $user_info->roles ) ){
            include_once(ABSPATH . 'wp-admin/includes/user.php');
            $editable_roles = array_keys( get_editable_roles() );
            if ( count( $user_info->roles ) <= 1 ) {
                $role = reset( $user_info->roles );
            } elseif ( $roles = array_intersect( array_values( $user_info->roles ), $editable_roles ) ) {
                $role = reset( $roles );
            } else {
                $role = reset( $user_info->roles );
            }
            $WP_Roles = new WP_Roles();
            $role_slug = isset( $WP_Roles->roles[$role] ) ? $role : __( 'None' );

            $returned_value = apply_filters('wppb_userlisting_extra_meta_role_slug', $role_slug, $user_info );
        }

    }
	else if( $name == 'meta_registration_date' ){
        $register_timestamp = strtotime( $user_info->user_registered );
        /* convert to local timezone as date */
        $time = date_i18n( 'Y-m-d', wppb_add_gmt_offset( $register_timestamp ) );
		$returned_value = apply_filters('wppb_userlisting_extra_meta_registration_date', $time, $user_info );
	}

    return apply_filters('wppb_userlisting_default_user_field_value', $returned_value, $name, $userID );
}
add_filter( 'mustache_variable_default_user_field', 'wppb_userlisting_show_default_user_fields', 10, 4 );



/**
 * Function that returns the number of posts related to each user
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return the value for the meta-field
 */
function wppb_userlisting_show_number_of_posts( $value, $name, $children, $extra_info ){
	$userID = wppb_get_query_var( 'username' );
	
	$user_id = ( !empty( $extra_info['user_id'] ) ? $extra_info['user_id'] : '' );
	$user_info = ( empty( $userID ) ? get_userdata( $user_id ) : get_userdata( $userID ) );
	
	$allPosts = get_posts( array( 'author'=> $user_info->ID, 'numberposts'=> -1 ) );
	$number_of_posts = count( $allPosts );
		
	return apply_filters('wppb_userlisting_extra_meta_number_of_posts', '<a href="'.get_author_posts_url($user_info->ID).'" id="postNumberLink" class="postNumberLink">'.$number_of_posts.'</a>', $user_info, $number_of_posts);
}
add_filter( 'mustache_variable_number_of_posts', 'wppb_userlisting_show_number_of_posts', 10, 4 );



/**
 * Function that returns the meta-value for the respectiv meta-field
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return the value for the meta-field
 */
function wppb_userlisting_show_user_meta( $value, $name, $children, $extra_info ){
	$userID = wppb_get_query_var( 'username' );
	
	$user_id = ( !empty( $extra_info['user_id'] ) ? $extra_info['user_id'] : '' );
	
	if( empty( $userID ) )	
		$userID = $user_id;
	
	// strip first meta_ from $name
	$name = preg_replace('/meta_/', '', $name, 1);
	$value = get_user_meta( $userID, $name, true );
	return apply_filters('wppb_userlisting_user_meta_value', $value, $name, $userID);

}
add_filter( 'mustache_variable_user_meta', 'wppb_userlisting_show_user_meta', 10, 4 );

function wppb_userlisting_show_user_meta_wysiwyg( $value, $name, $children, $extra_info ){
    $value = do_shortcode( wppb_userlisting_show_user_meta( $value, $name, $children, $extra_info ) );
    $wpautop = apply_filters( 'wppb_userlisting_wysiwyg_wpautop', true );
    if( $wpautop )
        return wpautop( $value );
    else
        return $value;
}
add_filter( 'mustache_variable_user_meta_wysiwyg', 'wppb_userlisting_show_user_meta_wysiwyg', 10, 4 );

/* select, checkbox and radio can have their labels displayed */
function wppb_userlisting_show_user_meta_labels( $value, $name, $children, $extra_info ){
    $userID = wppb_get_query_var( 'username' );

    $user_id = ( !empty( $extra_info['user_id'] ) ? $extra_info['user_id'] : '' );

    if( empty( $userID ) )
        $userID = $user_id;

    // strip first meta_ from $name
    $name = preg_replace( '/meta_/', '', $name, 1 );
    $name = preg_replace( '/_labels$/', '', $name, 1 );

    $value = get_user_meta( $userID, $name, true );
    /* get manage fields */
    global $wppb_manage_fields;
    if( !isset( $wppb_manage_fields ) )
        $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

    $wppb_manage_fields = apply_filters( 'wppb_form_fields', $wppb_manage_fields, array( 'user_id' => $userID, 'context' => 'mustache_variable' ) );
    if( !empty( $wppb_manage_fields ) ) {
        foreach ($wppb_manage_fields as $field) {
            if( $field['meta-name'] == $name ){
                /* get label corresponding to value. the values and labels in the backend settings are comma separated so we assume that as well here ? */
                $saved_values = array_map( 'trim', explode( ',', $value ) );
                $field['options'] = array_map( 'trim', explode( ',', $field['options'] ) );
                $field['labels'] = array_map( 'trim', explode( ',', $field['labels'] ) );
                /* get the position for each value */
                $key_array = array();
                if( !empty( $field['options'] ) ){
                    foreach( $field['options'] as $key => $option ){
                        if( in_array( $option, $saved_values ) )
                            $key_array[] = $key;
                    }
                }

                $show_values = array();
                if( !empty( $key_array ) ){
                    foreach( $key_array as $key ){
                        if( !empty( $field['labels'][$key] ) )
                            $show_values[] = $field['labels'][$key];
                        else
                            $show_values[] = $field['options'][$key];
                    }
                }

                return apply_filters( 'wppb_userlisting_user_meta_value_label', implode( ', ', $show_values ), $name, $show_values, $userID );
            }
        }
    }
}
add_filter( 'mustache_variable_user_meta_labels', 'wppb_userlisting_show_user_meta_labels', 10, 4 );

function wppb_modify_userlisting_user_meta_value($value, $name, $userID = ''){
    global $wppb_manage_fields;
    if( !isset( $wppb_manage_fields ) )
        $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

    $wppb_manage_fields = apply_filters( 'wppb_form_fields', $wppb_manage_fields, array( 'user_id' => $userID, 'context' => 'mustache_variable' ) );
    if( !empty( $wppb_manage_fields ) ){
        foreach ($wppb_manage_fields as $field){
            if ( ($field['field'] == 'Textarea')&& ($field['meta-name'] == $name)) {
                return wpautop($value);
            }
            if( ( $field['field'] == 'Avatar' || $field['field'] == 'Upload' ) && $field['meta-name'] == $name ){
                if( is_numeric($value) ){
                    $img_attr = wp_get_attachment_url( $value );
                    if( !empty( $img_attr ) )
                        return $img_attr;
                }
                else
                    return $value;
            }
            if( $field['field'] == 'Select (Country)' && $field['meta-name'] == $name ) {
                $country_array = wppb_country_select_options( 'front_end' );

                if( ! empty( $country_array[$value] ) )
                    return $country_array[$value];
            }
            if( $field['field'] == 'Select (Currency)' && $field['meta-name'] == $name ) {
                $currency_array = wppb_get_currencies();

                if( ! empty( $currency_array[$value] ) ) {
                    $currency_symbol = wppb_get_currency_symbol( $value );
                    return $currency_array[$value] . ( !empty( $field['show-currency-symbol'] ) && $field['show-currency-symbol'] == 'Yes' && !empty($currency_symbol) ? ' (' . html_entity_decode($currency_symbol) . ')' : '' ) ;
                }
            }

            if( $field['field'] == 'Timepicker' && $field['meta-name'] == $name ) {

                if( !empty( $field['time-format'] ) && $field['time-format'] == '12' ) {

                    if( strpos( $value, ':' ) !== false ) {
                        $time = explode( ':', $value );

                        $hour    = $time[0];
                        $minutes = $time[1];

                        if ($hour > 12) {
                            $hour -= 12;
                            $value = (strlen($hour) == 1 ? '0' . $hour : $hour) . ':' . $minutes . ' pm';
                        } elseif( $hour == 12 )
                            $value = $hour . ':' . $minutes . ' pm';
                        elseif( $hour == '00' )
                            $value = '12' . ':' . $minutes . ' am';
                        else
                            $value = $hour . ':' . $minutes . ' am';

                        return $value;

                    }

                }
            }
            if( ( $field['field'] == 'Checkbox' || $field['field'] == 'Select (Multiple)' ) && $field['meta-name'] == $name ) {
                $value = implode( ', ', explode( ',', $value ) );
                return $value;
            }
        }
    }
    return $value;
}
add_filter('wppb_userlisting_user_meta_value', 'wppb_modify_userlisting_user_meta_value', 10, 3);

/**
 * Function that creates the sort-link for the various fields
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return sort-link
 */
function wppb_userlisting_sort_tags( $value, $name, $children, $extra_info ){

	if ( $name == 'sort_user_name' )
        return '<a href="'.wppb_get_new_url( 'login', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'login' ) . '" id="sortLink1">'.apply_filters( 'sort_user_name_filter', __( 'Username', 'profile-builder' ) ).'</a>';
	
	elseif ($name == 'sort_first_last_name')
		return apply_filters( 'sort_first_last_name_filter', __( 'First/Lastname', 'profile-builder' ) );
		
	elseif ( $name == 'sort_email' )
        return '<a href="'.wppb_get_new_url( 'email', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'email' ) . '" id="sortLink2">'.apply_filters( 'sort_email_filter', __( 'Email', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_registration_date' )
        return '<a href="'.wppb_get_new_url( 'registered', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'registered' ) . '" id="sortLink3">'.apply_filters( 'sort_registration_date_filter', __( 'Sign-up Date', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_first_name' )
        return '<a href="'.wppb_get_new_url( 'firstname', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'firstname' ) . '" id="sortLink4">'.apply_filters( 'sort_first_name_filter', __( 'Firstname', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_last_name' )
        return '<a href="'.wppb_get_new_url( 'lastname', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'lastname' ) . '" id="sortLink5">'.apply_filters( 'sort_last_name_filter', __( 'Lastname', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_display_name' )		
        return '<a href="'.wppb_get_new_url( 'nicename', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'nicename' ) . '" id="sortLink6">'.apply_filters( 'sort_display_name_filter', __( 'Display Name', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_website' )
		return '<a href="'.wppb_get_new_url( 'url', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'website' ) . '" id="sortLink7">'.apply_filters('sort_website_filter', __( 'Website', 'profile-builder' ) ).'</a>';
	
	elseif ( $name == 'sort_biographical_info' )
        return '<a href="'.wppb_get_new_url( 'bio', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'bio' ) . '" id="sortLink8">'.apply_filters( 'sort_biographical_info_filter', __( 'Biographical Info', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_number_of_posts' )
        return '<a href="'.wppb_get_new_url( 'post_count', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'post_count' ) . '" id="sortLink9">'.apply_filters( 'sort_number_of_posts_filter', __( 'Posts', 'profile-builder' ) ).'</a>';
		
	elseif ( $name == 'sort_aim' )
        return '<a href="'.wppb_get_new_url( 'aim', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'aim' ) . '" id="sortLink10">'.apply_filters( 'sort_aim_filter', __( 'Aim', 'profile-builder' ) ).'</a>';
	
	elseif ( $name == 'sort_yim' )
        return '<a href="'.wppb_get_new_url( 'yim', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'yim' ) . '" id="sortLink11">'.apply_filters( 'sort_yim_filter', __( 'Yim', 'profile-builder' ) ).'</a>';
	
	elseif ( $name == 'sort_jabber' )
        return '<a href="'.wppb_get_new_url( 'jabber', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'jabber' ) . '" id="sortLink12">'.apply_filters( 'sort_jabber_filter', __( 'Jabber', 'profile-builder' ) ).'</a>';

    elseif ( $name == 'sort_nickname' )
        return '<a href="'.wppb_get_new_url( 'nickname', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'nickname' ) . '" id="sortLink13">'.apply_filters( 'sort_nickname_filter', __( 'Nickname', 'profile-builder' ) ).'</a>';

    elseif ( $name == 'sort_role' )
        return '<a href="'.wppb_get_new_url( 'role', $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( 'role' ) . '" id="sortLink14">'.apply_filters( 'sort_role_filter', __( 'Role', 'profile-builder' ) ).'</a>';

    else{
        global $wppb_manage_fields;
        if( !isset( $wppb_manage_fields ) )
            $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

		$wppb_manage_fields = apply_filters( 'wppb_sort_change_form_fields', $wppb_manage_fields );
		
		if ( $wppb_manage_fields != 'not_found' ){		
			$i = 14;
			
			foreach( $wppb_manage_fields as $key => $field_value ){
				if ( $name == 'sort_'.$field_value['meta-name'] ){
					$i++;
				
					return '<a href="'.wppb_get_new_url( $field_value['meta-name'], $extra_info ).'" class="sortLink ' . wppb_get_sorting_class( $field_value['meta-name'] ) . '" id="sortLink'.$i.'">'.$field_value['field-title'].'</a>';
				}
			}
		}
	}

    return $value;
	
}
add_filter( 'mustache_variable_sort_tag', 'wppb_userlisting_sort_tags', 10, 4 );



/**
 * Function that handles the user queryes for display and facets
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return sort-link
 */
function wppb_userlisting_users_loop( $value, $name, $children, $extra_values ){
	if( $name == 'users' ){
        global $userlisting_args;
        global $wpdb;
		$userlisting_form_id = $extra_values['userlisting_form_id'];		
		$userlisting_args = get_post_meta( $userlisting_form_id, 'wppb_ul_page_settings', true );

        if( !empty( $userlisting_args[0] ) ){
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
            if( !is_int( (int)$userlisting_args[0]['number-of-userspage'] ) || (int)$userlisting_args[0]['number-of-userspage'] == 0 )
                $userlisting_args[0]['number-of-userspage'] = 5;

            // Check if some of the listing parameters have changed
            if ( isset( $_REQUEST['setSortingOrder'] ) && ( trim( $_REQUEST['setSortingOrder'] ) !== '' ) )
                $sorting_order = sanitize_text_field( $_REQUEST['setSortingOrder'] );
            else
                $sorting_order = $userlisting_args[0]['default-sorting-order'];

            /* if we have admin approval on we don't want to show those users in the userlisting so we need to exclude them */
            $wppb_generalSettings = get_option( 'wppb_general_settings' );
            if( isset( $wppb_generalSettings['adminApproval'] ) && ( $wppb_generalSettings['adminApproval'] == 'yes' ) ){
                $excluded_ids = array();
                $user_statusTaxID = get_term_by( 'name', 'unapproved', 'user_status' );
                if( $user_statusTaxID != false ){
                    $term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;
                    $results = $wpdb->get_results( $wpdb->prepare( "SELECT wppb_t1.ID FROM $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->term_relationships AS wppb_t0 ON wppb_t1.ID = wppb_t0.object_id WHERE wppb_t0.term_taxonomy_id = %d", $term_taxonomy_id ) );

                    foreach ( $results as $result )
                        array_push( $excluded_ids, $result->ID );

                    $excluded_ids = implode( ',', $excluded_ids );
                }
            }
            if( !empty($excluded_ids) )
                $extra_values['exclude'] .= ','. $excluded_ids;
			//set query args
			$args = array(
				'order'					        => $sorting_order,
                'include'                       => $extra_values['include'],
                'exclude'                       => $extra_values['exclude'],
                'fields'                        => array( 'ID' )
			);

            /* get all field options here, we will need it bellow */
            global $wppb_manage_fields;
            if( !isset( $wppb_manage_fields ) )
                $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

            // Check if some of the listing parameters have changed
            if ( isset( $_REQUEST['setSortingCriteria'] ) && ( trim( $_REQUEST['setSortingCriteria'] ) !== '' ) )
                $sorting_criteria = sanitize_text_field( $_REQUEST['setSortingCriteria'] );
            else
                $sorting_criteria = $userlisting_args[0]['default-sorting-criteria'];

            if( in_array( $sorting_criteria, array( 'login', 'email', 'url', 'registered', 'post_count', 'nicename' ) ) ){
                if( $sorting_criteria == 'nicename' )
                    $args['orderby']  = 'display_name';
                else
                    $args['orderby']  = $sorting_criteria;
            }
            else{

                $args['orderby']  = apply_filters( 'wppb_ul_sorting_type', 'meta_value', $sorting_criteria );

                if ($wppb_manage_fields != 'not_found') {
                    foreach ($wppb_manage_fields as $wppb_field) {
                        if( $wppb_field['meta-name'] == $sorting_criteria ){
                            if( $wppb_field['field'] == 'Number' || $wppb_field['field'] == 'Phone' ){
                                $args['orderby']  = apply_filters( 'wppb_ul_sorting_type', 'meta_value_num', $sorting_criteria );
                            }
                        }
                    }
                }

                switch( $sorting_criteria ){
                    case "bio":
                        $args['meta_key'] = 'description';
                        break;
                    case "firstname":
                        $args['meta_key'] = 'first_name';
                        break;
                    case "lastname":
                        $args['meta_key'] = 'last_name';
                        break;
                    case "nickname":
                        $args['meta_key'] = 'nickname';
                        break;
                    case "role":
                        $args['meta_key'] = $wpdb->get_blog_prefix().'capabilities';
                        break;
                    case "RAND()":
                        break;
                    default:
                        $args['meta_key']  = $sorting_criteria;
                }
            }

            /* the relationship between meta query is AND because we need to narrow the result  */
            $args['meta_query'] = array('relation' => 'AND');

            /* we check if we have a meta_value and meta_key in the shortcode and add a meta query */
            if( !empty( $extra_values['meta_value'] ) && !empty( $extra_values['meta_key'] ) ){
                $args['meta_query'][0] = array( 'relation' => 'AND' ); //insert relation here
                $args['meta_query'][0][] = array(
                    'key' => $extra_values['meta_key'],
                    'value' => $extra_values['meta_value'],
                    'compare' => '='
                );
            }


            /* add facet meta query here */
            $all_user_listing_template = get_post_meta( $userlisting_form_id, 'wppb-ul-templates', true );
            if( !empty( $all_user_listing_template ) && strpos( $all_user_listing_template, '{{{faceted_menus}}}' ) !== false ) {
                $faceted_settings = get_post_meta( $userlisting_form_id, 'wppb_ul_faceted_settings', true );
                if( !empty( $faceted_settings ) ){
					if( empty( $args['meta_query'][0] ) )
                    	$args['meta_query'][0] = array( 'relation' => 'AND' );

                    foreach( $faceted_settings as $faceted_setting ){
                        if( isset( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ) ){
                            if( $faceted_setting['facet-type'] == 'range' ){
                                $args['meta_query'][0][$faceted_setting['facet-meta']] = array(
                                    'key' => $faceted_setting['facet-meta'],
                                    'value' => explode( '-', sanitize_text_field( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ) ),
                                    'compare' => 'BETWEEN',
                                    'type'    => 'NUMERIC'
                                );
                            }
                            else if( $faceted_setting['facet-type'] == 'search' ){
                                $args['meta_query'][0][$faceted_setting['facet-meta']] = array(
                                    'key' => $faceted_setting['facet-meta'],
                                    'value' => sanitize_text_field( stripslashes( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ) ),
                                    'compare' => 'LIKE'
                                );
                            }
                            else if( $faceted_setting['facet-behaviour'] == 'narrow' ){

                                /* for fields types that have multiple values (checkbox..) we check for the options in the fields settings and not what is stored in the database  */
                                if( wppb_check_if_field_is_multiple_value_from_meta_name( $faceted_setting['facet-meta'], $wppb_manage_fields ) ){
                                    $compare = 'REGEXP';
                                    $val = '('.preg_quote( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ).'$)|('. preg_quote( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ).',)';
                                }
                                else{
                                    /* handle roles facet differently */
                                    if( $wpdb->get_blog_prefix().'capabilities' == $faceted_setting['facet-meta'] ) {
                                        $compare = 'LIKE';
                                        $val = '"'. sanitize_text_field( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ) . '"';
                                    }
                                    else{
                                        $compare = '=';
                                        $val = sanitize_text_field( $_GET['ul_filter_'.$faceted_setting['facet-meta']] );
                                    }
                                }

                                $args['meta_query'][0][$faceted_setting['facet-meta']] = array(
                                    'key' => $faceted_setting['facet-meta'],
                                    'value' => $val,
                                    'compare' => $compare
                                );
                            }
                            else if( $faceted_setting['facet-behaviour'] == 'expand' ){

                                $values = explode( '||', sanitize_text_field( $_GET['ul_filter_'.$faceted_setting['facet-meta']] ) );
                                if( !empty( $values ) ) {
                                    /* for fields types that have multiple values (checkbox..) we check for the options in the fields settings and not what is stored in the database  */
                                    /* we need a new nested meta query for this */
                                    if( wppb_check_if_field_is_multiple_value_from_meta_name( $faceted_setting['facet-meta'], $wppb_manage_fields ) ) {
                                        $args['meta_query'][0][$faceted_setting['facet-meta']] = array('relation' => 'OR');
                                        foreach ($values as $key => $val) {
                                            $args['meta_query'][0][$faceted_setting['facet-meta']][] = array(
                                                'key' => $faceted_setting['facet-meta'],
                                                'value' => '(' . preg_quote( $val ) . '$)|(' . preg_quote( $val ) . ',)',
                                                'compare' => 'REGEXP'
                                            );
                                        }
                                    }/* handle roles facet differently */
                                    else if( $wpdb->get_blog_prefix().'capabilities' == $faceted_setting['facet-meta'] ){
                                        $args['meta_query'][0][$faceted_setting['facet-meta']] = array('relation' => 'OR');
                                        foreach ($values as $key => $val) {
                                            $args['meta_query'][0][$faceted_setting['facet-meta']][] = array(
                                                'key' => $faceted_setting['facet-meta'],
                                                'value' => '"'.$val.'"',
                                                'compare' => 'LIKE'
                                            );
                                        }
                                    }
                                    else{
                                        $args['meta_query'][0][$faceted_setting['facet-meta']] = array(
                                            'key' => $faceted_setting['facet-meta'],
                                            'value' => $values,
                                            'compare' => 'IN'
                                        );
                                    }
                                }


                            }
                        }
                    }
                }
            }

            /* handle the roles to display setting  it need to be before search*/
            if( !empty( $userlisting_args[0]['roles-to-display'] ) )
                $roles = explode( ', ', $userlisting_args[0]['roles-to-display'] );
            if( empty( $roles[0] ) || in_array( '*', $roles ) )
                $roles = array();

			if( !empty( $roles ) ){
				$args['meta_query'][1] = array('relation' => 'OR');
				foreach ($roles as $role) {
					$args['meta_query'][1][] = array(
						'key' => $wpdb->get_blog_prefix().'capabilities',
						'value' => '"'.$role.'"',
						'compare' => 'LIKE'
					);
				}
			}

            /* set the search here, we have a combination with search arg for columns in user table and meta query for user_meta table */
			if ( isset( $_REQUEST['searchFor'] ) ) {
                $search_for = sanitize_text_field( $_REQUEST['searchFor'] );
                //was a valid string enterd in the search form?
                $searchText = apply_filters('wppb_userlisting_search_field_text', __('Search Users by All Fields', 'profile-builder'));
                if (trim($search_for) !== $searchText){
                    $args['search'] = '*' . $search_for . '*';

                    /* filter used to exclude fields from search */
                    $wppb_exclude_search_fields = apply_filters('wppb_exclude_search_fields', array(), $userlisting_form_id );

                    $args['search_columns'] = array('ID', 'user_login', 'user_email', 'user_url', 'user_nicename' );
                    foreach( $args['search_columns'] as $key => $search_column ){
                        if( in_array( $search_column, $wppb_exclude_search_fields ) ){
                            unset( $args['search_columns'][$key] );
                        }
                    }

                    /* the meta query relationship in the search is or because we need all the results */
                    $args['meta_query'][2] = array('relation' => 'OR');
                    $user_meta_keys = array('first_name', 'last_name', 'nickname', 'description', $wpdb->get_blog_prefix().'capabilities');

                    if ($wppb_manage_fields != 'not_found') {
                        foreach ($wppb_manage_fields as $wppb_manage_field) {
                            $user_meta_keys[] = $wppb_manage_field['meta-name'];
                        }
                        $user_meta_keys = apply_filters( 'wppb_userlisting_search_in_user_meta_keys', $user_meta_keys, $wppb_manage_fields, $wppb_exclude_search_fields, $searchText, $args );
                    }

                    foreach ($user_meta_keys as $user_meta_key) {
                        if( !in_array($user_meta_key, $wppb_exclude_search_fields ) ) {
                            $args['meta_query'][2][] = array(
                                'key' => $user_meta_key,
                                'value' => apply_filters( 'wppb_ul_search_all_meta_value', stripslashes($search_for) ),
                                'compare' => apply_filters( 'wppb_ul_search_all_meta_compare', 'LIKE' )
                            );
                        }
                    }
                }
			}



			$args = apply_filters( 'wppb_userlisting_user_query_args', $args );

            global $totalUsers;

			//query users
            //echo microtime(true).'<br/>';
            /* check if we have faceted menus, if we have we need to query for all users so we can have dynamic facet values */
            $all_user_listing_template = get_post_meta( $userlisting_form_id, 'wppb-ul-templates', true );
            if( strpos( $all_user_listing_template, '{{{faceted_menus}}}' ) !== false ) {
                $args['count_total'] = false;
                $wp_all_user = new WP_User_Query($args);
                $all_user_ids = $wp_all_user->get_results();
                $totalUsers = count( $all_user_ids );
                $all_user_ids_array = array();
                if( !empty($all_user_ids) ){
                    foreach( $all_user_ids as $all_user_id ){
                        $all_user_ids_array[] = $all_user_id->ID;
                    }
                }
                global $all_queried_user_ids_string;
                $all_queried_user_ids_string = implode(',', $all_user_ids_array );

                $faceted_settings = get_post_meta( $userlisting_form_id, 'wppb_ul_faceted_settings', true );
                if( !empty( $faceted_settings ) ) {
                    foreach ($faceted_settings as $faceted_setting) {
                        if (isset($_GET['ul_filter_' . $faceted_setting['facet-meta']])){
                            $args_temp = $args;
                            unset( $args_temp['meta_query'][0][$faceted_setting['facet-meta']] );
                            $wp_users = new WP_User_Query($args_temp);
                            $user_ids = $wp_users->get_results();
                            $user_ids_array = array();
                            if( !empty($user_ids) ){
                                foreach( $user_ids as $user_id ){
                                    $user_ids_array[] = $user_id->ID;
                                }
                            }

                            $gloabl_filter_ids_name = $faceted_setting['facet-meta'].'_user_ids';
                            global ${$gloabl_filter_ids_name};
                            $$gloabl_filter_ids_name = implode(',', $user_ids_array );
                        }
                    }
                }


            }

            $args['number']	= (int)$userlisting_args[0]['number-of-userspage'];
            $args['paged']  = $paged;
            $wp_user_search = new WP_User_Query( $args );

            //echo microtime(true);
			
			$thisPageOnly = $wp_user_search->get_results();

            if( empty( $totalUsers ) )
			    $totalUsers = $wp_user_search->get_total();

			$children_vals = array();

			if( !empty( $thisPageOnly ) ){
				$i = 0;
				foreach( $thisPageOnly as $user ){
					foreach( $children as $child ){

						$children_vals[$i][ $child['name'] ] = apply_filters( 'mustache_variable_'. $child['type'], '', $child['name'], empty( $child['children']) ? array() : $child['children'], array( 'user_id' => $user->ID, 'userlisting_form_id' => $userlisting_form_id ) );
					}
					$i++;
				}
			}

			return $children_vals;
		}
	}
}
add_filter( 'mustache_variable_loop_tag', 'wppb_userlisting_users_loop', 10, 4 );

/**
 * Function that determines if a field has the type of Checkbox or Select Multiple from the meta name
 * @param $meta_name the meta name of the field
 * @param $wppb_manage_fields the mange fields array stored in the database
 * @return bool|mixed|void
 */
function wppb_check_if_field_is_multiple_value_from_meta_name( $meta_name, $wppb_manage_fields ){
    if( !empty( $meta_name ) ) {
        if (!empty($wppb_manage_fields) || $wppb_manage_fields != 'not_found') {
            foreach ($wppb_manage_fields as $field) {
                if ($field['meta-name'] == $meta_name && ($field['field'] == "Checkbox" || $field['field'] == "Select (Multiple)")) {
                    return apply_filters('wppb_is_multiple_value_type', true, $meta_name);
                    break;
                }
            }
        }
    }
    return false;
}

/**
 * We need to modify the query string in certain cases
 * @param $query the query performed on the DB
 */
function wppb_user_query_modifications($query) {
    global $userlisting_args;
    global $wpdb;

    /* hopefully it won't get applied to other user queries */
    if( !empty( $userlisting_args ) ){
        if ( isset( $_REQUEST['setSortingCriteria'] ) && ( trim( $_REQUEST['setSortingCriteria'] ) !== '' ) )
            $sorting_criteria = sanitize_text_field( $_REQUEST['setSortingCriteria'] );
        else
            $sorting_criteria = $userlisting_args[0]['default-sorting-criteria'];

        if ( isset( $_REQUEST['setSortingOrder'] ) && ( trim( $_REQUEST['setSortingOrder'] ) !== '' ) )
            $sorting_order = sanitize_text_field( $_REQUEST['setSortingOrder'] );
        else
            $sorting_order = $userlisting_args[0]['default-sorting-order'];

        switch( $sorting_criteria ){
            case "role":
                $query->query_orderby = 'ORDER by REPLACE( '.$wpdb->prefix.'usermeta.meta_value, SUBSTRING_INDEX( '.$wpdb->prefix.'usermeta.meta_value, \'"\', 1 ), \'\' ) '.$sorting_order;
                break;
            case "RAND()":
                $seed = apply_filters( 'wppb_userlisting_random_seed', '' );
                $query->query_orderby = 'ORDER by RAND('.$seed.')';
                break;
        }

        /* when searching in user listing we have to change the operator from AND to OR and move the search expression by changing some ')' around in the relationship between users table and user_meta table */
        if ( isset( $_REQUEST['searchFor'] ) ) {
            $search_for = $wpdb->prepare( "%s", '%'.$wpdb->esc_like( sanitize_text_field( $_REQUEST['searchFor'] ) ).'%' );
            remove_all_filters( 'user_search_columns' );//I am not sure that this works in any case but I will leave it here just in case. Implemented the pre_get_users hook for the correct way
            /* when we have sorting by a user meta then there are extra parenthesis which we have to rearange*/
            if( strpos( preg_replace( '/\s+/', ' ', $query->query_where ), ") ) ) AND (ID = " ) !== false ){
                $query->query_where = str_replace( ") ) ) AND (ID = ", "OR (ID = ", preg_replace( '/\s+/', ' ', $query->query_where ) );
                /* we add the user_registered column here as well */
                $query->query_where = str_replace( "user_nicename LIKE ".$search_for.")", "user_nicename LIKE ".$search_for."  OR user_registered LIKE ". $search_for ." OR display_name LIKE ". $search_for ." ) ) ) )", $query->query_where );
            }
            else{
                $query->query_where = str_replace( ") ) AND (ID = ", "OR (ID = ", preg_replace( '/\s+/', ' ', $query->query_where ) );
                /* we add the user_registered column here as well */
                $query->query_where = str_replace( "user_nicename LIKE ".$search_for.")", "user_nicename LIKE ".$search_for."  OR user_registered LIKE ". $search_for ." OR display_name LIKE ". $search_for ." ) ) )", $query->query_where );
            }
        }
    }
}
add_filter( 'pre_user_query', 'wppb_user_query_modifications' );

/* Remove all filters from the user_search_columns so it doesn't interfere with our own alteration of the query syntax for search in the wppb_user_query_modifications function */
add_action( 'pre_get_users', 'wppb_remove_user_search_columns_filters' );
function wppb_remove_user_search_columns_filters(){
    global $userlisting_args;
    /* hopefully it won't get applied to other user queries */
    if( !empty( $userlisting_args ) ) {
        if ( isset( $_REQUEST['searchFor'] ) ) {
            remove_all_filters('user_search_columns');
        }
    }
}

/**
 * Function that returns the user_id for the currently displayed user
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return ID
 */
function wppb_userlisting_user_id( $value, $name, $children, $extra_info ){
	$user_id = ( ! empty( $extra_info['user_id'] ) ? $extra_info['user_id'] : get_query_var( 'username' ) );
	$userID = wppb_get_query_var( 'username' );
	$user_info = ( empty( $userID ) ? get_userdata( $user_id ) : get_userdata( $userID ) );

	if( ! empty( $user_info ) )
		return $user_info->ID;
}
add_filter( 'mustache_variable_user_id', 'wppb_userlisting_user_id', 10, 4 );



/**
 * Function that returns the user_nicename for the currently displayed user
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return user_nicename
 */
function wppb_userlisting_user_nicename( $value, $name, $children, $extra_info ){
	$user_id = ( ! empty( $extra_info['user_id'] ) ? $extra_info['user_id'] : get_query_var( 'username' ) );
	$userID = wppb_get_query_var( 'username' );
	$user_info = ( empty( $userID ) ? get_userdata( $user_id ) : get_userdata( $userID ) );

	if( ! empty( $user_info ) )
		return $user_info->user_nicename;
}
add_filter( 'mustache_variable_user_nicename', 'wppb_userlisting_user_nicename', 10, 4 );



/**
 * Function that returns the link for the more_info link in html form
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_more_info( $value, $name, $children, $extra_info ){
	$more_url = wppb_userlisting_more_info_url( $value, $name, $children, $extra_info );
	
	if ( apply_filters( 'wbb_userlisting_extra_more_info_link_type', true ) )
		return apply_filters( 'wppb_userlisting_more_info_link', '<span id="wppb-more-span" class="wppb-more-span"><a href="'.$more_url.'" class="wppb-more" id="wppb-more" title="'.__( 'Click here to see more information about this user', 'profile-builder' ) .'" alt="'.__( 'More...', 'profile-builder' ).'">'.__( 'More...', 'profile-builder').'</a></span>', $more_url );
	
	else	
		return apply_filters( 'wppb_userlisting_more_info_link_with_arrow', '<a href="'.$more_url.'" class="wppb-more"><img src="'.WPPB_PLUGIN_URL.'assets/images/arrow_right.png" title="'.__( 'Click here to see more information about this user.', 'profile-builder' ).'" alt=">"></a>' );
}
add_filter( 'mustache_variable_more_info', 'wppb_userlisting_more_info', 10, 4 );


/**
 * Function that returns the map in html form
 *
 * @since v.2.3
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_show_user_meta_map( $value, $name, $children, $extra_info ){

    $userID = ( !empty( $extra_info['user_id'] ) && !empty( $extra_info['single'] ) )  ? $extra_info['user_id'] : wppb_get_query_var( 'username' ) ;
    $output_map = '';

    // Output for all user-listing
    if( empty( $userID ) ) {

        $more_url = wppb_userlisting_more_info_url( $value, $name, $children, $extra_info );
        $output_map .= '<a href="' . $more_url . '" class="wppb-view-map">' . __( 'View Map', 'profile-builder' ) . '</a>';

    // Output for single user-listing
    } else {

        global $wppb_manage_fields;
        if( !isset( $wppb_manage_fields ) )
            $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );

        $wppb_manage_fields = apply_filters( 'wppb_form_fields', $wppb_manage_fields, array( 'user_id' => $userID, 'context' => 'mustache_variable' ) );

        if( !empty( $wppb_manage_fields ) ) {
            foreach ($wppb_manage_fields as $field) {
                if ($field['meta-name'] == str_replace('meta_', '', $name)) {

                    wp_enqueue_script('wppb-google-maps-api-script', 'https://maps.googleapis.com/maps/api/js?key=' . $field['map-api-key'], array('jquery'), PROFILE_BUILDER_VERSION, true);
                    wp_enqueue_script('wppb-google-maps-script', WPPB_PLUGIN_URL . 'front-end/extra-fields/map/map.js', array('jquery'), PROFILE_BUILDER_VERSION, true);

                    $map_markers = wppb_get_user_map_markers($userID, $field['meta-name']);

                    $output_map .= wppb_get_map_output($field, array('markers' => $map_markers, 'show_search' => false, 'editable' => false));
                }
            }
        }
    }

    return apply_filters( 'wppb_userlisting_map', $output_map );

}
add_filter( 'mustache_variable_user_meta_map', 'wppb_userlisting_show_user_meta_map', 10, 4 );



/**
 * Function that returns the URL only for the more_info
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_more_info_url( $value, $name, $children, $extra_info ){		
	$user_id = ( !empty( $extra_info['user_id'] ) ? $extra_info['user_id'] : get_query_var( 'username' ) );
	$userID = wppb_get_query_var( 'username' );
	$user_info = ( empty( $userID ) ? get_userdata( $user_id ) : get_userdata( $userID ) );	
	
	//filter to get current user by either username or id(default);
	$get_user_by_ID = apply_filters( 'wppb_userlisting_get_user_by_id', true );
	$url = apply_filters( 'wppb_userlisting_more_base_url', get_permalink() );

	$user_data = get_the_author_meta( 'user_nicename', $user_info->ID );
	
	if ( isset( $_GET['page_id'] ) )
		return apply_filters ( 'wppb_userlisting_more_info_link_structure1', $url.'&userID='.$user_info->ID, $url, $user_info );
	
	else{
		if ( $get_user_by_ID === true )
			return apply_filters ( 'wppb_userlisting_more_info_link_structure2', trailingslashit( $url ).'user/'.$user_info->ID, $url, $user_info );
		
		else
			return apply_filters ( 'wppb_userlisting_more_info_link_structure3', trailingslashit( $url ).'user/'.$user_data, $url, $user_data );
	}
}
add_filter( 'mustache_variable_more_info_url', 'wppb_userlisting_more_info_url', 10, 4 );


/* we need to check if we have the filter that turns the link for the single user from /id/ to /username/
   if we have then the wppb_get_query_var needs to return the user id becuse that's what we expect in our functions that output the data
 */
add_action('init', 'wppb_check_userlisting_get_user_by');
function wppb_check_userlisting_get_user_by(){
    if ( has_filter( 'wppb_userlisting_get_user_by_id' ) ){
        add_filter( 'wppb_get_query_var_username', 'wppb_change_returned_username_query_var' );
        function wppb_change_returned_username_query_var( $var ){
            /* $var should be username and we want to change it into user id */
            if( !is_numeric($var) && !empty( $var ) ){
                $args= array(
                    'search' => $var,
                    'search_fields' => array( 'user_nicename' )
                );
                $user = new WP_User_Query($args);
                if( !empty( $user->results ) )
                    $var = $user->results[0]->ID;
            }

            return $var;
        }
    }
}

/* when we are on default permalinks we need to return $_GET['userID'] */
add_filter( 'wppb_get_query_var_username', 'wppb_change_returned_username_var_on_default_permalinks' );
function wppb_change_returned_username_var_on_default_permalinks( $var ){
    if( empty( $var ) && isset( $_GET['userID'] ) )
        return sanitize_user( $_GET['userID'] );

    return $var;
}

/**
 * Function that returns the link for the previous page
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_go_back_link( $value, $name, $children, $extra_values ){	
	if ( apply_filters( 'wppb_userlisting_go_back_link_type', true ) )
		return apply_filters( 'wppb_userlisting_go_back_link', '<div id="wppb-back-span" class="wppb-back-span"><a href=\'javascript:history.go(-1)\' class="wppb-back" id="wppb-back" title="'. __( 'Click here to go back', 'profile-builder' ) .'" alt="'. __( 'Back', 'profile-builder' ) .'">'. __( 'Back', 'profile-builder' ) .'</a></div>' );
	
	else	
		return apply_filters( 'wppb_userlisting_go_back_link_with_arrow', '<a href=\'javascript:history.go(-1)\' class="wppb-back"><img src="'.WPPB_PLUGIN_URL.'assets/images/arrow_left.png" title="'. __( 'Click here to go back', 'profile-builder' ) .'" alt="<"/></a>' );
}
add_filter( 'mustache_variable_go_back_link', 'wppb_userlisting_go_back_link', 10, 4 );



/**
 * Function that returns the pagination created
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_pagination( $value, $name, $children, $extra_info ){	
	global $totalUsers;
	
	require_once ( 'class-userlisting-pagination.php' );
	
	$this_form_settings = get_post_meta( $extra_info['userlisting_form_id'], 'wppb_ul_page_settings', true );
	
	if( !empty( $this_form_settings ) ){
		if ( ( $totalUsers != '0' ) || ( $totalUsers != 0 ) ){
			$pagination = new WPPB_Pagination;
			
			$first = __( '&laquo;&laquo; First', 'profile-builder' );
			$prev = __( '&laquo; Prev', 'profile-builder' );
			$next = __( 'Next &raquo; ', 'profile-builder' );
			$last = __( 'Last &raquo;&raquo;', 'profile-builder' );

            if( !is_int( (int)$this_form_settings[0]['number-of-userspage'] ) || (int)$this_form_settings[0]['number-of-userspage'] == 0 )
                $this_form_settings[0]['number-of-userspage'] = 5;

			$currentPage = wppb_get_query_var( 'page' );
			if ( $currentPage == 0 )
				$currentPage = 1;
		
			if ( isset( $_POST['searchFor'] ) ){
				$searchtext_label = apply_filters( 'wppb_userlisting_search_field_text', __( 'Search Users by All Fields', 'profile-builder' ) );
			
				if ( ( trim( $_POST['searchFor'] ) == $searchtext_label ) || ( trim( $_POST['searchFor'] ) == '' ) )
					$pagination->generate( $totalUsers, $this_form_settings[0]['number-of-userspage'], '', $first, $prev, $next, $last, $currentPage ); 
				
				else
					$pagination->generate( $totalUsers, $this_form_settings[0]['number-of-userspage'], sanitize_text_field($_POST['searchFor']), $first, $prev, $next, $last, $currentPage );
					
			}elseif ( isset( $_GET['searchFor'] ) ){
				$pagination->generate( $totalUsers, $this_form_settings[0]['number-of-userspage'], sanitize_text_field( $_GET['searchFor'] ), $first, $prev, $next, $last, $currentPage );
			
			}else{
				$pagination->generate( $totalUsers, $this_form_settings[0]['number-of-userspage'], '', $first, $prev, $next, $last, $currentPage );
			}
			
			return apply_filters( 'wppb_userlisting_userlisting_table_pagination', '<div class="userlisting_pagination" id="userlisting_pagination" align="right">'.$pagination->links().'</div>' );
		}
	}
	else
		return apply_filters( 'wppb_userlisting_no_pagination_settings', '<p class="error">'.__( 'You don\'t have any pagination settings on this userlisting!', 'profile-builder' ). '</p>' );
	
	return;
}
add_filter( 'mustache_variable_pagination', 'wppb_userlisting_pagination', 10, 4 );

/**
 * Function that returns the faceted filters
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_faceted_menus( $value, $name, $children, $extra_info ){
    $this_faceted_filters = get_post_meta( $extra_info['userlisting_form_id'], 'wppb_ul_faceted_settings', true );
    global $wppb_manage_fields;
    if( !isset( $wppb_manage_fields ) )
        $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );
    if( !empty( $this_faceted_filters ) ){

        /* we need to know if we have a search string and if we do then set the attribute so we can add it in the url when adding a facet in the url later */
        if( !empty( $_REQUEST['searchFor'] ) )
            $search_for = sanitize_text_field( $_REQUEST['searchFor'] );
        else
            $search_for = '';

        $faceted = '<ul class="wppb-faceted-list" data-search-for="'. esc_attr( $search_for ) .'">';

        $faceted .= '<li>'. wppb_ul_faceted_remove( $this_faceted_filters, $wppb_manage_fields ) . '</li>';

        foreach( $this_faceted_filters as $this_faceted_filter ){
            $faceted .= '<li class="wppb-facet-filter wppb-facet-'.$this_faceted_filter['facet-type'].'" id="wppb-facet-'. Wordpress_Creation_Kit_PB::wck_generate_slug( $this_faceted_filter['facet-meta'] ) .'">';
            if( !empty( $this_faceted_filter['facet-name'] ) )
                $faceted .= '<h5>'. $this_faceted_filter['facet-name'] .'</h5>';

            $meta_values = apply_filters( 'wppb_get_all_values_for_user_meta', wppb_get_all_values_for_user_meta( $this_faceted_filter['facet-meta'], $wppb_manage_fields ), $this_faceted_filter['facet-meta'], $this_faceted_filters, $wppb_manage_fields);

            $function_name = 'wppb_ul_faceted_'.$this_faceted_filter['facet-type'];
            if( function_exists( $function_name ) )
                $faceted .= $function_name( $this_faceted_filter, $meta_values, $wppb_manage_fields );

            if( $this_faceted_filter['facet-type'] == 'checkboxes' ) {
                if ( !empty($this_faceted_filter['facet-limit']) && is_numeric( trim( $this_faceted_filter['facet-limit'] ) ) && count( $meta_values ) >  intval( trim( $this_faceted_filter['facet-limit'] ) ) ) {
                    $faceted .= '<a href="#" class="show-all-facets">' . __('Show All', 'profile-builder') . '</a>';
                    $faceted .= '<a href="#" class="hide-all-facets" style="display:none;">' . __('Hide', 'profile-builder') . '</a>';
                }
            }

            $faceted .= '</li>';
        }
        $faceted .= '</ul><!-- wppb-faceted-list -->';
        return $faceted;
    }

    return;
}
add_filter( 'mustache_variable_faceted_menus', 'wppb_userlisting_faceted_menus', 10, 4 );

/**
 * Function that creates the filter for checkboxes
 * @param $faceted_filter_options the options for the current filter
 * @return string
 */
function wppb_ul_faceted_checkboxes( $faceted_filter_options, $meta_values, $wppb_manage_fields ){
    $current_value = wppb_ul_get_current_filter_value( $faceted_filter_options['facet-meta'] );

    if( !empty( $meta_values ) ){
        $filter = '';

        $i = 1;
        foreach( $meta_values as $meta_value => $repetitions ){
            if( !empty( $faceted_filter_options['facet-limit'] ) && is_numeric( trim( $faceted_filter_options['facet-limit'] ) ) && (int)$faceted_filter_options['facet-limit'] < $i )
                $filter .= '<div class="hide-this">';
            else
                $filter .= '<div>';

            $filter .= '<label for="wppb-facet-value-'. Wordpress_Creation_Kit_PB::wck_generate_slug($meta_value) .'"><input type="checkbox" id="wppb-facet-value-'. Wordpress_Creation_Kit_PB::wck_generate_slug($meta_value) .'" class="wppb-facet-checkbox" value="'. esc_attr( $meta_value ) .'" data-current-page="'. esc_attr( get_query_var('page') ) .'" data-filter-behaviour="'. esc_attr( $faceted_filter_options['facet-behaviour'] ) .'" data-meta-name="'. esc_attr( $faceted_filter_options['facet-meta'] ) .'" '. wppb_ul_checked( $meta_value, $current_value ) .'>';
            $filter .= esc_html( wppb_ul_facet_value_or_label( $meta_value, $faceted_filter_options, $wppb_manage_fields ) );
            if( apply_filters( 'wppb_ul_show_filter_count', true ) )
                $filter .= ' ('. $repetitions .')';
            $filter .= '</label>';
            $filter .= '</div>';

            $i++;
        }

        return $filter;
    }
    else
        return wppb_get_facet_no_options_message( $faceted_filter_options );
}

/**
 * Function that creates the filter for selects
 * @param $faceted_filter_options the options for the current filter
 * @return string
 */
function wppb_ul_faceted_select($faceted_filter_options, $meta_values, $wppb_manage_fields, $multiple = false ){
    $current_value = wppb_ul_get_current_filter_value( $faceted_filter_options['facet-meta'] );

    if( !empty( $meta_values ) ){
        $filter = '<select class="wppb-facet-select';
        if( $multiple )
            $filter .= '-multiple';
        $filter .= '" data-filter-behaviour="'. esc_attr( $faceted_filter_options['facet-behaviour'] ) .'" data-current-page="'. esc_attr( get_query_var('page') ) .'" data-meta-name="'. esc_attr( $faceted_filter_options['facet-meta'] ) .'"';
        /* only add multiple attr for the expand behaviour. for narrow just have a normal select with a size attribute so it fakes a multiple select. this means we will handle it differently in js */
        if( $multiple && $faceted_filter_options['facet-behaviour'] == 'expand' )
            $filter .= ' multiple ';
        if( $multiple && !empty( $faceted_filter_options['facet-limit'] ) && is_numeric( trim( $faceted_filter_options['facet-limit'] ) ) )
            $filter .= ' size="'.$faceted_filter_options['facet-limit'].'" ';
        $filter .= '>';
        $filter .= '<option value="">'. __( 'Choose...', 'profile-builder' ) .'</option>';
        foreach( $meta_values as $meta_value => $repetitions ){
            $filter .= '<option value="'.esc_attr( $meta_value ).'" '. wppb_ul_selected( $meta_value, $current_value ) .'>'.esc_html( wppb_ul_facet_value_or_label( $meta_value, $faceted_filter_options, $wppb_manage_fields ) );
            if( apply_filters( 'wppb_ul_show_filter_count', true ) )
                $filter .= ' ('. $repetitions .')';
            $filter .= '</option>';
        }
        $filter .= '</select>';

        return $filter;
    }
    else
        return wppb_get_facet_no_options_message( $faceted_filter_options );
}

/**
 * Function that creates the filter for selects
 * @param $faceted_filter_options the options for the current filter
 * @return string
 */
function wppb_ul_faceted_select_multiple($faceted_filter_options, $meta_values, $wppb_manage_fields ){
    $filter = wppb_ul_faceted_select( $faceted_filter_options, $meta_values, $wppb_manage_fields, true );
    return $filter;
}


/**
 * Function that creates the filter for range
 * @param $faceted_filter_options the options for the current filter
 * @return string
 */
function wppb_ul_faceted_range( $faceted_filter_options, $meta_values, $wppb_manage_fields ){
    $filter = '';
    if( !empty( $meta_values ) ) {
        foreach ($meta_values as $value => $count) {
            if (!is_numeric($value))
                unset($meta_values[$value]);
        }

        /* we might have nothing left */
        if( !empty( $meta_values ) ) {
            ksort($meta_values, SORT_NUMERIC);

            $i = 1;
            foreach ($meta_values as $value => $count) {
                if ($i == 1) $first_value = $value;
                if ($i == count($meta_values)) $last_value = $value;
                $i++;
            }

            $first_current_value = $first_value;
            $last_current_value = $last_value;


            $current_value = wppb_ul_get_current_filter_value($faceted_filter_options['facet-meta']);
            if (!empty($current_value)) {
                $current_value = explode('-', $current_value);
                $first_current_value = $current_value[0];
                $last_current_value = $current_value[1];
            }

            if (!isset($first_value) || !isset($last_value) || !isset($first_current_value) || !isset($last_current_value))
                return '';

            $filter .= '<div class="wppb-ul-range-values ' . esc_attr($faceted_filter_options['facet-meta']) . '">' . $first_current_value . '-' . $last_current_value . '</div>';
            $filter .= '<div class="wppb-ul-slider-range ' . esc_attr($faceted_filter_options['facet-meta']) . '" value="" data-meta-name="' . esc_attr($faceted_filter_options['facet-meta']) . '" data-filter-behaviour="' . esc_attr($faceted_filter_options['facet-behaviour']) . '" data-current-page="' . esc_attr(get_query_var('page')) . '"></div>
            <script type="text/javascript">
                jQuery(function(){
                    wppbRangeFacet( "' . esc_attr($faceted_filter_options['facet-meta']) . '", ' . $first_value . ', ' . $last_value . ', ' . $first_current_value . ', ' . $last_current_value . ' );
                });
            </script>';
        }
    }

    if( $filter == '' )
        $filter = wppb_get_facet_no_options_message( $faceted_filter_options );

    return $filter;

}


/**
 * Function that returns and filters the facet "No options available" message
 * @param $faceted_filter_options the options for the current filter
 * @return string
 */
function wppb_get_facet_no_options_message( $faceted_filter_options ){

    return apply_filters('wppb_facet_no_options_message', __( 'No options available', 'profile-builder' ), $faceted_filter_options );

}


/**
 * Function that creates the filter for search
 * @param $faceted_filter_options the options for the current filter
 * @return string
 */
function wppb_ul_faceted_search( $faceted_filter_options, $meta_values, $wppb_manage_fields ){
    $current_value = wppb_ul_get_current_filter_value( $faceted_filter_options['facet-meta'] );

    $filter = '<input type="text" value="'. $current_value .'" class="wppb-facet-search" data-filter-behaviour="'. esc_attr( $faceted_filter_options['facet-behaviour'] ) .'" data-current-page="'. esc_attr( get_query_var('page') ) .'" data-meta-name="'. esc_attr( $faceted_filter_options['facet-meta'] ) .'">';

    return $filter;

}

/**
 * Function that displays the Label from the Manage fields instead of the database value if we have one
 * @param $meta_value the database value
 * @param $faceted_filter_options the current filter options
 * @param $wppb_manage_fields the Manage Fields options
 * @return string the label if we have any else the database value
 */
function wppb_ul_facet_value_or_label( $meta_value, $faceted_filter_options, $wppb_manage_fields ){
    //cast to string
    $meta_value = (string)$meta_value;
    $returned_value = $meta_value;
    if( !empty( $wppb_manage_fields ) ){
        foreach( $wppb_manage_fields as $field ){
            if( $field['meta-name'] == $faceted_filter_options['facet-meta'] ){
                if( !empty( $field['labels'] ) ){
                    $field_values = array_map('trim', explode(',', $field['options']));
                    $field_labels = array_map('trim', explode(',', $field['labels']));

                    if( $field['field'] == 'Checkbox' || $field['field'] == 'Select (Multiple)' ){
                        $meta_values = array_map( 'trim', explode(',', $meta_value ) );
                    }

                    if ( !empty($field_values) ) {
                        foreach ($field_values as $key => $value) {
                            if ($value === $meta_value) {
                                if (isset($field_labels[$key])) {
                                    $returned_value = $field_labels[$key];
                                    break;
                                }
                            }

                            if( !empty( $meta_values ) ){
                                if( in_array( $value, $meta_values ) ){
                                    $returned_values[] = $field_labels[$key];
                                }
                            }
                        }

                        if( !empty( $returned_values ) ){
                            $returned_value = implode( ',', $returned_values );
                        }
                    }
                } else {
                    if( $field['field'] == 'Select (Country)' ){
                        $country_array = wppb_country_select_options( 'userlisting' );
                        $returned_value = $country_array[$meta_value];
                    } else if ($field['field'] == 'Select (CPT)')
                        $returned_value = get_the_title($meta_value);
                }
            }
        }
    }

    /* for user role grab the labels from the wp_roles global */
    global $wpdb;
    if( $faceted_filter_options['facet-meta'] ==  $wpdb->get_blog_prefix().'capabilities' ){
        global $wp_roles;
        if( !empty( $wp_roles->roles[$meta_value]['name'] ) ){
            $returned_value = $wp_roles->roles[$meta_value]['name'];
        }
    }

    return apply_filters('wppb_ul_facet_value_or_label', $returned_value, $meta_value, $faceted_filter_options, $wppb_manage_fields);
}

/**
 * Function that gets the value for a filter from the url
 * @param $filter_name the neame for the filter
 * @return string
 */
function wppb_ul_get_current_filter_value( $filter_name ){
    if( !empty( $_GET['ul_filter_'. $filter_name] ) )
        $current_value = sanitize_text_field( stripslashes( $_GET['ul_filter_'. $filter_name] ) );
    else
        $current_value = '';

    return $current_value;
}

/**
 * Function that chacks if the current value is checked
 * @param $value current value
 * @param $compare compared against
 * @return string
 */
function wppb_ul_checked( $value, $compare ){
    if( !empty( $compare ) ) {
        $compare = explode('||', $compare);
        if (in_array($value, $compare))
            return 'checked';
        else
            return '';
    }
}

/**
 * Function that chacks if the current value is selected
 * @param $value current value
 * @param $compare compared against
 * @return string
 */
function wppb_ul_selected( $value, $compare ){
    if( !empty( $compare ) ) {
        $compare = explode('||', $compare);
        if (in_array($value, $compare))
            return 'selected';
        else
            return '';
    }
}

function wppb_ul_faceted_remove( $faceted_filters_options, $wppb_manage_fields ){
    $filter = '';
    if( !empty( $faceted_filters_options ) ){
        $filter .= '<ul id="wppb-remove-facets-container">';
        $have_filters = array();
        foreach( $faceted_filters_options as $faceted_filter_options ){
            if( isset( $_GET['ul_filter_'.$faceted_filter_options['facet-meta']]  ) ) {
                $have_filters[] = $faceted_filter_options['facet-meta'];
                $filter_values = explode( '||', sanitize_text_field( stripslashes( $_GET['ul_filter_'.$faceted_filter_options['facet-meta']] ) ) );
                foreach( $filter_values as $filter_value ) {
                    $filter .= '<li>';
                    $filter .= '<a href="#" class="wppb-remove-facet" data-meta-name="' . esc_attr($faceted_filter_options['facet-meta']) . '" data-meta-value="' . esc_attr($filter_value) . '" data-current-page="' . esc_attr(get_query_var('page')) . '">' . $faceted_filter_options['facet-name'] . ':' . esc_html(  wppb_ul_facet_value_or_label( $filter_value, $faceted_filter_options, $wppb_manage_fields ) ) . '</a>';
                    $filter .= '</li>';
                }
            }
        }

        if( $have_filters ){
            $filter .= '<li>';
            $filter .= '<a href="#" class="wppb-remove-all-facets" data-all-filters="'. implode(',', $have_filters ) .'" data-current-page="' . esc_attr(get_query_var('page')) . '">' . __( 'Remove All Filters', 'profile-builder' ) . '</a>';
            $filter .= '</li>';
        }

        $filter .= '</ul>';
    }
    return $filter;
}

/**
 * Function that returns all the meta values for a meta key in the usermeta table sorted and unique
 * @param $meta_key
 * @return array
 */
function wppb_get_all_values_for_user_meta( $meta_key, $wppb_manage_fields ){
    $results = array();
    if( !empty( $meta_key ) ) {
        global $all_queried_user_ids_string;

        $gloabl_filter_ids_name = $meta_key.'_user_ids';
        global ${$gloabl_filter_ids_name};


        if( !empty( $all_queried_user_ids_string ) ){
            global $wpdb;
            $query_string = "
                    SELECT meta_value FROM {$wpdb->usermeta}
                    WHERE meta_key = '%s'
                    AND meta_value != ''
                ";

            if( !empty( $$gloabl_filter_ids_name ) ) {
                $partial_ids = $$gloabl_filter_ids_name;
                $query_string .= " AND user_id IN ($partial_ids)";
            }
            else
                $query_string .= " AND user_id IN ($all_queried_user_ids_string)";

            $results = $wpdb->get_col($wpdb->prepare( $query_string, $meta_key ));

            /* separate values in database for checkboxes */
            if( wppb_check_if_field_is_multiple_value_from_meta_name( $meta_key, $wppb_manage_fields ) ) {
                if( !empty( $results ) ){
                    $new_keys = array();
                    foreach( $results as $key => $value ){
                        if( strpos( $value, ',' ) !== false ){
                            $value = explode( ',', $value );
                            unset( $results[$key] );
                            $new_keys = array_merge( $new_keys, $value );
                        }
                    }
                    $results = array_merge( $results, $new_keys);
                }
            }/* we need to handle the role facet differently */
            else if( $meta_key == $wpdb->get_blog_prefix().'capabilities' ){
                if( !empty( $results ) ){
                    $new_keys = array();
                    foreach( $results as $key => $value ){
                        if( is_serialized( $value ) ){
                            $value = array_keys( maybe_unserialize( $value ) );
                            unset( $results[$key] );
                            $new_keys = array_merge( $new_keys, $value );
                        }
                    }
                    $results = array_merge( $results, $new_keys);
                }
            }

            $results = array_count_values($results);
            uksort($results, "strcasecmp");
        }
    }
    return $results;
}

/**
 * Function that returns the search field
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_extra_search_all_fields( $value, $name, $children, $extra_info ){	
	$userlisting_settings = get_post_meta( $extra_info['userlisting_form_id'], 'wppb_ul_page_settings', true );
	$set_new_sorting_order = ( isset( $userlisting_settings[0]['default-sorting-order'] ) ? $userlisting_settings[0]['default-sorting-order'] : 'asc' );	

	$searchText = apply_filters( 'wppb_userlisting_search_field_text', __( 'Search Users by All Fields', 'profile-builder' ) );
	
	if ( isset($_REQUEST['searchFor'] ) )
		if ( trim( $_REQUEST['searchFor'] ) != $searchText )
			$searchText = esc_attr( stripslashes( $_REQUEST['searchFor'] ) );
	
	$setSortingCriteria = ( isset( $userlisting_settings[0]['default-sorting-criteria'] ) ? $userlisting_settings[0]['default-sorting-criteria'] : 'login' );	
	$setSortingCriteria = ( isset( $_REQUEST['setSortingCriteria'] ) ? sanitize_text_field( $_REQUEST['setSortingCriteria'] ) : $setSortingCriteria );
	
	$setSortingOrder = ( isset( $userlisting_settings[0]['default-sorting-order'] ) ? $userlisting_settings[0]['default-sorting-order'] : 'asc' );	
	$setSortingOrder = ( isset( $_REQUEST['setSortingOrder'] ) ? sanitize_text_field( $_REQUEST['setSortingOrder'] ) : $setSortingOrder );

	return '
		<form method="post" action="'.add_query_arg( array( 'page' => 1, 'setSortingCriteria' => $setSortingCriteria, 'setSortingOrder' => $setSortingOrder ) ).'" class="wppb-search-users wppb-user-forms">
            <div class="wppb-search-users-wrap">
                <input onfocus="if(this.value == \''.$searchText.'\'){this.value = \'\';}" type="text" onblur="if(this.value == \'\'){this.value=\''.$searchText.'\';}" id="wppb-search-fields" name="searchFor" title="'. $searchText .'" value="'.$searchText.'" />
		        <input type="hidden" name="action" value="searchAllFields" />
		        <input type="submit" name="searchButton" class="wppb-search-button" value="'.__( 'Search', 'profile-builder' ).'" />
			    <a class="wppb-clear-results" href="'.wppb_clear_results().'">'.__( 'Clear Results', 'profile-builder' ).'</a>
		    </div>
		</form>';
}
add_filter( 'mustache_variable_extra_search_all_fields', 'wppb_userlisting_extra_search_all_fields', 10, 4 );

/**
 * Function that returns the number of users
 *
 * @since v.2.3.3
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_user_count( $value, $name, $children, $extra_values ){
	global $totalUsers;
	return $totalUsers;
}
add_filter('mustache_variable_user_count','wppb_userlisting_user_count', 10,4);

/**
 * Function that returns the avatar or gravatar (based on what is set)
 *
 * @since v.2.0
 *
 * @param str $value undefined value
 * @param str $name the name of the field
 * @param array $children an array containing all other fields
 * @param array $extra_info various extra information about the user
 *
 *
 * @return string
 */
function wppb_userlisting_avatar_or_gravatar( $value, $name, $children, $extra_information ){
	$this_form_settings = get_post_meta( $extra_information['userlisting_form_id'], 'wppb_ul_page_settings', true );
	
	$all_userlisting_avatar_size = apply_filters( 'all_userlisting_avatar_size', ( isset( $this_form_settings[0]['avatar-size-all-userlisting'] ) ? (int)$this_form_settings[0]['avatar-size-all-userlisting'] : 100 ) );
	$single_userlisting_avatar_size = apply_filters( 'single_userlisting_avatar_size', ( isset( $this_form_settings[0]['avatar-size-single-userlisting'] ) ? (int)$this_form_settings[0]['avatar-size-single-userlisting'] : 100 ) );
	
	$userID = wppb_get_query_var( 'username' );

	$user_info = ( empty( $userID ) ? get_userdata( $extra_information['user_id'] ) : get_userdata( $userID ) );
	$avatar_size = ( empty( $userID ) ? $all_userlisting_avatar_size : $single_userlisting_avatar_size );
	$avatar_crop = apply_filters( 'all_userlisting_avatar_crop', true, $userID );

	$avatar_or_gravatar = get_avatar( (int)$user_info->data->ID, $avatar_size );

	$wp_upload_array = wp_upload_dir();

	if ( strpos( $avatar_or_gravatar, $wp_upload_array['baseurl'] ) ){
		wppb_resize_avatar( (int)$user_info->data->ID, $avatar_size, $avatar_crop );
		$avatar_or_gravatar = get_avatar( (int)$user_info->data->ID, $avatar_size );
	}

	return apply_filters( 'wppb_userlisting_extra_avatar_or_gravatar', $avatar_or_gravatar, $user_info, $avatar_size, $userID );	
}
add_filter( 'mustache_variable_avatar_or_gravatar', 'wppb_userlisting_avatar_or_gravatar', 10, 4 );



/**
 * Remove certain actions from post list view
 *
 * @since v.2.0
 *
 * @param array $actions
 *
 * return array
 */
function wppb_remove_ul_view_link( $actions ){
	global $post;
	
	if ( $post->post_type == 'wppb-ul-cpt' ){
		unset( $actions['view'] );
		
		if ( wppb_get_post_number ( $post->post_type, 'singular_action' ) )
			unset( $actions['trash'] );
	}

	return $actions;
}
add_filter( 'post_row_actions', 'wppb_remove_ul_view_link', 10, 1 );


/**
 * Remove certain bulk actions from post list view
 *
 * @since v.2.0
 *
 * @param array $actions
 *
 * return array
 */
function wppb_remove_trash_bulk_option_ul( $actions ){
	global $post;
	if( !empty( $post ) ){	
		if ( $post->post_type == 'wppb-ul-cpt' ){
			unset( $actions['view'] );
			
			if ( wppb_get_post_number ( $post->post_type, 'bulk_action' ) )
				unset( $actions['trash'] );
		}
	}

	return $actions;
}
add_filter( 'bulk_actions-edit-wppb-ul-cpt', 'wppb_remove_trash_bulk_option_ul' );


/**
 * Function to hide certain publishing options
 *
 * @since v.2.0
 *
 */
function wppb_hide_ul_publishing_actions(){
	global $post;

	if ( $post->post_type == 'wppb-ul-cpt' ){
		echo '<style type="text/css">#misc-publishing-actions, #minor-publishing-actions{display:none;}</style>';
		
		$ul = get_posts( array( 'posts_per_page' => -1, 'post_status' => apply_filters ( 'wppb_check_singular_ul_form_publishing_options', array( 'publish' ) ) , 'post_type' => 'wppb-ul-cpt' ) );
		if ( count( $ul ) == 1 )
			echo '<style type="text/css">#major-publishing-actions #delete-action{display:none;}</style>';
	}
}
add_action('admin_head-post.php', 'wppb_hide_ul_publishing_actions');
add_action('admin_head-post-new.php', 'wppb_hide_ul_publishing_actions');


/**
 * Add custom columns to listing
 *
 * @since v.2.0
 *
 * @param array $columns
 * @return array $columns
 */
function wppb_add_extra_column_for_ul( $columns ){
	$columns['ul-shortcode'] = __( 'Shortcode', 'profile-builder' );
	
	return $columns;
}
add_filter( 'manage_wppb-ul-cpt_posts_columns', 'wppb_add_extra_column_for_ul' );


/**
 * Add content to the displayed column
 *
 * @since v.2.0
 *
 * @param string $column_name
 * @param integer $post_id
 * @return void
 */
function wppb_ul_custom_column_content( $column_name, $post_id ){
	if( $column_name == 'ul-shortcode' ){
		$post = get_post( $post_id );
		
		if( empty( $post->post_title ) )
			$post->post_title = __( '(no title)', 'profile-builder' );

        echo "<input readonly spellcheck='false' type='text' class='wppb-shortcode input' value='[wppb-list-users name=\"" . Wordpress_Creation_Kit_PB::wck_generate_slug( $post->post_title ) . "\"]' />";
	}
}
add_action("manage_wppb-ul-cpt_posts_custom_column",  "wppb_ul_custom_column_content", 10, 2);


/**
 * Add side metaboxes
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_ul_content(){
	global $post;
	
	$form_shortcode = trim( Wordpress_Creation_Kit_PB::wck_generate_slug( $post->post_title ) );
	if ( $form_shortcode == '' )
		echo '<p><em>' . __( 'The shortcode will be available after you publish this form.', 'profile-builder' ) . '</em></p>';
	else{
        echo '<p>' . __( 'Use this shortcode on the page you want the form to be displayed:', 'profile-builder' );
        echo '<br/>';
        echo "<textarea readonly spellcheck='false' class='wppb-shortcode textarea'>[wppb-list-users name=\"" . $form_shortcode . "\"]</textarea>";
        echo '</p><p>';
        echo __( '<span style="color:red;">Note:</span> changing the form title also changes the shortcode!', 'profile-builder' );
        echo '</p>';

        echo '<h4>'. __('Extra shortcode parameters', 'profile-builder') .'</h4>';
        
        echo '<a href="wppb-extra-shortcode-parameters" class="wppb-open-modal-box">' . __( "View all extra shortcode parameters", "profile-builder" ) . '</a>';

        echo '<div id="wppb-extra-shortcode-parameters" title="' . __( "Extra shortcode parameters", "profile-builder" ) . '" class="wppb-modal-box">';

        	echo '<p>';
	        echo '<strong>meta_key="key_here"<br /> meta_value="value_here"</strong> - '. __( 'displays users having a certain meta-value within a certain (extra) meta-field', 'profile-builder' );
	        echo '<br/><br/>'.__( 'Example:', 'profile-builder' ).'<br/>';
	        echo '<strong>[wppb-list-users name="' . $form_shortcode . '" meta_key="skill" meta_value="Photography"]</strong><br/><br/>';
	        echo __( 'Remember though, that the field-value combination must exist in the database.', 'profile-builder' );
	        echo '</p>';

	        echo '<hr />';

	        echo '<p>';
	        echo '<strong>include="user_id_1, user_id_2"</strong> - '. __( 'displays only the users that you specified the user_id for', 'profile-builder' );
	        echo '</p>';

	        echo '<hr />';

	        echo '<p>';
	        echo '<strong>exclude="user_id_1, user_id_2"</strong> - '. __( 'displays all users except the ones you specified the user_id for', 'profile-builder' );
	        echo '</p>';

        echo '</div>';
    }
}

function wppb_ul_side_box(){
	add_meta_box( 'wppb-ul-side', __( 'Form Shortcode', 'profile-builder' ), 'wppb_ul_content', 'wppb-ul-cpt', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'wppb_ul_side_box' );



/**
 * Function that manages the Userlisting CPT
 *
 * @since v.2.0
 *
 * @return void
 */
function wppb_manage_ul_cpt(){
	global $wp_roles;
	//$default_wp_role = trim( get_option( 'default_role' ) );
	$available_roles = $sorting_order = $sorting_criteria = $avatar_size = array();
	
	// Set role
	$available_roles[] = '%*%*';
	foreach ( $wp_roles->roles as $slug => $role )
		$available_roles[] = '%'.trim( $role['name'] ).'%'.$slug;

	// Set sorting criteria
	$sorting_criteria[] = '%'.__( 'Username', 'profile-builder' ).'%login';
	$sorting_criteria[] = '%'.__( 'Email', 'profile-builder' ).'%email';
	$sorting_criteria[] = '%'.__( 'Website', 'profile-builder' ).'%url';
	$sorting_criteria[] = '%'.__( 'Biographical Info', 'profile-builder' ).'%bio';
	$sorting_criteria[] = '%'.__( 'Registration Date', 'profile-builder' ).'%registered';
	$sorting_criteria[] = '%'.__( 'Firstname', 'profile-builder' ).'%firstname';
	$sorting_criteria[] = '%'.__( 'Lastname', 'profile-builder' ).'%lastname';
	$sorting_criteria[] = '%'.__( 'Display Name', 'profile-builder' ).'%nicename';
    $sorting_criteria[] = '%'.__( 'Nickname', 'profile-builder' ).'%nickname';
	$sorting_criteria[] = '%'.__( 'Number of Posts', 'profile-builder' ).'%post_count';
    $sorting_criteria[] = '%'.__( 'Role', 'profile-builder' ).'%role';

	// Default contact methods were removed in WP 3.6. A filter dictates contact methods.
	if ( apply_filters( 'wppb_remove_default_contact_methods', get_site_option( 'initial_db_version' ) < 23588 ) ){
		$sorting_criteria[] = '%'.__( 'Aim', 'profile-builder' ).'%aim';
		$sorting_criteria[] = '%'.__( 'Yim', 'profile-builder' ).'%yim';
		$sorting_criteria[] = '%'.__( 'Jabber', 'profile-builder' ).'%jabber';
	}
	
	$exclude_fields_from_settings = apply_filters( 'wppb_exclude_field_list_userlisting_settings', array( 'Default - Name (Heading)', 'Default - Contact Info (Heading)', 'Default - About Yourself (Heading)', 'Default - Username', 'Default - First Name', 'Default - Last Name', 'Default - Nickname', 'Default - E-mail', 'Default - Website', 'Default - AIM', 'Default - Yahoo IM', 'Default - Jabber / Google Talk', 'Default - Password', 'Default - Repeat Password', 'Default - Biographical Info', 'Default - Blog Details', 'Default - Display name publicly as', 'Heading' ) );

    global $wppb_manage_fields;
    if( !isset( $wppb_manage_fields ) )
        $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );
    if( !empty( $wppb_manage_fields ) && is_array( $wppb_manage_fields ) ) {
        foreach ($wppb_manage_fields as $key => $value) {
            if (!in_array($value['field'], $exclude_fields_from_settings) && !empty($value['meta-name']))
                $sorting_criteria[] = '%' . $value['field-title'] . '%' . $value['meta-name'];
        }
    }

	$sorting_criteria[] = '%'.__( 'Random (very slow on large databases > 10K user)', 'profile-builder' ).'%RAND()';
	
	// Set sorting order
	$sorting_order[] = '%'.__( 'Ascending', 'profile-builder' ).'%asc';
	$sorting_order[] = '%'.__( 'Descending', 'profile-builder' ).'%desc';
	
	// Avatar size
	for( $i=0; $i<=200; $i++ )
		$avatar_size[] = $i;

	// set up the fields array
	$settings_fields = array( 		
		array( 'type' => 'checkbox', 'slug' => 'roles-to-display', 'title' => __( 'Roles to Display', 'profile-builder' ), 'options' => $available_roles, 'default' => '*', 'description' => __( 'Restrict the userlisting to these selected roles only<br/>If not specified, defaults to all existing roles', 'profile-builder' ) ),
		array( 'type' => 'text', 'slug' => 'number-of-userspage', 'title' => __( 'Number of Users/Page', 'profile-builder' ), 'default' => '5', 'description' => __( 'Set the number of users to be displayed on every paginated part of the all-userlisting', 'profile-builder' ) ),
		array( 'type' => 'select', 'slug' => 'default-sorting-criteria', 'title' => __( 'Default Sorting Criteria', 'profile-builder' ), 'options' => apply_filters( 'wppb_default_sorting_criteria', $sorting_criteria ), 'default' => 'login', 'description' => __( 'Set the default sorting criteria<br/>This can temporarily be changed for each new session', 'profile-builder' ) ),
		array( 'type' => 'select', 'slug' => 'default-sorting-order', 'title' => __( 'Default Sorting Order', 'profile-builder' ), 'options' => $sorting_order, 'default' => 'asc', 'description' => __( 'Set the default sorting order<br/>This can temporarily be changed for each new session', 'profile-builder' ) ),
		array( 'type' => 'select', 'slug' => 'avatar-size-all-userlisting', 'title' => __( 'Avatar Size (All-userlisting)', 'profile-builder' ), 'options' => $avatar_size, 'default' => '40', 'description' => __( 'Set the avatar size on the all-userlisting only', 'profile-builder' ) ),
		array( 'type' => 'select', 'slug' => 'avatar-size-single-userlisting', 'title' => __( 'Avatar Size (Single-userlisting)', 'profile-builder' ), 'options' => $avatar_size, 'default' => '60', 'description' => __( 'Set the avatar size on the single-userlisting only', 'profile-builder' ) ),
		array( 'type' => 'checkbox', 'slug' => 'visible-only-to-logged-in-users', 'title' => __( 'Visible only to logged in users?', 'profile-builder' ), 'options' => array( '%'.__( 'Yes', 'profile-builder' ).'%yes' ), 'description' => __( 'The userlisting will only be visible only to the logged in users', 'profile-builder' ) ),
        array( 'type' => 'checkbox', 'slug' => 'visible-to-following-roles', 'title' => __( 'Visible to following Roles', 'profile-builder' ), 'options' => $available_roles, 'default' => '*', 'description' => __( 'The userlisting will only be visible to the following roles', 'profile-builder' ) ),
	);
	
	// set up the box arguments
	$args = array(
		'metabox_id' => 'wppb-ul-settings-args',
		'metabox_title' => __( 'Userlisting Settings', 'profile-builder' ),
		'post_type' => 'wppb-ul-cpt',
		'meta_name' => 'wppb_ul_page_settings',
		'meta_array' => $settings_fields,			
		'sortable' => false,
		'single' => true
	);
	new Wordpress_Creation_Kit_PB( $args );

    $facet_types = array( '%Checkboxes%checkboxes', '%Select%select', '%Select Multiple%select_multiple', '%Range%range', '%Search%search' );
    $facet_meta = array();
    $exclude_fields_from_facet_menus = apply_filters( 'wppb_exclude_field_list_userlisting_facet_menu_settings', array() );
    if( !empty( $wppb_manage_fields ) && is_array( $wppb_manage_fields ) ) {
        foreach ($wppb_manage_fields as $key => $value) {
            if (!in_array($value['field'], $exclude_fields_from_facet_menus) && !empty($value['meta-name']))
                $facet_meta[] = '%' . $value['field-title'] . '%' . $value['meta-name'];
        }
    }

    /* add roles to facets options */
    global $wpdb;
    $facet_meta[] = '%Role%'.$wpdb->get_blog_prefix().'capabilities';

    // set up the fields array for faceted
    $settings_fields = array(
        array( 'type' => 'text', 'slug' => 'facet-name', 'title' => __( 'Label', 'profile-builder' ), 'required' => true, 'description' => __( 'Choose the facet name that appears on the frontend', 'profile-builder' ) ),
        array( 'type' => 'select', 'slug' => 'facet-type', 'title' => __( 'Facet Type', 'profile-builder' ), 'options' => $facet_types, 'default' => 'checkboxes', 'description' => __( 'Choose the facet menu type', 'profile-builder' ) ),
        array( 'type' => 'select', 'slug' => 'facet-meta', 'title' => __( 'Facet Meta', 'profile-builder' ), 'options' => apply_filters( 'wppb_userlisting_facet_meta', $facet_meta, $wppb_manage_fields ), 'description' => __( 'Choose the meta field for the facet menu', 'profile-builder' ) ),
        array( 'type' => 'select', 'slug' => 'facet-behaviour', 'title' => __( 'Behaviour', 'profile-builder' ), 'options' => array( '%'. __('Narrow the results', 'profile-builder') .'%narrow', '%'. __('Expand the results', 'profile-builder') .'%expand' ), 'description' => __( 'Choose how multiple selections affect the results', 'profile-builder' ) ),
        array( 'type' => 'text', 'slug' => 'facet-limit', 'title' => __( 'Visible choices', 'profile-builder' ), 'description' => __( 'Show a toggle link after this many choices. Leave blank for all', 'profile-builder' ) ),
    );

    // set up the box arguments
    $args = array(
        'metabox_id' => 'wppb-ul-faceted-args',
        'metabox_title' => __( 'Faceted Menus', 'profile-builder' ),
        'post_type' => 'wppb-ul-cpt',
        'meta_name' => 'wppb_ul_faceted_settings',
        'meta_array' => $settings_fields
    );
    new Wordpress_Creation_Kit_PB( $args );

    /* start search field setting box */
    $search_fields = array( '%User Login%user_login', '%User Email%user_email', '%User Website%user_url' );
    $search_defaults = array( 'user_login', 'user_email', 'user_url' );
    if( !empty( $wppb_manage_fields ) && is_array( $wppb_manage_fields ) ) {
        foreach ($wppb_manage_fields as $key => $value) {
            if (!empty($value['meta-name'])) {
                $search_fields[] = '%' . $value['field-title'] . '%' . $value['meta-name'];
                $search_defaults[] = $value['meta-name'];
            }
        }
    }
    $settings_fields = array(
        array( 'type' => 'checkbox', 'slug' => 'search-fields', 'options' => apply_filters('wppb_userlisting_search_all_fields', $search_fields, $wppb_manage_fields), 'default' => $search_defaults,  'title' => __( 'Search Fields', 'profile-builder' ), 'description' => __( 'Choose the fields in which the Search Field will look in', 'profile-builder' ) ),
    );
    // set up the box arguments
    $args = array(
        'metabox_id' => 'wppb-ul-search-settings',
        'metabox_title' => __( 'Search Settings', 'profile-builder' ),
        'post_type' => 'wppb-ul-cpt',
        'meta_name' => 'wppb_ul_search_settings',
        'meta_array' => $settings_fields,
        'single'  => true
    );
    new Wordpress_Creation_Kit_PB( $args );
    /* end search field setting box */
}
add_action( 'admin_init', 'wppb_manage_ul_cpt', 1 );

/* hook to filter to exclude fields from the search field */
add_filter('wppb_exclude_search_fields', 'wppb_ul_exclude_fields_from_search',10, 2 );
/**
 * @param $fields array of fields to exclude from search
 * @param $userlisting_form_id the id of the userlisting cpt
 * @return array
 *
 */
function wppb_ul_exclude_fields_from_search( $fields, $userlisting_form_id ){
    $search_settings = get_post_meta( $userlisting_form_id, 'wppb_ul_search_settings', true );
    if( !empty( $search_settings ) ){
        $default_fields = array( 'user_login', 'user_email', 'user_url' );
        global $wppb_manage_fields;
        if( !isset( $wppb_manage_fields ) )
            $wppb_manage_fields = get_option( 'wppb_manage_fields', 'not_found' );
        $search_in_these_fields = array_map( 'trim', explode( ',', $search_settings[0]['search-fields'] ) );

        foreach ( $default_fields as $key => $value ){
            if( !in_array( $value, $search_in_these_fields ) )
                $fields[] = $value;

        }

        foreach ( $wppb_manage_fields as $key => $value ){
            if( !empty( $value['meta-name'] ) ) {
                if( !in_array( $value['meta-name'], $search_in_these_fields ) )
                    $fields[] = $value['meta-name'];
            }
        }

    }

    return $fields;
}


add_filter( "wck_before_listed_wppb_ul_fields_element_0", 'wppb_manage_fields_display_field_title_slug', 10, 3 );
add_filter( 'wck_update_container_class_wppb_ul_fields', 'wppb_update_container_class', 10, 4 );
add_filter( 'wck_element_class_wppb_ul_fields', 'wppb_element_class', 10, 4 );



/* Facet Settings Form change classes based on Facet Type field start */
add_filter( 'wck_update_container_class_wppb_ul_faceted_settings', 'wppb_ul_faceted_form_change_class_based_on_field_type', 10, 4 );
function wppb_ul_faceted_form_change_class_based_on_field_type($wck_update_container_css_class, $meta, $results, $counter ) {
    if( !empty( $results ) ){
        $ftype = Wordpress_Creation_Kit_PB::wck_generate_slug( $results[$counter]["facet-type"] );
        return 'class="update_container_'.$meta.' update_container_'.$ftype.' facet_'.$ftype.'"';
    }
}

add_filter( 'wck_element_class_wppb_ul_faceted_settings', 'wppb_ul_faceted_settings_element_type', 10, 4 );
function wppb_ul_faceted_settings_element_type( $element_class, $meta, $results, $element_id ){
    $wppb_element_type = Wordpress_Creation_Kit_PB::wck_generate_slug( $results[$element_id]["facet-type"] );
    return "class='facet_type_$wppb_element_type'";
}

/* Facet Settings Form change classes based on Facet Type field end */

// function to display an error message in the front end in case the shortcode was used but the userlisting wasn't activated
function wppb_list_all_users_display_error($atts){
	return apply_filters( 'wppb_not_addon_not_activated', '<p class="error">'.__( 'You need to activate the Userlisting feature from within the "Modules" tab!', 'profile-builder' ).'<br/>'.__( 'You can find it in the Profile Builder menu.', 'profile-builder' ).'</p>' );
}



//function to return to the userlisting page without the search parameters
function wppb_clear_results(){
	$args = array( 'searchFor', 'setSortingOrder', 'setSortingCriteria' );
	
	return remove_query_arg( $args );
}



//function to return the links for the sortable headers
function wppb_get_new_url( $criteria, $extra_info ){
	$set_new_sorting_criteria = ( ( isset( $_REQUEST['setSortingCriteria'] ) && ( $_REQUEST['setSortingCriteria'] == $criteria ) ) ? sanitize_text_field( $_REQUEST['setSortingCriteria'] ) : $criteria );
	
	$userlisting_settings = get_post_meta( $extra_info['userlisting_form_id'], 'wppb_ul_page_settings', true );
	$set_new_sorting_order = ( isset( $userlisting_settings[0]['default-sorting-order'] ) ? $userlisting_settings[0]['default-sorting-order'] : 'asc' );
	$set_new_sorting_order = ( ( isset( $_REQUEST['setSortingOrder'] ) && ( $_REQUEST['setSortingOrder'] == 'desc' ) ) ? 'asc' : 'desc' );
	
	$args = array( 'setSortingCriteria' => $set_new_sorting_criteria, 'setSortingOrder' => $set_new_sorting_order );	
	
	$searchText = apply_filters( 'wppb_userlisting_search_field_text', __( 'Search Users by All Fields', 'profile-builder' ) );

	if ( ( isset( $_REQUEST['searchFor'] ) ) && ( trim( $_REQUEST['searchFor'] ) != $searchText ) )
		$args['searchFor'] = sanitize_text_field( $_REQUEST['searchFor'] );

	return add_query_arg( $args );
}

//function that returns a class for the sort link depending on what sorting is selected
function wppb_get_sorting_class( $criteria ) {
    $output = '';

    if( isset( $_REQUEST['setSortingCriteria'] ) && ( $_REQUEST['setSortingCriteria'] == $criteria ) ) {
        if( isset( $_REQUEST['setSortingOrder'] ) && $_REQUEST['setSortingOrder'] == 'asc' ) {
            $output = 'sort-asc';
        } elseif( $_REQUEST['setSortingOrder'] == 'desc' ) {
            $output = 'sort-desc';
        }
    }

    return $output;
}

//function to render 404 page in case a user doesn't exist
function wppb_set404(){
	global $wp_query;
	global $wpdb;

    /* we should only do this if we are on a userlisting single page username query arg or $_GET['userID'] is set */
    $username_query_var = wppb_get_query_var( 'username' );
    if( isset($_GET['userID']) || ( !empty( $username_query_var ) && !isset( $_POST['username'] ) ) ){
        $arrayID = array();
        $nrOfIDs = 0;

        //check if certain users want their profile hidden
        $extraField_meta_key = apply_filters( 'wppb_display_profile_meta_field_name', '' );	//meta-name of the extra-field which checks if the user wants his profile hidden
        $extraField_meta_value = apply_filters( 'wppb_display_profile_meta_field_value', '' );	//the value of the above parameter; the users with these 2 combinations will be excluded

        if ( ( trim($extraField_meta_key) != '' ) && ( trim( $extraField_meta_value) != '' ) ){
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT wppb_t1.ID FROM $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = %s WHERE wppb_t2.meta_value LIKE %s ORDER BY wppb_t1.ID", $extraField_meta_key, '%'. $wpdb->esc_like(trim($extraField_meta_value)).'%' ) );
            if( !empty( $results ) ){
                foreach ($results as $result){
                    array_push($arrayID, $result->ID);
                }
            }
        }

        //if admin approval is activated, then give 404 if the user was manually requested
        $wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
        if( $wppb_generalSettings != 'not_found' )
            if( !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'yes' ){

                // Get term by name ''unapproved'' in user_status taxonomy.
                $user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
                if( $user_statusTaxID != false ){
                    $term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;

                    $results = $wpdb->get_results( $wpdb->prepare ( "SELECT wppb_t3.ID FROM $wpdb->users AS wppb_t3 LEFT OUTER JOIN $wpdb->term_relationships AS wppb_t4 ON wppb_t3.ID = wppb_t4.object_id WHERE wppb_t4.term_taxonomy_id = %d ORDER BY wppb_t3.ID", $term_taxonomy_id ) );
                    if( !empty( $results ) ){
                        foreach ($results as $result){
                            array_push($arrayID, $result->ID);
                        }
                    }
                }
            }

        $nrOfIDs=count($arrayID);

        //filter to get current user by either username or id(default); get user by username?
        $get_user_by_ID = apply_filters('wppb_userlisting_get_user_by_id', true);

        $invoke404 = false;

        //get user ID
        if (isset($_GET['userID'])){
            $userID = get_userdata( absint( $_GET['userID'] ) );
            if ( is_object( $userID ) ){
                if ( $nrOfIDs ){
                    if ( in_array( $userID->ID, $arrayID ) )
                        $invoke404 = true;
                }else{
                    $username = $userID->user_login;
                    $user = get_user_by('login', $username);
                    if ( ( $user === false ) || ( $user == null ) )
                        $invoke404 = true;
                }
            }
        }else{
            if ( $get_user_by_ID === true ){
                $userID = $username_query_var;
                if ($nrOfIDs){
                    if ( in_array( $userID, $arrayID ) )
                        $invoke404 = true;
                }else{
                    $user = get_userdata($userID);
                    if ( is_object( $user ) ){
                        $username = $user->user_login;
                        $user = get_user_by( 'login', $username );
                        if ( ( $userID !== '' ) && ( $user === false ) )
                            $invoke404 = true;
                    }
                    else
                        $invoke404 = true;
                }

            }else{
                $username = $username_query_var;
                $user = get_userdata($username);
                if ( is_object( $user ) ){
                    if ( $nrOfIDs ){
                        if ( in_array($user->ID, $arrayID ) )
                            $invoke404 = true;
                    }else{
                        if ( ( $username !== '' ) && ( $user === false ) )
                            $invoke404 = true;
                    }
                }
                else
                    $invoke404 = true;
            }
        }

        if ( $invoke404 )
            $wp_query->set_404();
    }
}
add_action('template_redirect', 'wppb_set404');


//function to handle the case when a search was requested but there were no results
function no_results_found_handler($content){

	$retContent = '';
	$formEnd = strpos( (string)$content, '</form>' );
	
	for ($i=0; $i<$formEnd+7; $i++){
		$retContent .= $content[$i];
	}
	
	return apply_filters( 'wppb_no_results_found_message', '<p class="noResults" id="noResults">'.__( 'No results found!', 'profile-builder' ) .'</p>' );
}


// flush_rules() if our rules are not yet included
function wppb_flush_rewrite_rules(){
	$rules = get_option( 'rewrite_rules' );

	if ( !isset( $rules['(.+?)/user/([^/]+)'] ) ){
		global $wp_rewrite;
	   	
		$wp_rewrite->flush_rules();
	}
}
add_action( 'wp_loaded', 'wppb_flush_rewrite_rules' );


// Adding a new rule
function wppb_insert_userlisting_rule( $rules ){
	$new_rule = array();
	
	$new_rule['(.+?)/user/([^/]+)'] = 'index.php?pagename=$matches[1]&username=$matches[2]';
	
	return $new_rule + $rules;
}
add_filter( 'rewrite_rules_array', 'wppb_insert_userlisting_rule' );


// Adding the username var so that WP recognizes it
function wppb_insert_query_vars( $vars ){
    global $wp;
    /**
     * only add this query var if we are not on the frontpage (when we have a form on a page that is set to static frontpage the page will redirect to the post archive
     * because it contains the username field) Having a post variable in the form that is also a registered query arg it will not work
     */
    if( $wp->did_permalink )
        array_push( $vars, 'username' );
	
    return $vars;
}
add_filter( 'query_vars', 'wppb_insert_query_vars' );


// Filter wp_title for single user listing
add_filter( 'wp_title', 'wppb_single_user_list_filter_wp_title', 99, 2 );
function wppb_single_user_list_filter_wp_title( $title, $sep ) {
    $userID = wppb_get_query_var('username');

    if ( empty( $userID ) )
        return $title;

    $user_object = new WP_User( $userID );

    if( !empty( $user_object->first_name ) || !empty( $user_object->last_name ) ) {
        $title .= ' ' . $sep . ' ';
        $title .= $user_object->first_name;

        if( !empty( $user_object->first_name ) ) {
            $title .= ' ';
            $title .= $user_object->last_name;
        }
    }

    return $title;
}

// Filter canonical url so profiles are indexed by google
add_filter( 'get_canonical_url', 'wppb_single_user_list_canonical_url', 99, 2 );
function wppb_single_user_list_canonical_url( $canonical_url, $post ) {
    $userID = wppb_get_query_var('username');

    if ( !empty( $userID ) )
        $canonical_url .= 'user/' . $userID;

    return $canonical_url;
}

//add description for google
add_filter( 'wpseo_metadesc', 'wppb_single_user_description_meta' );
if( !has_filter( 'wpseo_metadesc' ) )
    add_action( 'wp_head', 'wppb_single_user_description_meta' );
function wppb_single_user_description_meta( $description ) {
    $userID = wppb_get_query_var('username');

    if ( empty( $userID ) ){
        if( !empty( $description ) )
            return $description;
        else
            return;
    }

    $user_object = new WP_User( $userID );

    if( !empty( $user_object->description ) ) {
        if( current_filter() == 'wpseo_metadesc' )
            return $user_object->description;
        else
            echo '<meta property="og:description" content="' . $user_object->description . '" />';
    }
}

// Add body classes for userlisting when search or faceted are present
add_filter('body_class', 'ul_search_faceted_body_classes');
function ul_search_faceted_body_classes( $classes ){
    if( isset( $_REQUEST['searchFor'] ) )
        $classes[] = 'ul-search';
    if( !empty( $_REQUEST ) ){
        foreach( $_REQUEST as $request_key => $request_value ){
            if( strpos( $request_key, 'ul_filter_' ) === 0 ){
                $classes[] = 'ul-facet-filter';
            }
        }
    }

    return $classes;
}