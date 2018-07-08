<?php
/* handle field output */
function wppb_country_select_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
    if ( $field['field'] == 'Select (Country)' ){
        $item_title = apply_filters( 'wppb_'.$form_location.'_country_select_custom_field_'.$field['id'].'_item_title', wppb_icl_t( 'plugin profile-builder-pro', 'custom_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
        $item_description = wppb_icl_t( 'plugin profile-builder-pro', 'custom_field_'.$field['id'].'_description_translation', $field['description'] );

        $country_array = wppb_country_select_options( $form_location );

		$old_country_array = array( __( 'Afghanistan', 'profile-builder'), __( 'Aland Islands', 'profile-builder' ), __( 'Albania', 'profile-builder' ), __( 'Algeria', 'profile-builder' ), __( 'American Samoa', 'profile-builder' ), __( 'Andorra', 'profile-builder' ), __( 'Angola', 'profile-builder' ), __( 'Anguilla', 'profile-builder' ), __( 'Antarctica', 'profile-builder' ), __( 'Antigua and Barbuda', 'profile-builder' ), __( 'Argentina', 'profile-builder' ), __( 'Armenia', 'profile-builder' ), __( 'Aruba', 'profile-builder' ), __( 'Australia', 'profile-builder' ), __( 'Austria', 'profile-builder' ), __( 'Azerbaijan', 'profile-builder' ), __( 'Bahamas', 'profile-builder' ), __( 'Bahrain', 'profile-builder' ), __( 'Bangladesh', 'profile-builder' ), __( 'Barbados', 'profile-builder' ), __( 'Belarus', 'profile-builder' ), __( 'Belgium', 'profile-builder' ), __( 'Belize', 'profile-builder' ), __( 'Benin', 'profile-builder' ), __( 'Bermuda', 'profile-builder' ), __( 'Bhutan', 'profile-builder' ), __( 'Bolivia, __( Plurinational State of', 'profile-builder' ), __( 'Bonaire, __( Sint Eustatius and Saba', 'profile-builder' ), __( 'Bosnia and Herzegovina', 'profile-builder' ), __( 'Botswana', 'profile-builder' ), __( 'Bouvet Island', 'profile-builder' ), __( 'Brazil', 'profile-builder' ), __( 'British Indian Ocean Territory', 'profile-builder' ), __( 'Brunei Darussalam', 'profile-builder' ), __( 'Bulgaria', 'profile-builder' ), __( 'Burkina Faso', 'profile-builder' ), __( 'Burundi', 'profile-builder' ), __( 'Cambodia', 'profile-builder' ), __( 'Cameroon', 'profile-builder' ), __( 'Canada', 'profile-builder' ), __( 'Cabo Verde', 'profile-builder' ), __( 'Cayman Islands', 'profile-builder' ), __( 'Central African Republic', 'profile-builder' ), __( 'Chad', 'profile-builder' ), __( 'Chile', 'profile-builder' ), __( 'China', 'profile-builder' ), __( 'Christmas Island', 'profile-builder' ), __( 'Cocos (Keeling) Islands', 'profile-builder' ), __( 'Colombia', 'profile-builder' ), __( 'Comoros', 'profile-builder' ), __( 'Congo', 'profile-builder' ), __( 'Congo, __( the Democratic Republic of the', 'profile-builder' ), __( 'Cook Islands', 'profile-builder' ), __( 'Costa Rica', 'profile-builder' ), __( 'Cote dIvoire', 'profile-builder' ), __( 'Croatia', 'profile-builder' ), __( 'Cuba', 'profile-builder' ), __( 'Curacao', 'profile-builder' ), __( 'Cyprus', 'profile-builder' ), __( 'Czech Republic', 'profile-builder' ), __( 'Denmark', 'profile-builder' ), __( 'Djibouti', 'profile-builder' ), __( 'Dominica', 'profile-builder' ), __( 'Dominican Republic', 'profile-builder' ), __( 'Ecuador', 'profile-builder' ), __( 'Egypt', 'profile-builder' ), __( 'El Salvador', 'profile-builder' ), __( 'Equatorial Guinea', 'profile-builder' ), __( 'Eritrea', 'profile-builder' ), __( 'Estonia', 'profile-builder' ), __( 'Ethiopia', 'profile-builder' ), __( 'Falkland Islands (Malvinas)', 'profile-builder' ), __( 'Faroe Islands', 'profile-builder' ), __( 'Fiji', 'profile-builder' ), __( 'Finland', 'profile-builder' ), __( 'France', 'profile-builder' ), __( 'French Guiana', 'profile-builder' ), __( 'French Polynesia', 'profile-builder' ), __( 'French Southern Territories', 'profile-builder' ), __( 'Gabon', 'profile-builder' ), __( 'Gambia', 'profile-builder' ), __( 'Georgia', 'profile-builder' ), __( 'Germany', 'profile-builder' ), __( 'Ghana', 'profile-builder' ), __( 'Gibraltar', 'profile-builder' ), __( 'Greece', 'profile-builder' ), __( 'Greenland', 'profile-builder' ), __( 'Grenada', 'profile-builder' ), __( 'Guadeloupe', 'profile-builder' ), __( 'Guam', 'profile-builder' ), __( 'Guatemala', 'profile-builder' ), __( 'Guernsey', 'profile-builder' ), __( 'Guinea', 'profile-builder' ), __( 'Guinea-Bissau', 'profile-builder' ), __( 'Guyana', 'profile-builder' ), __( 'Haiti', 'profile-builder' ), __( 'Heard Island and McDonald Islands', 'profile-builder' ), __( 'Holy See (Vatican City State)', 'profile-builder' ), __( 'Honduras', 'profile-builder' ), __( 'Hong Kong', 'profile-builder' ), __( 'Hungary', 'profile-builder' ), __( 'Iceland', 'profile-builder' ), __( 'India', 'profile-builder' ), __( 'Indonesia', 'profile-builder' ), __( 'Iran, __( Islamic Republic of', 'profile-builder' ), __( 'Iraq', 'profile-builder' ), __( 'Ireland', 'profile-builder' ), __( 'Isle of Man', 'profile-builder' ), __( 'Israel', 'profile-builder' ), __( 'Italy', 'profile-builder' ), __( 'Jamaica', 'profile-builder' ), __( 'Japan', 'profile-builder' ), __( 'Jersey', 'profile-builder' ), __( 'Jordan', 'profile-builder' ), __( 'Kazakhstan', 'profile-builder' ), __( 'Kenya', 'profile-builder' ), __( 'Kiribati', 'profile-builder' ), __( 'Korea, __( Democratic Peoples Republic of', 'profile-builder' ), __( 'Korea, __( Republic of', 'profile-builder' ), __( 'Kuwait', 'profile-builder' ), __( 'Kyrgyzstan', 'profile-builder' ), __( 'Lao Peoples Democratic Republic', 'profile-builder' ), __( 'Latvia', 'profile-builder' ), __( 'Lebanon', 'profile-builder' ), __( 'Lesotho', 'profile-builder' ), __( 'Liberia', 'profile-builder' ), __( 'Libya', 'profile-builder' ), __( 'Liechtenstein', 'profile-builder' ), __( 'Lithuania', 'profile-builder' ), __( 'Luxembourg', 'profile-builder' ), __( 'Macao', 'profile-builder' ), __( 'Macedonia, __( the former Yugoslav Republic of', 'profile-builder' ), __( 'Madagascar', 'profile-builder' ), __( 'Malawi', 'profile-builder' ), __( 'Malaysia', 'profile-builder' ), __( 'Maldives', 'profile-builder' ), __( 'Mali', 'profile-builder' ), __( 'Malta', 'profile-builder' ), __( 'Marshall Islands', 'profile-builder' ), __( 'Martinique', 'profile-builder' ), __( 'Mauritania', 'profile-builder' ), __( 'Mauritius', 'profile-builder' ), __( 'Mayotte', 'profile-builder' ), __( 'Mexico', 'profile-builder' ), __( 'Micronesia, __( Federated States of', 'profile-builder' ), __( 'Moldova, __( Republic of', 'profile-builder' ), __( 'Monaco', 'profile-builder' ), __( 'Mongolia', 'profile-builder' ), __( 'Montenegro', 'profile-builder' ), __( 'Montserrat', 'profile-builder' ), __( 'Morocco', 'profile-builder' ), __( 'Mozambique', 'profile-builder' ), __( 'Myanmar', 'profile-builder' ), __( 'Namibia', 'profile-builder' ), __( 'Nauru', 'profile-builder' ), __( 'Nepal', 'profile-builder' ), __( 'Netherlands', 'profile-builder' ), __( 'New Caledonia', 'profile-builder' ), __( 'New Zealand', 'profile-builder' ), __( 'Nicaragua', 'profile-builder' ), __( 'Niger', 'profile-builder' ), __( 'Nigeria', 'profile-builder' ), __( 'Niue', 'profile-builder' ), __( 'Norfolk Island', 'profile-builder' ), __( 'Northern Mariana Islands', 'profile-builder' ), __( 'Norway', 'profile-builder' ), __( 'Oman', 'profile-builder' ), __( 'Pakistan', 'profile-builder' ), __( 'Palau', 'profile-builder' ), __( 'Palestine, __( State of', 'profile-builder' ), __( 'Panama', 'profile-builder' ), __( 'Papua New Guinea', 'profile-builder' ), __( 'Paraguay', 'profile-builder' ), __( 'Peru', 'profile-builder' ), __( 'Philippines', 'profile-builder' ), __( 'Pitcairn', 'profile-builder' ), __( 'Poland', 'profile-builder' ), __( 'Portugal', 'profile-builder' ), __( 'Puerto Rico', 'profile-builder' ), __( 'Qatar', 'profile-builder' ), __( 'Reunion', 'profile-builder' ), __( 'Romania', 'profile-builder' ), __( 'Russian Federation', 'profile-builder' ), __( 'Rwanda', 'profile-builder' ), __( 'Saint Barthelemy', 'profile-builder' ), __( 'Saint Helena, __( Ascension and Tristan da Cunha', 'profile-builder' ), __( 'Saint Kitts and Nevis', 'profile-builder' ), __( 'Saint Lucia', 'profile-builder' ), __( 'Saint Martin (French part)', 'profile-builder' ), __( 'Saint Pierre and Miquelon', 'profile-builder' ), __( 'Saint Vincent and the Grenadines', 'profile-builder' ), __( 'Samoa', 'profile-builder' ), __( 'San Marino', 'profile-builder' ), __( 'Sao Tome and Principe', 'profile-builder' ), __( 'Saudi Arabia', 'profile-builder' ), __( 'Senegal', 'profile-builder' ), __( 'Serbia', 'profile-builder' ), __( 'Seychelles', 'profile-builder' ), __( 'Sierra Leone', 'profile-builder' ), __( 'Singapore', 'profile-builder' ), __( 'Sint Maarten (Dutch part)', 'profile-builder' ), __( 'Slovakia', 'profile-builder' ), __( 'Slovenia', 'profile-builder' ), __( 'Solomon Islands', 'profile-builder' ), __( 'Somalia', 'profile-builder' ), __( 'South Africa', 'profile-builder' ), __( 'South Georgia and the South Sandwich Islands', 'profile-builder' ), __( 'South Sudan', 'profile-builder' ), __( 'Spain', 'profile-builder' ), __( 'Sri Lanka', 'profile-builder' ), __( 'Sudan', 'profile-builder' ), __( 'Suriname', 'profile-builder' ), __( 'Svalbard and Jan Mayen', 'profile-builder' ), __( 'Swaziland', 'profile-builder' ), __( 'Sweden', 'profile-builder' ), __( 'Switzerland', 'profile-builder' ), __( 'Syrian Arab Republic', 'profile-builder' ), __( 'Taiwan, __( Province of China', 'profile-builder' ), __( 'Tajikistan', 'profile-builder' ), __( 'Tanzania, __( United Republic of', 'profile-builder' ), __( 'Thailand', 'profile-builder' ), __( 'Timor-Leste', 'profile-builder' ), __( 'Togo', 'profile-builder' ), __( 'Tokelau', 'profile-builder' ), __( 'Tonga', 'profile-builder' ), __( 'Trinidad and Tobago', 'profile-builder' ), __( 'Tunisia', 'profile-builder' ), __( 'Turkey', 'profile-builder' ), __( 'Turkmenistan', 'profile-builder' ), __( 'Turks and Caicos Islands', 'profile-builder' ), __( 'Tuvalu', 'profile-builder' ), __( 'Uganda', 'profile-builder' ), __( 'Ukraine', 'profile-builder' ), __( 'United Arab Emirates', 'profile-builder' ), __( 'United Kingdom', 'profile-builder' ), __( 'United States', 'profile-builder' ), __( 'United States Minor Outlying Islands', 'profile-builder' ), __( 'Uruguay', 'profile-builder' ), __( 'Uzbekistan', 'profile-builder' ), __( 'Vanuatu', 'profile-builder' ), __( 'Venezuela, __( Bolivarian Republic of', 'profile-builder' ), __( 'Viet Nam', 'profile-builder' ), __( 'Virgin Islands, __( British', 'profile-builder' ), __( 'Virgin Islands, __( U.S.', 'profile-builder' ), __( 'Wallis and Futuna', 'profile-builder' ), __( 'Western Sahara', 'profile-builder' ), __( 'Yemen', 'profile-builder' ), __( 'Zambia', 'profile-builder' ), __( 'Zimbabwe', 'profile-builder' ) );

		$extra_attr = apply_filters( 'wppb_extra_attribute', '', $field, $form_location );

		if( $form_location != 'register' ) {
			// change current user country meta_value with country ISO code
			$user_country_option = wppb_user_meta_exists( $user_id, $field['meta-name'] );

			if( $user_country_option != null ) {
				if( in_array( $user_country_option->meta_value, $old_country_array ) ) {
					$country_iso = array_search( $user_country_option->meta_value, $country_array );

					update_user_meta( $user_id, $field['meta-name'], $country_iso );
				}
			}

			$input_value = ( ( $user_country_option != null ) ? $country_array[stripslashes( get_user_meta( $user_id, $field['meta-name'], true ) )] : $country_array[$field['default-option-country']] );
		} else {
			$input_value = ( ! empty( $field['default-option-country'] ) ? $country_array[trim( $field['default-option-country'] )] : '' );
		}

        $input_value = ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ? $country_array[trim( $request_data[wppb_handle_meta_name( $field['meta-name'] )] )] : $input_value );

        if ( $form_location != 'back_end' ){
            $error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="wppb-required" title="'.wppb_required_field_error($field["field-title"]).'">*</span>' : '' );

            if ( array_key_exists( $field['id'], $field_check_errors ) )
                $error_mark = '<img src="'.WPPB_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.wppb_required_field_error($field["field-title"]).'"/>';

            $output = '
				<label for="'.$field['meta-name'].'">'.$item_title.$error_mark.'</label>
				<select name="'.$field['meta-name'].'" id="'.$field['meta-name'].'" class="custom_field_country_select '. apply_filters( 'wppb_fields_extra_css_class', '', $field ) .'" '. $extra_attr .'>';

			$extra_select_option = apply_filters( 'wppb_extra_select_option', '', $field, $item_title );
			if( ! empty( $extra_select_option ) ) {
				$output .= $extra_select_option;
				$country_array = array_filter( $country_array );
			}

            foreach( $country_array as $iso => $country ){
                $output .= '<option value="'.$iso.'"';

                if ( $input_value === $country )
                    $output .= ' selected';

                $output .= '>'.$country.'</option>';
            }

            $output .= '
				</select>';
            if( !empty( $item_description ) )
                $output .= '<span class="wppb-description-delimiter">'.$item_description.'</span>';

        }else{
            $output = '
				<table class="form-table">
					<tr>
						<th><label for="'.$field['meta-name'].'">'.$item_title.'</label></th>
						<td>
							<select name="'.$field['meta-name'].'" class="custom_field_country_select" id="'.$field['meta-name'].'" '. $extra_attr .'>';

            foreach( $country_array as $iso => $country ){
                $output .= '<option value="'.$iso.'"';

                if ( $input_value === $country )
                    $output .= ' selected';

                $output .= '>'.$country.'</option>';
            }

            $output .= '</select>
							<span class="description">'.$item_description.'</span>
						</td>
					</tr>
				</table>';
        }

        return apply_filters( 'wppb_'.$form_location.'_country_select_custom_field_'.$field['id'], $output, $form_location, $field, $user_id, $field_check_errors, $request_data, $input_value );
    }
}
add_filter( 'wppb_output_form_field_select-country', 'wppb_country_select_handler', 10, 6 );
add_filter( 'wppb_admin_output_form_field_select-country', 'wppb_country_select_handler', 10, 6 );


/* handle field save */
function wppb_save_country_select_value( $field, $user_id, $request_data, $form_location ){
    if( $field['field'] == 'Select (Country)' ){
        if ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) )
            update_user_meta( $user_id, $field['meta-name'], $request_data[wppb_handle_meta_name( $field['meta-name'] )] );
    }
}
add_action( 'wppb_save_form_field', 'wppb_save_country_select_value', 10, 4 );
add_action( 'wppb_backend_save_form_field', 'wppb_save_country_select_value', 10, 4 );


/* handle field validation */
function wppb_check_country_select_value( $message, $field, $request_data, $form_location ){
    if( $field['field'] == 'Select (Country)' ){
        if( $field['required'] == 'Yes' ){
            if ( ( isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) && ( trim( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) == '' ) ) || !isset( $request_data[wppb_handle_meta_name( $field['meta-name'] )] ) ){
                return wppb_required_field_error($field["field-title"]);
            }
        }
    }

    return $message;
}
add_filter( 'wppb_check_form_field_select-country', 'wppb_check_country_select_value', 10, 4 );