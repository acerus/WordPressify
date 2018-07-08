{{--
    Required:
        pageActionKey
--}}

@if(!isset($noNonceAndAction) || !$noNonceAndAction)
    {!! wp_nonce_field($pageActionKey, \WPCCrawler\Constants::$NONCE_NAME) !!}

    <input type="hidden" name="action" value="{{ $pageActionKey }}" id="hiddenaction">
@endif