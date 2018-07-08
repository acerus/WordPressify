<div class="wcc-settings-title">
    <h3>{{ _wpcc('SEO') }}</h3>
    <span>{{ _wpcc('Set meta keywords and description keys') }}</span>
</div>

<table class="wcc-settings">
    {{-- META KEYWORDS KEY --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_meta_keywords_meta_key',
                'title' =>  _wpcc('Meta Keywords Key'),
                'info'  =>  _wpcc('Set the key under which the meta keywords are saved. This key depends
                    on your SEO plugin. If you do not set a key, meta keywords will not be saved. If you use
                    Yoast SEO, this key is <span class="highlight variable">_yoast_wpseo_metakeys</span>')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_meta_keywords_meta_key',
                'isOption'  =>  $isOption,
            ])
        </td>
    </tr>

    {{-- META DESCRIPTION KEY --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_meta_description_meta_key',
                'title' =>  _wpcc('Meta Description Key'),
                'info'  =>  _wpcc('Set the key under which the meta description is saved. This key depends
                    on your SEO plugin. If you do not set a key, meta description will not be saved. If you use
                    Yoast SEO, this key is <span class="highlight variable">_yoast_wpseo_metadesc</span>')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_meta_description_meta_key',
                'isOption'  =>  $isOption,
            ])
        </td>
    </tr>

    {{-- FIND AND REPLACE TEST CODE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_test_find_replace',
                'title' =>  _wpcc('Find and Replace Test Code'),
                'info'  =>  _wpcc('A piece of code to be used when testing find-and-replace settings below.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_wpcc_test_find_replace',
                'placeholder'   =>  _wpcc('The code which will be used to test find-and-replace settings'),
            ])
        </td>
    </tr>

    {{-- FIND REPLACE FOR ALL POST PAGES --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_wpcc_find_replace',
                'title' => _wpcc("Find and replace in all post pages"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>HTML of
                    the post page</b>, this is the place. The replacements will be done after the HTML of target page
                    is loaded, and <i>before the replacements (for HTML of the page at first load) of the site
                    being crawled is applied</i>. So, these are the
                    first replacements that will be done before everything else. <b>These replacements will be
                    applied to all of the active sites.</b>') . " " . _wpcc_trans_regex()
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       =>  'form-items/find-replace',
                'name'          =>  '_wpcc_find_replace',
                'addKeys'       =>  true,
                'remove'        =>  true,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'subjectSelector'   =>  "#_wpcc_test_find_replace",
                    'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_FIND_REPLACE,
                ],
                'test'          => true,
                'addonClasses'  => 'wcc-test-find-replace'
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    <?php

    /**
     * Fires before closing table tag in SEO tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/seo', $settings, $isGeneralPage, $isOption);

    ?>

</table>