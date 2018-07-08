<input type="hidden" name="_dev_tools_state" value='{!! isset($settings['_dev_tools_state']) ? $settings['_dev_tools_state'][0] : '' !!}'>
<div class="dev-tools-content-container" data-wcc="{{ isset($data) && $data ? json_encode($data) : json_encode([]) }}">
    {{-- This is the element that stores all of the functionality of the developer tools --}}
    <div class="dev-tools-content" tabindex="-1">
        {{-- Lightbox title. This will be used as lightbox title later and won't be left here. It will be moved. --}}
        <div class="lightbox-title">Hi</div>

        {{-- Toolbar --}}
        <div class="toolbar">
            {{-- Address bar --}}
            <div class="address-bar">
                <div class="button-container">
                    {{-- Back button --}}
                    <span class="dashicons dashicons-arrow-left-alt button-option back disabled"
                          title="{{ _wpcc("Click to go back") }}"></span>

                    {{-- Forward button --}}
                    <span class="dashicons dashicons-arrow-right-alt button-option forward disabled"
                          title="{{ _wpcc("Click to go forward") }}"></span>

                    {{-- Refresh button --}}
                    <span class="dashicons dashicons-update button-option refresh disabled"
                          title="{{ _wpcc("Click to refresh") }}"></span>
                </div>

                @include('form-items.text', [
                    'name' => '_dt_toolbar_url',
                    'class' => 'toolbar-input-container url-input',
                    'placeholder' => _wpcc('URL starting with http...'),
                ])

                <div class="button-container">
                    {{-- Go button --}}
                    <span class="dashicons dashicons-admin-collapse button-option go"
                          title="{{ _wpcc("Click to go to the URL") }}"></span>

                    {{-- Sidebar button --}}
                    <span class="dashicons dashicons-menu button-option sidebar-open"
                          title="{{ _wpcc("Click to open the sidebar") }}"></span>
                </div>
            </div>

            {{-- CSS selector tools --}}
            <div class="css-selector-tools">
                <div class="button-container">
                    {{-- Clear highlights button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-clear-highlights',
                        'iconClass' => 'dashicons dashicons-editor-removeformatting',
                        'title' => _wpcc('Clear the highlights'),
                    ])

                    {{-- Show alternatives button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-show-alternatives',
                        'iconClass' => 'dashicons dashicons-image-rotate-right',
                        'title' => _wpcc('Show alternative selectors'),
                    ])

                    {{-- Use button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-use',
                        'iconClass' => 'dashicons dashicons-yes',
                        'title' => _wpcc('Use the selector'),
                    ])
                </div>

                @include('form-items.text', [
                    'name' => '_dt_toolbar_css_selector',
                    'class' => 'toolbar-input-container css-selector-input',
                    'placeholder' => _wpcc('CSS selector'),
                ])

                <div class="button-container">
                    {{-- Test button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-test',
                        'iconClass' => 'dashicons dashicons-search',
                        'title' => _wpcc('Test the selector'),
                        'data' => [
                            'urlSelector' => '#_dt_toolbar_url',
                            'testType' => \WPCCrawler\objects\Test::$TEST_TYPE_HTML,
                            'url' => 0
                        ]
                    ])

                    {{-- Remove elements button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-remove-elements',
                        'iconClass' => 'dashicons dashicons-trash',
                        'title' => _wpcc('Remove the elements from current page'),
                    ])
                </div>
            </div>

            {{-- Other options --}}
            <div class="options">
                {{-- Options on the left --}}
                <div class="left">
                    {{-- Toggle hover select button --}}
                    <span class="dashicons dashicons-external button-option toggle-hover-select active"
                          title="{{ _wpcc("Toggle hover select") }}"></span>

                    {{-- Target HTML tag --}}
                    <label for="_dt_target_html_tag">
                        {{ _wpcc("Target HTML Tag ") }}:
                        <input type="text" name="_dt_target_html_tag" class="target-html-tag">
                    </label>

                </div>

                {{-- Options on the right --}}
                <div class="right">
                    {{-- Test button behavior --}}
                    <label for="test_button_behavior">
                        {{ _wpcc("Test via") }}
                        <select name="test_button_behavior" id="test_button_behavior" class="test-button-behavior">
                            <option value="php">{{ _wpcc("PHP") }}</option>
                            <option value="js">{{ _wpcc("JavaScript") }}</option>
                            <option value="both" selected="selected">{{ _wpcc("Both") }}</option>
                        </select>
                    </label>

                    {{-- Use immediately --}}
                    <label for="use-immediately">
                        <input type="checkbox" id="use-immediately" class="use-immediately" name="use_immediately" tabindex="-1"> {{ _wpcc("Use immediately when clicked") }}
                    </label>

                    {{-- Remove scripts --}}
                    <label for="remove-scripts">
                        <input type="checkbox" id="remove-scripts" class="remove-scripts" name="remove_scripts" tabindex="-1" checked="checked"> {{ _wpcc("Remove scripts") }}
                    </label>

                    {{-- Remove styles --}}
                    <label for="remove-styles">
                        <input type="checkbox" id="remove-styles" class="remove-styles" name="remove_styles" tabindex="-1"> {{ _wpcc("Remove styles") }}
                    </label>
                </div>
            </div>

            @include('partials/test-result-container')
        </div>

        {{-- iframe will be used to show the content --}}
        <iframe frameborder="0" class="source"></iframe>

        {{-- Sidebar --}}
        <div class="sidebar">
            {{-- Close button --}}
            <span class="dashicons dashicons-no-alt sidebar-close"></span>

            @include('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('History'),
                'class' => 'history',
                'buttons' => [
                    'dashicons dashicons-trash clear-history'
                ]
            ])

            @include('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('Alternative Selectors'),
                'class' => 'alternative-selectors'
            ])

            @include('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('All Used Selectors'),
                'class' => 'used-selectors'
            ])

        </div>

        {{-- Used to display IFrame status --}}
        <div class="iframe-status hidden"></div>
    </div>
</div>

{{-- This style will be copied to each page loaded into the iframe --}}
<style id="iframe-style">
    img.wpcc-element-hovered {
        border: 2px solid #ff4400 !important;
    }

    a.wpcc-element-hovered, .wpcc-element-hovered a {
        color: #fff !important;
        display: inline-block;
    }

    .wpcc-element-hovered {
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background-color: rgba(255, 68, 0, 0.8) !important;
        color: #fff !important;
        z-index: 9999 !important;
        /*-webkit-box-shadow: inset 0 0 2px 2px rgba(255, 0, 0, 1) !important;
           -moz-box-shadow: inset 0 0 2px 2px rgba(255, 0, 0, 1) !important;
                box-shadow: inset 0 0 2px 2px rgba(255, 0, 0, 1) !important;*/

        -webkit-box-shadow: 0 0 10px rgba(255, 0, 0, 1);
           -moz-box-shadow: 0 0 10px rgba(255, 0, 0, 1);
                box-shadow: 0 0 10px rgba(255, 0, 0, 1);
    }

    .wpcc-element-hovered.glow {
        -webkit-animation: glow .5s infinite alternate;
    }

    @-webkit-keyframes glow {
        to {
                          border-color: rgba(255, 0, 0, 1);
            -webkit-box-shadow: 0 0 10px rgba(255, 0, 0, 1);
               -moz-box-shadow: 0 0 10px rgba(255, 0, 0, 1);
                    box-shadow: 0 0 10px rgba(255, 0, 0, 1);
        }
    }
</style>