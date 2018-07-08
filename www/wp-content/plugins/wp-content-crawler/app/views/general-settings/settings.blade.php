<?php
    // Define a variable to understand if this is the general page. If not, this settings is in post settings page.
    // Take some actions according to this.
    $isGeneralPage = !isset($isPostPage) || !$isPostPage;
    $isOption = !isset($isOption) ? ($isGeneralPage ? true : false) : $isOption;
?>

{{-- TABS IF GENERAL PAGE --}}
@if($isGeneralPage)
    <h2 class="nav-tab-wrapper">
        <a href="#" data-tab="#tab-gs-scheduling" class="
            nav-tab nav-tab-active
            {{ isset($settings['_wpcc_is_scheduling_active']) && !empty($settings['_wpcc_is_scheduling_active']) && $settings['_wpcc_is_scheduling_active'][0] ? 'nav-tab-highlight-on' : 'nav-tab-highlight-off' }}
        ">
            {{ _wpcc('Scheduling') }}
        </a>
        <a href="#" data-tab="#tab-gs-post" class="nav-tab">{{ _wpcc('Post') }}</a>
        <a href="#" data-tab="#tab-gs-translation" class="nav-tab">{{ _wpcc('Translation') }}</a>
        <a href="#" data-tab="#tab-gs-seo" class="nav-tab">{{ _wpcc('SEO') }}</a>
        <a href="#" data-tab="#tab-gs-notifications" class="nav-tab">{{ _wpcc('Notifications') }}</a>
        <a href="#" data-tab="#tab-gs-advanced" class="nav-tab">{{ _wpcc('Advanced') }}</a>

        <?php

        /**
         * Fires before advanced tab in tab title area of general settings page
         *
         * @param array $settings       Existing settings and their values saved by user before
         * @param bool  $isGeneralPage  True if this is called from a general settings page.
         * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
         *                              an option is a WordPress option. This is true when this is fired from general
         *                              settings page.
         * @since 1.6.3
         */
        do_action('wpcc/view/general-settings/add_tab_title', $settings, $isGeneralPage, $isOption);

        ?>

        @include('partials.input-active-tab')
        @include('partials/button-toggle-info-texts')
    </h2>

{{-- SOME BUTTONS IF POST PAGE --}}
@else
    <div class="section-header-button-container">
        <button class="button" id="btn-load-general-settings">{{ _wpcc("Load General Settings") }}</button>
        <button class="button" id="btn-clear-general-settings">{{ _wpcc("Clear General Settings") }}</button>
    </div>
@endif

{{-- SCHEDULING --}}
<div id="tab-gs-scheduling" class="tab{{ $isGeneralPage ? '' : '-inside' }}">
    @include('general-settings.tab-scheduling')
</div>

{{-- POST SETTINGS --}}
<div id="tab-gs-post" class="tab{{ $isGeneralPage ? '' : '-inside' }} {{ $isGeneralPage ? 'hidden' : '' }}">
    @include('general-settings.tab-post')
</div>

{{-- TRANSLATION --}}
<div id="tab-gs-translation" class="tab{{ $isGeneralPage ? '' : '-inside' }} {{ $isGeneralPage ? 'hidden' : '' }}">
    @include('general-settings.tab-translation')
</div>

@if($isGeneralPage)
    {{-- SEO --}}
    <div id="tab-gs-seo" class="tab hidden">
        @include('general-settings.tab-seo')
    </div>

    {{-- NOTIFICATIONS --}}
    <div id="tab-gs-notifications" class="tab hidden">
        @include('general-settings.tab-notifications')
    </div>
@endif

<?php

/**
 * Fires before advanced tab content in tab content area of general settings page
 *
 * @param array $settings       Existing settings and their values saved by user before
 * @param bool  $isGeneralPage  True if this is called from a general settings page.
 * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
 *                              an option is a WordPress option. This is true when this is fired from general
 *                              settings page.
 * @since 1.6.3
 */
do_action('wpcc/view/general-settings/add_tab_content', $settings, $isGeneralPage, $isOption);

?>

{{-- ADVANCED --}}
<div id="tab-gs-advanced" class="tab{{ $isGeneralPage ? '' : '-inside' }} {{ $isGeneralPage ? 'hidden' : '' }}">
    @include('general-settings.tab-advanced')
</div>