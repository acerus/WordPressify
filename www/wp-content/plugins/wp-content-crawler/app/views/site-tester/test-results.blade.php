@if(isset($template))
    <div class="details">
        <h2>
            <span>{{ _wpcc('Template') }}</span>
            @if(isset($templateMessage) && $templateMessage)
                <span class="small">{{ $templateMessage }}</span>
            @endif
            <button class="button" id="go-to-details">{{ _wpcc('Go to details') }}</button>
        </h2>
        <div class="inside">
            <div class="template">
                {!! $template !!}
            </div>
            <div class="clear-fix"></div>
        </div>
        <div class="clear-fix"></div>
    </div>
@endif

<div class="details" id="details">
    <h2>
        <span>{{ _wpcc('Details') }}</span>
        <button class="button go-to-top">{{ _wpcc('Go to top') }}</button>
    </h2>
    <div class="inside">
        @if(isset($info))
            <div class="info">
                <h4>{{ _wpcc('Info') }}</h4>
                <ul class="info-list">

                    {{-- ERRORS --}}
                    @if(isset($data["errors"]))
                        @foreach($data["errors"] as $key => $value)
                            <li>
                                <b><span class="error">{{ _wpcc("Error") . ': ' }}</span> {{ $value["error"] }}</b>
                                <br><code>{{ $value["data"] }}</code>
                            </li>
                        @endforeach
                    @endif

                    {{-- DETAILS --}}
                    @foreach($info as $name => $value)
                        <li>
                            <b>{{ $name }}:</b>
                            @include('site-tester.detail-value')
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="source-code">
            @if(isset($template) && isset($showSourceCode) && $showSourceCode)
                <h4>{{ _wpcc('Source Code') }}</h4>
                <textarea id="source_code" class="source-code" rows="12">{{ $template }}</textarea>
            @endif
        </div>
        @if(isset($data))
            <div class="data">
                <h4>{{ _wpcc('Data') }}</h4>

                <textarea id="source_code" class="data" rows="16"><?php
                    $str = (print_r($data, true));
                    echo esc_html($str);
                ?></textarea>

            </div>
        @endif
        <div class="clear-fix"></div>
        <div class="go-to-top-container">
            <button class="button go-to-top">{{ _wpcc('Go to top') }}</button>
        </div>
    </div>

    <div class="clear-fix"></div>
</div>