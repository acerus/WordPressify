<div class="wcc-settings-title">
    <h3>{{ _wpcc('Category Page Settings') }}</h3>
    <span>{{ _wpcc('Category mapping and other settings to be used when crawling category pages') }}</span>
</div>

<table class="wcc-settings">
    {{-- CATEGORY LIST PAGE URL --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_category_list_page_url',
                'title' => _wpcc('Category List Page URL'),
                'info'  => _wpcc('The URL to get category links from. The page should include a container having category URLs.
                    This will be used to automatically insert category URLs for category map.'),
            ])
        </td>
        <td>@include('form-items/text', ['name' => '_category_list_page_url', 'type' => 'url'])</td>
    </tr>

    {{-- CATEGORY LIST URL SELECTOR --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_category_list_url_selectors',
                'title' =>  _wpcc('Category List URL Selectors'),
                'info'  =>  _wpcc('CSS selectors for category links. This is used to get category URLs automatically for category map.
                    Gets "href" attributes of "a" tags. E.g. <span class="highlight selector">.top-level-navigation ul > li > a</span>.
                    Before using the insert button, make sure you filled the category list page URL.')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/text',
                'name'          => '_category_list_url_selectors',
                'addon'         =>  'dashicons dashicons-plus',
                'addonTitle'    =>  _wpcc('Find and add category links for mapping'),
                'addonClasses'  =>  'wcc-category-map',
                'data'          =>  [
                    'urlSelector'   =>  "#_category_list_page_url",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_HREF,
                    'targetTag'     =>  'a',
                ],
                'test'          => true,
                'inputClass'    => 'css-selector',
                'showDevTools'  => true,
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- CATEGORY MAP --}}
    <tr>
        <td>
            @include('form-items/label', [
                'title' =>  _wpcc('Category Map'),
                'info'  =>  _wpcc('Map the categories to target site\'s URLs. You can write the URLs relative to the
                    main site URL. E.g. <span class="highlight url">/category/art</span>. Category URLs should
                    be added once, no duplicates allowed. <b>Note that</b> changing category map will clear the
                    post URLs waiting to be saved.')
            ])
        </td>
        <td id="category-map">
            @include('form-items/multiple', [
                'include'       =>  'form-items/category-map',
                'name'          =>  '_category_map',
                'placeholder'   =>  _wpcc('Category URL'),
                'categories'    =>  $categories,
                'data'          =>  [
                    'urlSelector'       =>  "input",
                    'closest_inside'    =>  true,
                    'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_HREF,
                ],
                'addKeys'       =>  true,
            ])
        </td>
    </tr>

    {{-- TEST CATEGORY URL --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_test_url_category',
                'title' =>  _wpcc('Test Category URL'),
                'info'  =>  _wpcc('A full category URL to be used to perform the tests for category page CSS selectors.')
            ])
        </td>
        <td>@include('form-items/text', ['name' => '_test_url_category', 'type' => 'url'])</td>
    </tr>

    {{-- CATEGORY POST URL SELECTOR --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_category_post_link_selectors',
                'title' =>  _wpcc('Category Post URL Selectors'),
                'info'  =>  _wpcc('CSS selectors for the post URLs in category pages. Gets "href" attributes of "a" tags.
                    E.g. <span class="highlight selector">article.post > h2 > a</span>. When testing, make sure you
                    filled the category test URL. If you give more than one selector, each selector will be used
                    to get URLs and the results will be combined.')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/text',
                'name'          => '_category_post_link_selectors',
                'addon'         => 'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'   =>  "#_test_url_category",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_HREF,
                    'targetTag'     =>  'a',
                ],
                'test'          => true,
                'inputClass'    => 'css-selector',
                'showDevTools'  => true,
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- COLLECT URLS IN REVERSE ORDER--}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_category_collect_in_reverse_order',
                'title' =>  _wpcc('Collect URLs in reverse order?'),
                'info'  =>  _wpcc('When you check this, the URLs found by URL selectors will be ordered in reverse before
                        they are saved into the database. Therefore, the posts will be saved in reverse order for
                        each category page.')
            ])
        </td>
        <td>
            <div class="inputs">
                @include('form-items/checkbox', [
                    'name' => '_category_collect_in_reverse_order',
                ])
            </div>
        </td>
    </tr>

    {{-- SECTION: NEXT PAGE --}}
    @include('partials.table-section-title', ['title' => _wpcc("Next Page")])

    {{-- CATEGORY NEXT PAGE URL SELECTORS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_category_next_page_selectors',
                'title' => _wpcc('Category Next Page URL Selectors'),
                'info'  => _wpcc('CSS selectors for next page URL in a category page. Gets "href" attributes of "a" tags.
                    E.g. <span class="highlight selector">.pagination > a.next</span>. When testing, make sure you
                    filled the category test URL. If you give more than one selector, the first
                    match will be used.')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/selector-with-attribute',
                'name'          => '_category_next_page_selectors',
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'           =>  "#_test_url_category",
                    'testType'              =>  \WPCCrawler\objects\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                    'attr'                  =>  'href',
                    'targetTag'             =>  'a',
                    'targetCssSelectors'    => ['link[rel="next"]']
                ],
                'test'          => true,
                'addKeys'       => true,
                'addonClasses'  => 'wcc-test-selector-attribute',
                'defaultAttr'   => 'href',
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- SECTION: FEATURED IMAGES --}}
    @include('partials.table-section-title', ['title' => _wpcc("Featured Images")])

    {{-- SAVE THUMBNAILS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_category_post_save_thumbnails',
                'class' =>  'label-thumbnail',
                'title' =>  _wpcc('Save featured images?'),
                'info'  =>  _wpcc('If there are featured images for each post on category page and you want to
                    save the featured images for the posts, check this.')
            ])
        </td>
        <td>
            <div class="inputs">
                @include('form-items/checkbox', [
                    'name'          => '_category_post_save_thumbnails',
                    'dependants'    => '[
                            "#category-post-thumbnail-selectors",
                            "#category-thumbnail-test-url",
                            "#category-thumbnail-find-replace",
                            "#category-post-link-is-before-thumbnail"
                        ]'
                ])
            </div>
        </td>
    </tr>

    {{-- CATEGORY POST THUMBNAIL SELECTORS --}}
    <tr id="category-post-thumbnail-selectors">
        <td>
            @include('form-items/label', [
                'for'   => '_category_post_thumbnail_selectors',
                'class' =>  'label-thumbnail',
                'title' => _wpcc('Featured Image Selectors'),
                'info'  => _wpcc('CSS selectors for post featured images in a category page. Gets "src" attributes of "img" tags.
                    E.g. <span class="highlight selector">.post-item > img</span>. When testing, make sure you
                    filled the category test URL. If you give more than one selector, the first match will be used.')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/text',
                'name'          => '_category_post_thumbnail_selectors',
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'   =>  "#_test_url_category",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_SRC,
                    'targetTag'     =>  'img',
                ],
                'test'          => true,
                'inputClass'    => 'css-selector',
                'showDevTools'  => true,
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- CATEGORY TEST THUMBNAIL IMAGE URL --}}
    <tr id="category-thumbnail-test-url">
        <td>
            @include('form-items/label', [
                'for'   =>  '_test_find_replace_thumbnail_url_cat',
                'class' =>  'label-thumbnail',
                'title' =>  _wpcc('Test Featured Image URL'),
                'info'  =>  _wpcc('A full image URL to be used to perform tests for the find-replace settings
                    for featured image URL.')
            ])
        </td>
        <td>@include('form-items/text', ['name' => '_test_find_replace_thumbnail_url_cat'])</td>
    </tr>

    {{-- CATEGORY FIND AND REPLACE FOR THUMBNAIL URL --}}
    <tr id="category-thumbnail-find-replace">
        <td>
            @include('form-items/label', [
                'for'   => '_category_find_replace_thumbnail_url',
                'class' =>  'label-thumbnail',
                'title' => _wpcc("Find and replace in featured image URL"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>URL of the
                    featured image</b>, this is the place. The replacement will be done before saving the image.') . " " . _wpcc_trans_regex()
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       =>  'form-items/find-replace',
                'name'          =>  '_category_find_replace_thumbnail_url',
                'addKeys'       =>  true,
                'remove'        =>  true,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'subjectSelector'   =>  "#_test_find_replace_thumbnail_url_cat",
                    'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_FIND_REPLACE,
                ],
                'test'          => true,
                'addonClasses'  => 'wcc-test-find-replace'
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- CATEGORY POST LINK IS BEFORE THUMBNAIL --}}
    <tr id="category-post-link-is-before-thumbnail">
        <td>
            @include('form-items/label', [
                'for'   =>  '_category_post_is_link_before_thumbnail',
                'class' =>  'label-thumbnail',
                'title' =>  _wpcc('Post links come before featured images?'),
                'info'  =>  _wpcc("If the links for the posts in the category page come before the featured images,
                    considering the position of the featured image and link in the HTML of the page, check this.")
            ])
        </td>
        <td>
            <div class="inputs">
                @include('form-items/checkbox', [
                    'name'          => '_category_post_is_link_before_thumbnail',
                ])
            </div>
        </td>
    </tr>

    {{-- MANIPULATE HTML --}}
    @include('site-settings.partial.html-manipulation-inputs', [
        "keyTestUrl"                        => "_test_url_category",
        "keyTestFindReplace"                => "_test_find_replace_first_load_cat",
        "keyFindReplaceFirstLoad"           => "_category_find_replace_first_load",
        "keyFindReplaceElementAttributes"   => "_category_find_replace_element_attributes",
        "keyExchangeElementAttributes"      => "_category_exchange_element_attributes",
        "keyRemoveElementAttributes"        => "_category_remove_element_attributes",
        "keyFindReplaceElementHtml"         => "_category_find_replace_element_html"
    ])

    {{-- SECTION: UNNECESSARY ELEMENTS --}}
    @include('partials.table-section-title', ['title' => _wpcc("Unnecessary Elements")])

    {{-- UNNECESSARY CATEGORY ELEMENT SELECTORS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'title' =>  _wpcc('Unnecessary Element Selectors'),
                'info'  =>  _wpcc('CSS selectors for unwanted elements in the category page. Specified elements will be
                    removed from the HTML of the page. Content extraction will be done after the page is cleared
                    from unnecessary elements.')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/text',
                'name'          => '_category_unnecessary_element_selectors',
                'addon'         =>  'dashicons dashicons-search',
                'addonTitle'    =>  'test',
                'data'          =>  [
                    'urlSelector'   =>  "#_test_url_category",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_HTML,
                ],
                'test'          => true,
                'inputClass'    => 'css-selector',
                'showDevTools'  => true,
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- SECTION: NOTIFICATIONS --}}
    @include('partials.table-section-title', ['title' => _wpcc("Notifications")])

    {{-- EMPTY VALUE NOTIFICATION --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_category_notify_empty_value_selectors',
                'title' => _wpcc('CSS selectors for empty value notification'),
                'info'  => _wpcc('Write CSS selectors and their attributes you want to retrieve. If the retrieved value
                        is empty, you will be notified via email. These CSS selectors will be tried to be retrieved
                        after all replacements are applied.')
            ])
        </td>
        <td>
            @include('form-items/multiple', [
                'include'       => 'form-items/selector-with-attribute',
                'name'          => '_category_notify_empty_value_selectors',
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'   =>  "#_test_url_category",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                    'attr'          =>  'text'
                ],
                'test'          => true,
                'addKeys'       => true,
                'addonClasses'  => 'wcc-test-selector-attribute',
                'defaultAttr'   => 'text',
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    <?php

    /**
     * Fires before closing table tag in category tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/category', $settings, $postId);

    ?>

</table>