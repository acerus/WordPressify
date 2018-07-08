<?php

$isLanguagesAvailableGoogle = $languagesGoogleTranslateFrom && $languagesGoogleTranslateTo;
$optionsLoadLanguagesButtonGoogle = [
    'class'                 => 'google',
    'isLanguagesAvailable'  => $isLanguagesAvailableGoogle,
    'data' => [
        'selectors' => [
            'project_id' => '#_wpcc_translation_google_translate_project_id',
            'api_key'    => '#_wpcc_translation_google_translate_api_key',
        ],
        'serviceType' => \WPCCrawler\objects\translation\TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION,
        'requestType' => 'load_refresh_translation_languages',
    ],
];

$isLanguagesAvailableMicrosoft = $languagesMicrosoftTranslatorTextFrom && $languagesMicrosoftTranslatorTextTo;
$optionsLoadLanguagesButtonMicrosoft = [
    'class'                 => 'microsoft',
    'isLanguagesAvailable'  => $isLanguagesAvailableMicrosoft,
    'data' => [
        'selectors' => [
            'client_secret' => '#_wpcc_translation_microsoft_translate_client_secret',
        ],
        'serviceType' => \WPCCrawler\objects\translation\TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT,
        'requestType' => 'load_refresh_translation_languages',
    ],
];

$optionsRefreshLanguagesLabel = [
    'title' => _wpcc('Refresh languages'),
    'info'  => _wpcc('Refresh languages by retrieving them from the API. By this way, if there are new languages, you can get them.')
];

$videoUrlGoogleCloudTranslationAPI = 'https://www.youtube.com/watch?v=imQd2pGj7-o';
$videoUrlMicrosoftTranslatorTextAPI = 'https://www.youtube.com/watch?v=VHZIQcctixY';

?>

<div class="wcc-settings-title">
    <h3>{{ _wpcc('Translation') }}</h3>
    <span>{{ _wpcc('Set content translation options') }}</span>
</div>

