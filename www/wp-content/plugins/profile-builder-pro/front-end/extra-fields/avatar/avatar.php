<?php
/* the avatar field relies on the upload field  */

/* handle field output */
function wppb_avatar_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	if ( $field['field'] == 'Avatar' ){

        $field['meta-name'] = Wordpress_Creation_Kit_PB::wck_generate_slug( $field['meta-name'] );

        /* media upload add here, this should be added just once even if called multiple times */
        wp_enqueue_media();
        /* propper way to dequeue. add to functions file in theme or custom plugin
         function wppb_dequeue_script() {
            wp_script_is( 'wppb-upload-script', 'enqueued' ); //true
            wp_dequeue_script( 'wppb-upload-script' );
        }
        add_action( 'get_footer', 'wppb_dequeue_script' );
         */
        wp_enqueue_script( 'wppb-upload-script', WPPB_PLUGIN_URL.'front-end/extra-fields/upload/upload.js', array('jquery'), PROFILE_BUILDER_VERSION, true );
        wp_enqueue_style( 'profile-builder-upload-css', WPPB_PLUGIN_URL.'front-end/extra-fields/upload/upload.css', false, PROFILE_BUILDER_VERSION );

        $item_title = apply_filters( 'wppb_'.$form_location.'_avatar_custom_field_'.$field['id'].'_item_title', wppb_icl_t( 'plugin profile-builder-pro', 'custom_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
		$item_description = wppb_icl_t( 'plugin profile-builder-pro', 'custom_field_'.$field['id'].'_description_translation', $field['description'] );

        if( $form_location != 'register' ) {
            if( empty( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) )
                $input_value = ( (wppb_user_meta_exists($user_id, $field['meta-name']) != null) ? get_user_meta($user_id, $field['meta-name'], true) : '');
            else
                $input_value = $request_data[wppb_handle_meta_name( $field['meta-name'] )];

            if( !empty( $input_value ) && !is_numeric( $input_value ) ){
                /* we have a file url and we need to change it into an attachment */
                // Check the type of file. We'll use this as the 'post_mime_type'.
                $wp_upload_dir = wp_upload_dir();
                $file_path = str_replace( $wp_upload_dir['baseurl'], $wp_upload_dir["basedir"], $input_value );
                //on windows os we might have \ instead of / so change them
                $file_path = str_replace( "\\", "/", $file_path );
                $file_type = wp_check_filetype( basename( $input_value ), null );
                $attachment = array(
                    'guid' => $input_value,
                    'post_mime_type' => $file_type['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $input_value ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                // Insert the attachment.
                $input_value = wp_insert_attachment( $attachment, $input_value, 0 );
                if( !empty( $input_value ) ) {
                    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    // Generate the metadata for the attachment, and update the database record.
                    $attach_data = wp_generate_attachment_metadata($input_value, $file_path);
                    wp_update_attachment_metadata($input_value, $attach_data);
                    /* save the new attachment instead of the url */
                    update_user_meta( $user_id, $field['meta-name'], $input_value );
                }
            }
        }
        else
            $input_value = !empty( $_POST[$field['meta-name']] ) ? sanitize_text_field( $_POST[$field['meta-name']] ) : '';

        if ( $form_location != 'back_end' ){
            $error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="wppb-required" title="'.wppb_required_field_error($field["field-title"]).'">*</span>' : '' );

            if ( array_key_exists( $field['id'], $field_check_errors ) )
                $error_mark = '<img src="'.WPPB_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.wppb_required_field_error($field["field-title"]).'"/>';

            $output = '<label for="'.$field['meta-name'].'">'.$item_title.$error_mark.'</label>';
            $output .= wppb_make_upload_button( $field, $input_value );
            if( !empty( $item_description ) )
                $output .= '<span class="wppb-description-delimiter">'.$item_description.'</span>';
        }else{
            $item_title = ( ( $field['required'] == 'Yes' ) ? $item_title .' <span class="description">('. __( 'required', 'profile-builder' ) .')</span>' : $item_title );
            $output = '
				<table class="form-table">
					<tr>
						<th><label for="'.$field['meta-name'].'">'.$item_title.'</label></th>
						<td>';
            $output .= wppb_make_upload_button( $field, $input_value );
            $output .='<br/><span class="wppb-description-delimiter">'.$item_description;
            $output .= '
						</td>
					</tr>
				</table>';
        }

		return apply_filters( 'wppb_'.$form_location.'_avatar_custom_field_'.$field['id'], $output, $form_location, $field, $user_id, $field_check_errors, $request_data, $input_value );
	}
}
add_filter( 'wppb_output_form_field_avatar', 'wppb_avatar_handler', 10, 6 );
add_filter( 'wppb_admin_output_form_field_avatar', 'wppb_avatar_handler', 10, 6 );


/* handle field save */
function wppb_save_avatar_value( $field, $user_id, $request_data, $form_location ){
	if( $field['field'] == 'Avatar' ){
        $field['meta-name'] = Wordpress_Creation_Kit_PB::wck_generate_slug( $field['meta-name'] );

        if ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ){
            update_user_meta( $user_id, $field['meta-name'], $request_data[wppb_handle_meta_name( $field['meta-name'] )] );
        }
	}
}
add_action( 'wppb_save_form_field', 'wppb_save_avatar_value', 10, 4 );
add_action( 'wppb_backend_save_form_field', 'wppb_save_avatar_value', 10, 4 );


/* handle field validation */
function wppb_check_avatar_value( $message, $field, $request_data, $form_location ){
	if( $field['field'] == 'Avatar' ){
        if( $field['required'] == 'Yes' ){
            $field['meta-name'] = Wordpress_Creation_Kit_PB::wck_generate_slug( $field['meta-name'] );

            if ( ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) && ( trim( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) == '' ) ) || !isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ){
                return wppb_required_field_error($field["field-title"]);
            }
        }
	}
    return $message;
}
add_filter( 'wppb_check_form_field_avatar', 'wppb_check_avatar_value', 10, 4 );


