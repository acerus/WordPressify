@if(isset($urls) && $urls)
    <h3>{{ _wpcc('Post URLs') }}</h3>
    @include('site-tester/urls-with-test', [
        'urls'      =>  $urls,
        'testType'  =>  'test_post'
    ])
@endif

@if(isset($nextPageUrl) && $nextPageUrl)
    <h3>{{ _wpcc('Next Page URL') }}</h3>
    <div>
        @include('site-tester/button-test-this', ['url' => $nextPageUrl, 'type' => 'test_category'])
        <a target="_blank" href="{{ $nextPageUrl }}">{{ $nextPageUrl }}</a>
    </div>
@endif