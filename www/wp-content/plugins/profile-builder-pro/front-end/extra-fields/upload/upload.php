<?php
/* handle field output */
function wppb_upload_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	if ( $field['field'] == 'Upload' ){

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

		$item_title = apply_filters( 'wppb_'.$form_location.'_upload_custom_field_'.$field['id'].'_item_title', wppb_icl_t( 'plugin profile-builder-pro', 'custom_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
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
		return apply_filters( 'wppb_'.$form_location.'_upload_custom_field_'.$field['id'], $output, $form_location, $field, $user_id, $field_check_errors, $request_data, $input_value );
	}
}
add_filter( 'wppb_output_form_field_upload', 'wppb_upload_handler', 10, 6 );
add_filter( 'wppb_admin_output_form_field_upload', 'wppb_upload_handler', 10, 6 );

function wppb_make_upload_button( $field, $input_value ){
    $upload_button = '';
    $upload_input_id = str_replace( '-', '_', Wordpress_Creation_Kit_PB::wck_generate_slug( $field['meta-name'] ) );

    /* container for the image preview (or file ico) and name and file type */
    if( !empty( $input_value ) ){
        /* it can hold multiple attachments separated by comma */
        $values = explode( ',', $input_value );
        foreach( $values as $value ) {
            if( !empty( $value ) && is_numeric( $value ) ){
                $thumbnail = wp_get_attachment_image($value, array(80, 80), true);
                $file_name = get_the_title($value);
                $file_type = get_post_mime_type($value);
                $attachment_url = wp_get_attachment_url($value);
                $upload_button .= '<div id="' . esc_attr($upload_input_id) . '_info_container" class="upload-field-details" data-attachment_id="' . $value . '">';
                $upload_button .= '<div class="file-thumb">';
                $upload_button .= "<a href='{$attachment_url}' target='_blank' class='wppb-attachment-link'>" . $thumbnail . "</a>";
                $upload_button .= '</div>';
                $upload_button .= '<p><span class="file-name">';
                $upload_button .= $file_name;
                $upload_button .= '</span><span class="file-type">';
                $upload_button .= $file_type;
                $upload_button .= '</span>';
                $upload_button .= '<span class="wppb-remove-upload" tabindex="0">' . __('Remove', 'profile-builder') . '</span>';
                $upload_button .= '</p></div>';
            }
        }
        $hide_upload_button = 'style="display:none;"';
    }
    else{
        $hide_upload_button = '';
    }

    $upload_button .= '<a href="#" class="button wppb_upload_button" id="upload_' . esc_attr(Wordpress_Creation_Kit_PB::wck_generate_slug($field['meta-name'], $field)) . '_button" '.$hide_upload_button.' data-uploader_title="' . $field["field-title"] . '" data-uploader_button_text="'. __( 'Select File', 'profile-builder' ) .'" data-upload_mn="'. $field['meta-name'] .'" data-upload_input="' . esc_attr($upload_input_id) . '"';

    if (is_user_logged_in())
        $upload_button .= 'data-uploader_logged_in="true"';
    $upload_button .= ' data-multiple_upload="false"';

    $upload_button .= '>' . __('Upload ', 'profile-builder') . '</a>';


    $upload_button .= '<input id="'. esc_attr( $upload_input_id ) .'" type="hidden" size="36" name="'. esc_attr( Wordpress_Creation_Kit_PB::wck_generate_slug( $field['meta-name'], $field ) ) .'" value="'. $input_value .'"/>';

    return $upload_button;
}

/* handle field save */
function wppb_save_upload_value( $field, $user_id, $request_data, $form_location ){
	if( $field['field'] == 'Upload' ){
		if ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ){
            update_user_meta( $user_id, $field['meta-name'], $request_data[wppb_handle_meta_name( $field['meta-name'] )] );

            // use this to update the post author to the correct user
            if( is_numeric( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ) {
                wp_update_post( array(
                    'ID'            => trim( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ),
                    'post_author'   => $user_id
                ) );
            }
		}
	}
}
add_action( 'wppb_save_form_field', 'wppb_save_upload_value', 10, 4 );
add_action( 'wppb_backend_save_form_field', 'wppb_save_upload_value', 10, 4 );

/* handle field validation */
function wppb_check_upload_value( $message, $field, $request_data, $form_location ){
	if( $field['field'] == 'Upload' ){
        if( $field['required'] == 'Yes' ){
            if ( ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) && ( trim( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) == '' ) ) || !isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ){
                return wppb_required_field_error($field["field-title"]);
            }
        }
	}

    return $message;
}
add_filter( 'wppb_check_form_field_upload', 'wppb_check_upload_value', 10, 4 );





