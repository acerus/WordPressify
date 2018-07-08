<h2>
    <span>{{ _wpcc("Clear URLs") }}</span>
    @include('partials/button-toggle-info-texts')
</h2>
<div class="inside">
    <form action="" class="tool-form">
        {{--{!! wp_nonce_field('wcc-tools', \WPCCrawler\Constants::$NONCE_NAME) !!}--}}

        @include('partials.form-nonce-and-action')
        <input type="hidden" name="tool_type" value="delete_urls">

        <div class="panel-wrap wcc-settings-meta-box">

            <table class="wcc-settings">
                {{-- SITE --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_site_id',
                            'title' =>  _wpcc('Site'),
                            'info'  =>  _wpcc('Select the site whose URLs you want to be deleted from the database.'),
                        ])
                    </td>
                    <td>
                        @include('form-items/select', [
                            'name'      =>  '_wpcc_tools_site_id',
                            'options'   =>  $sites,
                        ])
                    </td>
                </tr>

                {{-- URL TYPE --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_url_type',
                            'title' =>  _wpcc('URL Type'),
                            'info'  =>  _wpcc('Select URL types to be cleared for the specified site. If you clear the URLs
                                waiting in the queue, those URLs will not be saved, unless they are collected again. If you
                                clear already-saved URLs, those URLs may end up in the queue again, and they may be saved
                                as posts again. So, you may want to delete the posts as well, unless you want duplicate content.'),
                        ])
                    </td>
                    <td>
                        @include('form-items/select', [
                            'name'      =>  '_wpcc_tools_url_type',
                            'options'   =>  $urlTypes,
                        ])
                    </td>
                </tr>

                {{-- SAFETY CHECK --}}
                <tr>
                    <td>
                        @include('form-items/label', [
                            'for'   =>  '_wpcc_tools_safety_check',
                            'title' =>  _wpcc("I'm sure"),
                            'info'  =>  _wpcc('Check this to indicate that you are sure about this.'),
                        ])
                    </td>
                    <td>
                        @include('form-items/checkbox', [
                            'name'      =>  '_wpcc_tools_safety_check',
                        ])
                    </td>
                </tr>
            </table>

            @include('form-items/submit-button', [
                'text'  =>  _wpcc('Delete URLs')
            ])

            @include('partials/test-result-container')

        </div>
    </form>
</div>