<table class="wcc-settings">

    @if($isGeneralPage)
        {{-- TRANSLATION IS ACTIVE --}}
        <tr>
            <td>
                @include('form-items/label', [
                    'for'   =>  '_wpcc_is_translation_active',
                    'title' =>  _wpcc('Translation is active?'),
                    'info'  =>  _wpcc('If you want to activate automated content translation, check this. Note that
                            translating will increase the time required to crawl a post.')
                ])
            </td>
            <td>
                @include('form-items/checkbox', [
                    'name' => '_wpcc_is_translation_active',
                ])
            </td>
        </tr>
    @endif

    {{-- TRANSLATE WITH --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_selected_translation_service',
                'title' =>  _wpcc('Translate with'),
                'info'  =>  _wpcc('Select the translation service you want to use to translate contents. You also need
                    to properly configure the settings of the selected API below.')
            ])
        </td>
        <td>
            @include('form-items/select', [
                'name'      =>  '_wpcc_selected_translation_service',
                'options'   =>  $translationServices,
                'isOption'  =>  $isOption,
            ])
        </td>
    </tr>

    {{-- SECTION: GOOGLE TRANSLATE OPTIONS --}}
    @include('partials.table-section-title', ['title' => _wpcc("Google Cloud Translation Options")])

    {{-- GOOGLE TRANSLATE - TRANSLATE FROM --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_google_translate_from',
                'title' =>  _wpcc('Translate from'),
                'info'  =>  _wpcc('Select the language of the content of crawled posts.')
            ])
        </td>
        <td>
            @if($isLanguagesAvailableGoogle)
                @include('form-items/select', [
                    'name'      =>  '_wpcc_translation_google_translate_from',
                    'options'   =>  $languagesGoogleTranslateFrom,
                    'isOption'  =>  $isOption,
                ])
            @else
                @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButtonGoogle + ['id' => '_wpcc_translation_google_translate_from'])
            @endif
        </td>
    </tr>

    {{-- GOOGLE TRANSLATE - TRANSLATE TO --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_google_translate_to',
                'title' =>  _wpcc('Translate to'),
                'info'  =>  _wpcc('Select the language to which the content should be translated.')
            ])
        </td>
        <td>
            @if($isLanguagesAvailableGoogle)
                @include('form-items/select', [
                    'name'      =>  '_wpcc_translation_google_translate_to',
                    'options'   =>  $languagesGoogleTranslateTo,
                    'isOption'  =>  $isOption,
                ])
            @else
                @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButtonGoogle + ['id' => '_wpcc_translation_google_translate_to'])
            @endif
        </td>
    </tr>

    {{-- GOOGLE TRANSLATE - REFRESH LANGUAGES --}}
    @if($isLanguagesAvailableGoogle)
        <tr>
            <td>
                @include('form-items/label', $optionsRefreshLanguagesLabel)
            </td>
            <td>
                @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButtonGoogle)
            </td>
        </tr>
    @endif

    {{-- GOOGLE TRANSLATE - PROJECT ID --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_google_translate_project_id',
                'title' =>  _wpcc('Project ID'),
                'info'  =>  _wpcc('Project ID retrieved from Google Cloud Console.') . ' ' . _wpcc_trans_how_to_get_it($videoUrlGoogleCloudTranslationAPI)
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name' => '_wpcc_translation_google_translate_project_id',
            ])
        </td>
    </tr>

    {{-- GOOGLE TRANSLATE - API KEY --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_google_translate_api_key',
                'title' =>  _wpcc('API Key'),
                'info'  =>  _wpcc('API key retrieved from Google Cloud Console.') . ' ' . _wpcc_trans_how_to_get_it($videoUrlGoogleCloudTranslationAPI)
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name' => '_wpcc_translation_google_translate_api_key',
            ])
        </td>
    </tr>

    {{-- GOOGLE TRANSLATE - TEST --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_google_translate_test',
                'title' =>  _wpcc('Test Google Translate Options'),
                'info'  =>  _wpcc('You can write any text to test Google Translate options you configured.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_wpcc_translation_google_translate_test',
                'placeholder'   =>  _wpcc('Test text to translate...'),
                'data'          =>  [
                    'apiKeySelector'    => '#_wpcc_translation_google_translate_api_key',
                    'projectIdSelector' => '#_wpcc_translation_google_translate_project_id',
                    'fromSelector'      => '#_wpcc_translation_google_translate_from',
                    'toSelector'        => '#_wpcc_translation_google_translate_to',
                    'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_TRANSLATION,
                    'serviceType'       =>  \WPCCrawler\objects\translation\TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION,
                    'requiredSelectors' =>  "#_wpcc_translation_google_translate_test & #_wpcc_translation_google_translate_api_key & #_wpcc_translation_google_translate_project_id & #_wpcc_translation_google_translate_from & #_wpcc_translation_google_translate_to"
                ],
                'addon'         =>  'dashicons dashicons-search',
                'test'          =>  true,
                'addonClasses'  => 'wcc-test-translation google-translate',
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- SECTION: MICROSOFT TRANSLATOR TEXT OPTIONS --}}
    @include('partials.table-section-title', ['title' => _wpcc("Microsoft Translator Text Options")])

    {{-- MICROSOFT TRANSLATOR TEXT - TRANSLATE FROM --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_microsoft_translate_from',
                'title' =>  _wpcc('Translate from'),
                'info'  =>  _wpcc('Select the language of the content of crawled posts.')
            ])
        </td>
        <td>
            @if($isLanguagesAvailableMicrosoft)
                @include('form-items/select', [
                    'name'      => '_wpcc_translation_microsoft_translate_from',
                    'options'   => $languagesMicrosoftTranslatorTextFrom,
                    'isOption'  => $isOption,
                ])
            @else
                @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButtonMicrosoft + ['id' => '_wpcc_translation_microsoft_translate_from'])
            @endif
        </td>
    </tr>

    {{-- MICROSOFT TRANSLATOR TEXT - TRANSLATE TO --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_microsoft_translate_to',
                'title' =>  _wpcc('Translate to'),
                'info'  =>  _wpcc('Select the language to which the content should be translated.')
            ])
        </td>
        <td>
            @if($isLanguagesAvailableMicrosoft)
                @include('form-items/select', [
                    'name'      =>  '_wpcc_translation_microsoft_translate_to',
                    'options'   =>  $languagesMicrosoftTranslatorTextTo,
                    'isOption'  =>  $isOption,
                ])
            @else
                @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButtonMicrosoft + ['id' => '_wpcc_translation_microsoft_translate_to'])
            @endif
        </td>
    </tr>

    {{-- MICROSOFT TRANSLATOR TEXT - REFRESH LANGUAGES --}}
    @if($isLanguagesAvailableMicrosoft)
        <tr>
            <td>
                @include('form-items/label', $optionsRefreshLanguagesLabel)
            </td>
            <td>
                @include('form-items/partials/button-load-languages', $optionsLoadLanguagesButtonMicrosoft)
            </td>
        </tr>
    @endif

    {{-- MICROSOFT TRANSLATOR TEXT - CLIENT SECRET --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_microsoft_translate_client_secret',
                'title' =>  _wpcc('Client Secret'),
                'info'  =>  _wpcc('Client secret retrieved from Microsoft Azure Portal.') . ' ' . _wpcc_trans_how_to_get_it($videoUrlMicrosoftTranslatorTextAPI)
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name' => '_wpcc_translation_microsoft_translate_client_secret',
            ])
        </td>
    </tr>

    {{-- MICROSOFT TRANSLATOR TEXT - TEST --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_translation_microsoft_translate_test',
                'title' =>  _wpcc('Test Microsoft Translator Text Options'),
                'info'  =>  _wpcc('You can write any text to test Microsoft Translator Text options you configured.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_wpcc_translation_microsoft_translate_test',
                'placeholder'   =>  _wpcc('Test text to translate...'),
                'data'          =>  [
                    'clientSecretSelector'  => '#_wpcc_translation_microsoft_translate_client_secret',
                    'fromSelector'          => '#_wpcc_translation_microsoft_translate_from',
                    'toSelector'            => '#_wpcc_translation_microsoft_translate_to',
                    'testType'              =>  \WPCCrawler\objects\Test::$TEST_TYPE_TRANSLATION,
                    'serviceType'           =>  \WPCCrawler\objects\translation\TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT,
                    'requiredSelectors'     =>  "#_wpcc_translation_microsoft_translate_test & #_wpcc_translation_microsoft_translate_client_secret & #_wpcc_translation_microsoft_translate_from & #_wpcc_translation_microsoft_translate_to"
                ],
                'addon'         =>  'dashicons dashicons-search',
                'test'          =>  true,
                'addonClasses'  => 'wcc-test-translation microsoft-translator-text',
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    <?php

    /**
     * Fires before closing table tag in translation tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/translation', $settings, $isGeneralPage, $isOption);

    ?>

</table>