/* register image size defined in avatar field */
add_action( 'after_setup_theme', 'wppb_add_avatar_image_sizes' );
function wppb_add_avatar_image_sizes() {
    $all_fields = get_option('wppb_manage_fields');
    if( !empty( $all_fields ) ) {
        foreach ($all_fields as $field) {
            if( $field['field'] == 'Avatar' ) {
                if( !empty( $field['avatar-size'] ) )
                    add_image_size( 'wppb-avatar-size-'.$field['avatar-size'], $field['avatar-size'], $field['avatar-size'], true );
                else
                    add_image_size( 'wppb-avatar-size-100', 100, 100, true );

                add_image_size( 'wppb-avatar-size-64', 64, 64, true );
                add_image_size( 'wppb-avatar-size-26', 26, 26, true );
            }
        }
    }

    $userlisting_posts = get_posts( array( 'posts_per_page' => -1, 'post_status' =>'publish', 'post_type' => 'wppb-ul-cpt', 'orderby' => 'post_date', 'order' => 'ASC' ) );
    if( !empty( $userlisting_posts ) ){
        foreach ( $userlisting_posts as $post ){
            $this_form_settings = get_post_meta( $post->ID, 'wppb_ul_page_settings', true );
            $all_userlisting_avatar_size = apply_filters( 'all_userlisting_avatar_size', ( isset( $this_form_settings[0]['avatar-size-all-userlisting'] ) ? (int)$this_form_settings[0]['avatar-size-all-userlisting'] : 100 ) );
            $single_userlisting_avatar_size = apply_filters( 'single_userlisting_avatar_size', ( isset( $this_form_settings[0]['avatar-size-single-userlisting'] ) ? (int)$this_form_settings[0]['avatar-size-single-userlisting'] : 100 ) );

            add_image_size( 'wppb-avatar-size-'.$all_userlisting_avatar_size, $all_userlisting_avatar_size, $all_userlisting_avatar_size, true );
            add_image_size( 'wppb-avatar-size-'.$single_userlisting_avatar_size, $single_userlisting_avatar_size, $single_userlisting_avatar_size, true );
        }
    }
}