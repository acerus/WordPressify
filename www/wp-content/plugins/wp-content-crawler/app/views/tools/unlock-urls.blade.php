<h2>
    <span>{{ _wpcc('Unlock all URLs') }}</span>
    @include('partials/button-toggle-info-texts')
</h2>
<div class="inside">
    <form action="" class="tool-form">
        @include('partials.form-nonce-and-action')

        <input type="hidden" name="tool_type" value="unlock_all_urls">

        <div class="panel-wrap wcc-settings-meta-box">

            {{-- INFO --}}
            <div>
                <p>
                {{ _wpcc('The plugin locks the URLs which it is processing. This is to avoid processing
                    already-being-processed URLs. But sometimes things go wrong either with your server or the target server
                    and some URLs stay locked. You can see these URLs shown as "currently being crawled/recrawled" in
                    dashboard. If you see some URLs are stuck there and you do not want to see them, you can use this tool
                    to unlock all URLs.') }}
                </p>
            </div>

            @include('form-items/submit-button', [
                'text'  =>  _wpcc('Unlock All URLs')
            ])

            @include('partials/test-result-container')
        </div>
    </form>
</div>