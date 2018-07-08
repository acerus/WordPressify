<div class="wcc-settings-title">
    <h3>{{ _wpcc('Advanced') }}</h3>
    <span>{{ _wpcc('Advanced settings for crawler') }}</span>
</div>

<table class="wcc-settings">
    {{-- MAKE SURE ENCODING IS UTF8 --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_make_sure_encoding_utf8',
                'title' =>  _wpcc('Always use UTF8 encoding?'),
                'info'  =>  _wpcc('If you want to crawl all pages in UTF-8 encoding, check this.')
            ])
        </td>
        <td>
            @include('form-items/checkbox', [
                'name'      =>  '_wpcc_make_sure_encoding_utf8',
            ])
        </td>
    </tr>

    {{-- HTTP USER AGENT --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_http_user_agent',
                'title' =>  _wpcc('HTTP User Agent'),
                'info'  =>  _wpcc('The user agent to be used when crawling, i.e.
                <span class="highlight variable">HTTP_USER_AGENT</span>. If you leave this empty, the default value
                will be used. You can find user agent strings
                <a target="_blank" href="http://www.useragentstring.com/pages/useragentstring.php">here</a>.')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_http_user_agent',
            ])
        </td>
    </tr>

    {{-- HTTP ACCEPT --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_http_accept',
                'title' =>  _wpcc('HTTP Accept'),
                'info'  =>  _wpcc('HTTP accept value to be used when crawling, i.e.
                    <span class="highlight variable">HTTP_ACCEPT</span>. If you leave this empty, the default value
                    will be used.')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_http_accept',
            ])
        </td>
    </tr>

    {{-- HTTP ALLOW COOKIES --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_http_allow_cookies',
                'title' =>  _wpcc('Allow cookies?'),
                'info'  =>  _wpcc('If you want to allow cookies when crawling, check this.')
            ])
        </td>
        <td>
            @include('form-items/checkbox', [
                'name'      =>  '_wpcc_http_allow_cookies',
            ])
        </td>
    </tr>

    {{-- CONNECTION TIMEOUT --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_connection_timeout',
                'title' =>  _wpcc('Connection timeout (in seconds)'),
                'info'  =>  _wpcc('Maximum number of seconds in which target server should response. Write 0 to disable.
                        Default: 0')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_connection_timeout',
                'type'      =>  'number',
                'min'       =>  0
            ])
        </td>
    </tr>

    {{-- USE PROXY --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_use_proxy',
                'title' =>  _wpcc('Use proxy?'),
                'info'  =>  _wpcc('If you want to use a proxy when crawling the target site, check this.')
            ])
        </td>
        <td>
            @include('form-items/checkbox', [
                'name'          =>  '_wpcc_use_proxy',
                'dependants'    =>  '["#proxy-test-url", "#proxies", "#proxy-try-limit", "#proxy-randomize"]',
            ])
        </td>
    </tr>

    {{-- TEST URL FOR PROXY --}}
    <tr id="proxy-test-url">
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_test_url_proxy',
                'title' =>  _wpcc('URL for proxy testing'),
                'info'  =>  _wpcc('A URL to be used to perform the proxy test.')
            ])
        </td>
        <td>@include('form-items/text', ['name' => '_wpcc_test_url_proxy', 'type' => 'url'])</td>
    </tr>

    {{-- PROXIES --}}
    <tr id="proxies">
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_proxies',
                'title' =>  _wpcc('Proxies'),
                'info'  =>  _wpcc('You can write your proxies here. Write every proxy in a new line. If you want to
                        use a proxy specifically with a protocol, write the proxy with its protocol. E.g.
                        <span class="highlight proxy">https://192.168.16.1:10</span>, or
                        <span class="highlight proxy">http://192.168.16.1:10</span>. You can also provide proxies
                        that contain a scheme, username and password. E.g.
                        <span class="highlight proxy">http://username:password@192.168.16.1:10</span>. If you do not
                        specify the protocol, TCP will be used. SOCKS is not supported.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_wpcc_proxies',
                'placeholder'   =>  _wpcc('New line-separated proxies...'),
                'data'          =>  [
                    'urlSelector'   =>  "#_wpcc_test_url_proxy",
                    'testType'      =>  \WPCCrawler\objects\Test::$TEST_TYPE_PROXY,
                ],
                'addon'         =>  'dashicons dashicons-search',
                'test'          =>  true,
                'addonClasses'  => 'wcc-test-proxy',
            ])
            @include('partials/test-result-container')
        </td>
    </tr>

    {{-- PROXY TRY LIMIT --}}
    <tr id="proxy-try-limit">
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_proxy_try_limit',
                'title' =>  _wpcc('Proxy try limit'),
                'info'  =>  _wpcc('Maximum number of proxies that can be tried for one request. Write 0 for no limitation.
                        Default: 0')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_proxy_try_limit',
                'type'      =>  'number',
                'min'       =>  0
            ])
        </td>
    </tr>

    {{-- RANDOMIZE --}}
    <tr id="proxy-randomize">
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_proxy_randomize',
                'title' =>  _wpcc('Randomize proxies'),
                'info'  =>  _wpcc('When you check this, the proxies you entered will be randomized. This means, the order
                    of the proxies will be changed every time before a new request is made. If you do not check this,
                    the proxies will be tried in the order you entered them.')
            ])
        </td>
        <td>
            @include('form-items/checkbox', [
                'name'          =>  '_wpcc_proxy_randomize'
            ])
        </td>
    </tr>

    <?php

    /**
     * Fires before closing table tag in advanced tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/advanced', $settings, $isGeneralPage, $isOption);

    ?>

</table>