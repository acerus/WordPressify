{{--
    Required variables:
        $keyTestUrl:                        Option key for "test URL" input
        $keyTestFindReplace:                Option key for "test find-replace" input
        $keyFindReplaceFirstLoad:           Option key for "find-replace at first load" input
        $keyFindReplaceElementAttributes:   Option key for "find-replace in element attributes" input
        $keyExchangeElementAttributes:      Option key for "exchange element attributes" input
        $keyRemoveElementAttributes:        Option key for "remove element attributes" input
        $keyFindReplaceElementHtml:         Option key for "find-replace in element HTML" input
--}}

<?php
    $cssSelectorChangeEmptyResultWarning = _wpcc("If you change the values that you use in the CSS selector,
        <b>the test results will be empty.</b>");
?>

{{-- SECTION: MANIPULATE HTML --}}
@include('partials.table-section-title', ['title' => _wpcc("Manipulate HTML")])

{{-- TEST FIND REPLACE FOR POST HTML AT FIRST LOAD --}}
<tr>
    <td>
        @include('form-items/label', [
            'for'   =>  $keyTestFindReplace,
            'title' =>  _wpcc('Test code for find-and-replaces in HTML'),
            'info'  =>  _wpcc('A piece of code to be used when testing find-and-replace settings below.')
        ])
    </td>
    <td>
        @include('form-items/textarea', [
            'name'          => $keyTestFindReplace,
            'placeholder'   =>  _wpcc('The code which will be used to test find-and-replace settings'),
        ])
    </td>
</tr>

{{-- FIND AND REPLACE FOR POST HTML AT FIRST LOAD--}}
<tr>
    <td>
        @include('form-items/label', [
            'for'   => $keyFindReplaceFirstLoad,
            'title' => _wpcc("Find and replace in HTML at first load"),
            'info'  => _wpcc('If you want some things to be replaced with some other things in <b>HTML of
                the post page at first load</b>, this is the place. <b>The replacements will be applied after
                the HTML is retrieved and replacements defined in general settings page are applied</b>.') . " " . _wpcc_trans_regex()
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       =>  'form-items/find-replace',
            'name'          =>  $keyFindReplaceFirstLoad,
            'addKeys'       =>  true,
            'remove'        =>  true,
            'addon'         =>  'dashicons dashicons-search',
            'data'          =>  [
                'subjectSelector'   =>  "#" . $keyTestFindReplace,
                'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_FIND_REPLACE,
            ],
            'test'          => true,
            'addonClasses'  => 'wcc-test-find-replace'
        ])
        @include('partials/test-result-container')
    </td>
</tr>

{{-- FIND AND REPLACE IN ELEMENT ATTRIBUTES --}}
<tr>
    <td>
        @include('form-items/label', [
            'for'   => $keyFindReplaceElementAttributes,
            'title' => _wpcc("Find and replace in element attributes"),
            'info'  => _wpcc('If you want some things to be replaced with some other things in <b>attributes of
                elements</b>, this is the place. <b>The replacements will be applied after
                the replacements at first load are applied</b>.') . " " . $cssSelectorChangeEmptyResultWarning . " " . _wpcc_trans_regex()
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       =>  'form-items/find-replace-in-element-attributes',
            'name'          =>  $keyFindReplaceElementAttributes,
            'addKeys'       =>  true,
            'remove'        =>  true,
            'addon'         =>  'dashicons dashicons-search',
            'data'          =>  [
                'urlSelector'       =>  "#" . $keyTestUrl,
                'subjectSelector'   =>  "#" . $keyTestFindReplace,
                'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_FIND_REPLACE_IN_ELEMENT_ATTRIBUTES,
                'requiredSelectors' =>  "#{$keyTestUrl} | #{$keyTestFindReplace}", // One of them is enough
            ],
            'test'          => true,
            'addonClasses'  => 'wcc-test-find-replace-in-element-attributes'
        ])
        @include('partials/test-result-container')
    </td>
</tr>

{{-- EXCHANGE ELEMENT ATTRIBUTES --}}
<tr>
    <td>
        @include('form-items/label', [
            'for'   => $keyExchangeElementAttributes,
            'title' => _wpcc("Exchange element attributes"),
            'info'  => sprintf(_wpcc('If you want to exchange <b>the values of two attributes of an element</b>,
                this is the place. <b>If value of attribute 2 does not exist, the values will not be exchanged.</b>
                <b>The replacements will be applied after the find-and-replaces for element attributes are applied.</b>
                E.g. you can replace the values of %1$s and %2$s attributes to save lazy-loading images if the
                target %3$s element has these attributes.') . " " . $cssSelectorChangeEmptyResultWarning,
                '<span class="highlight attribute">src</span>',
                '<span class="highlight attribute">data-src</span>',
                '<span class="highlight selector">img</span>'
            )
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       =>  'form-items/exchange-element-attributes',
            'name'          =>  $keyExchangeElementAttributes,
            'addKeys'       =>  true,
            'remove'        =>  true,
            'addon'         =>  'dashicons dashicons-search',
            'data'          =>  [
                'urlSelector'       =>  "#" . $keyTestUrl,
                'subjectSelector'   =>  "#" . $keyTestFindReplace,
                'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_EXCHANGE_ELEMENT_ATTRIBUTES,
                'requiredSelectors' =>  "#{$keyTestUrl} | #{$keyTestFindReplace}", // One of them is enough
            ],
            'test'          => true,
            'addonClasses'  => 'wcc-test-exchange-element-attributes'
        ])
        @include('partials/test-result-container')
    </td>
</tr>

{{-- REMOVE ELEMENT ATTRIBUTES --}}
<tr>
    <td>
        @include('form-items/label', [
            'for'   => $keyRemoveElementAttributes,
            'title' => _wpcc("Remove element attributes"),
            'info'  => _wpcc('If you want to remove <b>attributes of an element</b>, this is the place. <b>The
                removals will be applied after the attribute exchanges are applied</b>.') . " " . $cssSelectorChangeEmptyResultWarning
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       =>  'form-items/remove-element-attributes',
            'name'          =>  $keyRemoveElementAttributes,
            'addKeys'       =>  true,
            'remove'        =>  true,
            'addon'         =>  'dashicons dashicons-search',
            'data'          =>  [
                'urlSelector'       =>  "#" . $keyTestUrl,
                'subjectSelector'   =>  "#" . $keyTestFindReplace,
                'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_REMOVE_ELEMENT_ATTRIBUTES,
                'requiredSelectors' =>  "#{$keyTestUrl} | #{$keyTestFindReplace}", // One of them is enough
            ],
            'test'          => true,
            'addonClasses'  => 'wcc-test-remove-element-attributes'
        ])
        @include('partials/test-result-container')
    </td>
</tr>

{{-- FIND AND REPLACE IN ELEMENT HTML --}}
<tr>
    <td>
        @include('form-items/label', [
            'for'   => $keyFindReplaceElementHtml,
            'title' => _wpcc("Find and replace in element HTML"),
            'info'  => _wpcc('If you want some things to be replaced with some other things in <b>HTML of
                elements</b>, this is the place. <b>The replacements will be applied after
                the attribute removals are applied</b>.') . " " . $cssSelectorChangeEmptyResultWarning . " " . _wpcc_trans_regex()
        ])
    </td>
    <td>
        @include('form-items/multiple', [
            'include'       =>  'form-items/find-replace-in-element-html',
            'name'          =>  $keyFindReplaceElementHtml,
            'addKeys'       =>  true,
            'remove'        =>  true,
            'addon'         =>  'dashicons dashicons-search',
            'data'          =>  [
                'urlSelector'       =>  "#" . $keyTestUrl,
                'subjectSelector'   =>  "#" . $keyTestFindReplace,
                'testType'          =>  \WPCCrawler\objects\Test::$TEST_TYPE_FIND_REPLACE_IN_ELEMENT_HTML,
                'requiredSelectors' =>  "#{$keyTestUrl} | #{$keyTestFindReplace}", // One of them is enough
            ],
            'test'          => true,
            'addonClasses'  => 'wcc-test-find-replace-in-element-html'
        ])
        @include('partials/test-result-container')
    </td>
</tr>