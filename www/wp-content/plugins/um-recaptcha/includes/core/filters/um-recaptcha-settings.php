<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@extend settings
	***/
add_filter( 'um_settings_structure', 'um_recaptcha_settings', 10, 1 );

function um_recaptcha_settings( $settings ) {
    $key = ! empty( $settings['extensions']['sections'] ) ? 'recaptcha' : '';
    $settings['extensions']['sections'][$key] = array(
        'title'     => __( 'Google reCAPTCHA','um-recaptcha'),
        'fields'    => array(
            array(
                'id'       		=> 'g_recaptcha_status',
                'type'     		=> 'checkbox',
                'label'   		=> __( 'Enable Google reCAPTCHA','um-recaptcha' ),
                'tooltip' 	   		=> __('Turn on or off your Google reCAPTCHA on your site registration and login forms by default.','um-recaptcha'),
            ),

            array(
                'id'       		=> 'g_recaptcha_sitekey',
                'type'     		=> 'text',
                'label'   		=> __( 'Site Key','um-recaptcha' ),
                'tooltip' 	   		=> __('You can register your site and generate a site key via <a href="https://www.google.com/recaptcha/">Google reCAPTCHA</a>','um-recaptcha'),
                'size'      => 'medium',
            ),

            array(
                'id'       		=> 'g_recaptcha_secretkey',
                'type'     		=> 'text',
                'label'   		=> __( 'Secret Key','um-recaptcha' ),
                'tooltip' 	   		=> __('Keep this a secret. You can get your secret key via <a href="https://www.google.com/recaptcha/">Google reCAPTCHA</a>','um-recaptcha'),
                'size'      => 'medium',
            ),
            array(
                'id'       		=> 'g_recaptcha_type',
                'type'     		=> 'select',
                'label'   		=> __( 'Type','um-recaptcha' ),
                'tooltip' 	   		=> __('The type of reCAPTCHA to serve.','um-recaptcha'),
                'options' 		=> array(
                    'audio'    		 => 'Audio',
                    'image'			 => 'Image'
                ),
                'size'      => 'small',
            ),
            array(
                'id'       		=> 'g_recaptcha_language_code',
                'type'     		=> 'select',
                'label'   		=> __( 'Language','um-recaptcha' ),
                'tooltip' 	   		=> __('Select the language to be used in your reCAPTCHA.','um-recaptcha'),
                'options' 		=> array(
                    'ar'     => 'Arabic',
                    'af'     => 'Afrikaans',
                    'am'     => 'Amharic',
                    'hy'     => 'Armenian',
                    'az'     => 'Azerbaijani',
                    'eu'     => 'Basque',
                    'bn'     => 'Bengali',
                    'bg'     => 'Bulgarian',
                    'ca'     => 'Catalan',
                    'zh-HK'  => 'Chinese (Hong Kong)',
                    'zh-CN'  => 'Chinese (Simplified)',
                    'zh-TW'  => 'Chinese (Traditional)',
                    'hr'     => 'Croatian',
                    'cs'     => 'Czech',
                    'da'     => 'Danish',
                    'nl'     => 'Dutch',
                    'en-GB'  => 'English (UK)',
                    'en'     => 'English (US)',
                    'et'     => 'Estonian',
                    'fil'    => 'Filipino',
                    'fi'     => 'Finnish',
                    'fr'     => 'French',
                    'fr-CA'  => 'French (Canadian)',
                    'gl'     => 'Galician',
                    'ka'     => 'Georgian',
                    'de'     => 'German',
                    'de-AT'  => 'German (Austria)',
                    'de-CH'  => 'German (Switzerland)',
                    'el'     => 'Greek',
                    'gu'     => 'Gujarati',
                    'iw'     => 'Hebrew',
                    'hi'     => 'Hindi',
                    'hu'     => 'Hungarain',
                    'is'     => 'Icelandic',
                    'id'     => 'Indonesian',
                    'it'     => 'Italian',
                    'ja'     => 'Japanese',
                    'kn'     => 'Kannada',
                    'ko'     => 'Korean',
                    'lo'     => 'Laothian',
                    'lv'     => 'Latvian',
                    'lt'     => 'Lithuanian',
                    'ms'     => 'Malay',
                    'ml'     => 'Malayalam',
                    'mr'     => 'Marathi',
                    'mn'     => 'Mongolian',
                    'no'     => 'Norwegian',
                    'fa'     => 'Persian',
                    'pl'     => 'Polish',
                    'pt'     => 'Portuguese',
                    'pt-BR'  => 'Portuguese (Brazil)',
                    'pt-PT'  => 'Portuguese (Portugal)',
                    'ro'     => 'Romanian',
                    'ru'     => 'Russian',
                    'sr'     => 'Serbian',
                    'si'     => 'Sinhalese',
                    'sk'     => 'Slovak',
                    'sl'     => 'Slovenian',
                    'es'     => 'Spanish',
                    'es-419' => 'Spanish (Latin America)',
                    'sw'     => 'Swahili',
                    'sv'     => 'Swedish',
                    'ta'     => 'Tamil',
                    'te'     => 'Telugu',
                    'th'     => 'Thai',
                    'tr'     => 'Turkish',
                    'uk'     => 'Ukrainian',
                    'ur'     => 'Urdu',
                    'vi'     => 'Vietnamese',
                    'zu'     => 'Zulu'
                ),
                'size'      => 'small',
            ),
            array(
                'id'       		=> 'g_recaptcha_theme',
                'type'     		=> 'select',
                'label'   		=> __( 'Theme','um-recaptcha' ),
                'tooltip' 	   		=> __('Select a color theme of the widget.','um-recaptcha'),
                'options' 		=> array(
                    'dark'     => 'Dark',
                    'light'			 => 'Light'
                ),
                'size'      => 'small',
            ),
            array(
                'id'       		=> 'g_recaptcha_size',
                'type'     		=> 'select',
                'label'   		=> __( 'Size','um-recaptcha' ),
                'tooltip' 	   		=> __('The type of reCAPTCHA to serve.','um-recaptcha'),
                'options' 		=> array(
                    'compact'     		 => 'Compact',
                    'normal'			 => 'Normal',
                    'invisible'		 	=> 'Invisible'
                ),
                'size'      => 'small',
            )
        )
    );

    return $settings;
}
