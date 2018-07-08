<?php

/**
 * @param string $name  Post meta key
 * @return array
 */
function _wpcc_prepare_find_replace_form_item_data($name) {
    return [
        'include'       =>  'form-items/find-replace',
        'name'          =>  $name,
        'addKeys'       =>  true,
        'remove'        =>  true,
        'addon'         =>  'dashicons dashicons-search',
        'data'          =>  [
            'subjectSelector'   =>  "#_test_find_replace",
            'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_FIND_REPLACE,
        ],
        'test'          => true,
        'addonClasses'  => 'wcc-test-find-replace'
    ];
}

?>

<div class="wcc-settings-title">
    <h3>{{ _wpcc('Template Settings') }}</h3>
    <span>{{ _wpcc('Set templates for the post, find and replace things...') }}</span>
</div>

<table class="wcc-settings">
    {{-- POST MAIN TEMPLATE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_template_main',
                'title' => _wpcc('Main Post Template'),
                'info'  => _wpcc('Main template used for the posts. The buttons above the editor holds short codes which
                            are used to place certain elements into the post page. You can hover over the buttons
                            to see what they are used to show in post page, and <b>click them to copy the code</b>. After
                            copying, just place the short codes into anywhere you want in the editor. <b>You must
                            fill the template.<b>')
            ])
        </td>
        <td>@include('form-items/template-editor', ['name' => '_post_template_main', 'buttons' => $buttonsMain])</td>
    </tr>

    {{-- POST TITLE TEMPLATE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_template_title',
                'title' => _wpcc('Post Title Template'),
                'info'  => _wpcc('Template for post title. You can also use custom short codes. If you leave this empty,
                        original post title found by CSS selectors will be used.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'      => '_post_template_title',
                'buttons'   => $buttonsTitle,
                'rows'      => 3,
            ])
        </td>
    </tr>

    {{-- POST EXCERPT TEMPLATE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_template_excerpt',
                'title' => _wpcc('Post Excerpt Template'),
                'info'  => _wpcc('Template for post excerpt. You can also use custom short codes. If you leave this empty,
                        original post excerpt found by CSS selectors will be used.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'      => '_post_template_excerpt',
                'buttons'   => $buttonsExcerpt,
                'rows'      => 3,
            ])
        </td>
    </tr>

    {{-- POST LIST TEMPLATE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_template_list_item',
                'title' => _wpcc('List Item Template'),
                'info'  => _wpcc('This template is used for the list. If you set the post list type and wrote some selectors
                            for the list items, then the list items will be crawled. Here, you can set a template
                            to be used for <b>each</b> list item. You can include the entire list in main post
                            template. <b>You must fill the template if you expect a list from the target page.</b>')
            ])
        </td>
        <td>@include('form-items/template-editor', ['name' => '_post_template_list_item', 'buttons' => $buttonsList])</td>
    </tr>

    {{-- POST GALLERY ITEM TEMPLATE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_template_gallery_item',
                'title' => _wpcc('Gallery Item Template'),
                'info'  => _wpcc('This template is used for the gallery. If you activated saving images as gallery
                            and wrote some selectors for the image URLs, then the gallery items will be crawled.
                            Here, you can set a template to be used for <b>each</b> gallery image. You can
                            include the entire gallery in main post template. <b>You must fill the template if
                            you expect a gallery from the target page.</b>')
            ])
        </td>
        <td>@include('form-items/template-editor', ['name' => '_post_template_gallery_item', 'buttons' => $buttonsGallery])</td>
    </tr>

    {{-- SECTION: REMOVE LINKS --}}
    @include('partials.table-section-title', ['title' => _wpcc("Remove Links")])

    {{-- REMOVE LINKS FROM SHORT CODES --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_post_remove_links_from_short_codes',
                'title' =>  _wpcc('Remove links from short codes?'),
                'info'  =>  sprintf(_wpcc('If you want to remove links from all of the short code data, check this.
                        Checking this box is almost the same as adding <b>%1$s</b> regex for find and <b>%2$s</b> for
                        replace option for each find and replace option in this tab. This option will not touch custom
                        links inside the templates.'),
                        esc_html(trim(\WPCCrawler\objects\crawling\PostBot::$REMOVE_LINKS_FIND, '/')),
                        esc_html(\WPCCrawler\objects\crawling\PostBot::$REMOVE_LINKS_REPLACE)
                )
            ])
        </td>
        <td>
            <div class="inputs">
                @include('form-items/checkbox', [
                    'name' => '_post_remove_links_from_short_codes',
                ])
            </div>
        </td>
    </tr>

    {{-- SECTION: UNNECESSARY ELEMENTS --}}
    @include('partials.table-section-title', ['title' => _wpcc("Unnecessary Elements")])

    {{-- UNNECESSARY TEMPLATE ELEMENT SELECTORS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'title' =>  _wpcc('Template Unnecessary Element Selectors'),
                'info'  =>  _wpcc('CSS selectors for unwanted elements in the template. Specified elements will be
                    removed from the HTML of the template. The removal will be done after the shortcodes are replaced.
                    Find-and-replaces will be done after the template is cleared from unnecessary elements. <b>This
                    will use test post URL on Post tab to conduct the tests.</b>')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/text',
                'name'          => '_template_unnecessary_element_selectors',
                'addon'         =>  'dashicons dashicons-search',
                'addonTitle'    =>  'test',
                'data'          =>  [
                    'urlSelector'   =>  "#_test_url_post",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_HTML,
                ],
                'test'          => true,
                'inputClass'    => 'css-selector',
                'showDevTools'  => true,
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- SECTION: MANIPULATE HTML --}}
    @include('partials.table-section-title', ['title' => _wpcc("Manipulate HTML")])

    {{-- FIND AND REPLACE TEST CODE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_test_find_replace',
                'title' =>  _wpcc('Find and Replace Test Code'),
                'info'  =>  _wpcc('A piece of code to be used when testing find-and-replace settings below.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_test_find_replace',
                'placeholder'   =>  _wpcc('The code which will be used to test find-and-replace settings'),
            ])
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR TEMPLATE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_template',
                'title' => _wpcc("Find and replace in post's content"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>HTML of post\'s content</b>,
                    this is the place. The replacement will be done after the final post template is ready. You
                    can write plain text or regular expressions. When writing regex, do not put slashes before
                    and after the expression, and make sure the checkbox is checked. You can test your regex
                    <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_template'))
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR CUSTOM SHORT CODES --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_custom_shortcodes',
                'title' => _wpcc("Find and replace in custom short code contents"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>each custom short
                    code\'s content</b>, this is the place. The replacement will be done after the final post template
                    is ready. You can write plain text or regular expressions. When writing regex, do not put slashes
                    before and after the expression, and make sure the checkbox is checked. You can test your regex
                    <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_custom_shortcodes'))
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR TITLE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_title',
                'title' => _wpcc("Find and replace in post's title"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s title</b>,
                    this is the place. You can write plain text or regular expressions. When writing regex, do
                    not put slashes before and after the expression, and make sure the checkbox is checked. You
                    can test your regex <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_title'))
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR EXCERPT --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_excerpt',
                'title' => _wpcc("Find and replace in post's excerpt"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s excerpt</b>,
                    this is the place. You can write plain text or regular expressions. When writing regex, do
                    not put slashes before and after the expression, and make sure the checkbox is checked. You
                    can test your regex <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_excerpt'))
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR TAGS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_tags',
                'title' => _wpcc("Find and replace in post's each tag"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s each tag</b>,
                    this is the place. You can write plain text or regular expressions. When writing regex, do
                    not put slashes before and after the expression, and make sure the checkbox is checked. You
                    can test your regex <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_tags'))
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR META KEYWORDS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_meta_keywords',
                'title' => _wpcc("Find and replace in meta keywords"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s meta keywords</b>,
                    this is the place. You can write plain text or regular expressions. When writing regex, do
                    not put slashes before and after the expression, and make sure the checkbox is checked. You
                    can test your regex <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_meta_keywords'))
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- POST FIND REPLACE FOR META DESCRIPTION --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_find_replace_meta_description',
                'title' => _wpcc("Find and replace in meta description"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s meta description</b>,
                    this is the place. You can write plain text or regular expressions. When writing regex, do
                    not put slashes before and after the expression, and make sure the checkbox is checked. You
                    can test your regex <a href="https://regex101.com/" target="_blank">here</a>.')
            ])
        </td>
        <td>
            @include('form-items.multiple', _wpcc_prepare_find_replace_form_item_data('_post_find_replace_meta_description'))
            @include('partials/test-result-container')
        </td>
    </tr>

    <?php

    /**
     * Fires before closing table tag in templates tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/templates', $settings, $postId);

    ?>

</table>