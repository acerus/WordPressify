<h2>
    <span>{{ _wpcc('Manually crawl and save a post') }}</span>
    @include('partials/button-toggle-info-texts')
</h2>
<div class="inside">
    <form action="" class="tool-form">
        {{--{!! wp_nonce_field('wcc-tools', \WPCCrawler\Constants::$NONCE_NAME) !!}--}}

        @include('partials.form-nonce-and-action')

        <input type="hidden" name="tool_type" value="save_post">

        <div class="panel-wrap wcc-settings-meta-box">

            <table class="wcc-settings">
                {{-- SITE --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_site_id',
                            'title' =>  _wpcc('Site'),
                            'info'  =>  _wpcc('Select the site for the post you want to save.'),
                        ])
                    </td>
                    <td>
                        @include('form-items/select', [
                            'name'      =>  '_wpcc_tools_site_id',
                            'options'   =>  $sites,
                        ])
                    </td>
                </tr>

                {{-- CATEGORY --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_category_id',
                            'title' =>  _wpcc('Category'),
                            'info'  =>  _wpcc('Select the category in which you want the post saved.'),
                        ])
                    </td>
                    <td>
                        @include('form-items/select', [
                            'name'      =>  '_wpcc_tools_category_id',
                            'options'   =>  $categories,
                        ])
                    </td>
                </tr>

                {{-- POST URL --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_post_url',
                            'title' =>  _wpcc('Post URL'),
                            'info'  =>  _wpcc('Full URL for the post you want to save.'),
                        ])
                    </td>
                    <td>
                        @include('form-items/text', [
                            'name'      =>  '_wpcc_tools_post_url',
                            'type'      =>  'url',
                            'required'  =>  true,
                        ])
                    </td>
                </tr>

                {{-- FEATURED IMAGE URL --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_featured_image_url',
                            'title' =>  _wpcc('Featured Image URL'),
                            'info'  =>  _wpcc('Full URL for the featured image of the post, if there is a featured image.')
                        ])
                    </td>
                    <td>
                        @include('form-items/text', [
                            'name'  =>  '_wpcc_tools_featured_image_url',
                            'type'  =>  'url'
                        ])
                    </td>
                </tr>

            </table>

            @include('form-items/submit-button', [
                'text'  =>  _wpcc('Crawl and Save')
            ])

            @include('partials/test-result-container')
        </div>
    </form>
</div>