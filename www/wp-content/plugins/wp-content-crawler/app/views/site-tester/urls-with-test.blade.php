<ul class="post-url-list">
    @foreach($urls as $i => $url)
        <li>
            <div><span>{{ $i + 1 }}.</span></div>
            <div>
                @include('site-tester/button-test-this', ['url' => $url["data"], 'type' => $testType])
            </div>
            <div class="thumbnail-container @if(isset($hideThumbnails) && $hideThumbnails) {{ 'hidden' }} @endif">
                @if(isset($url["thumbnail"]))
                    <a href="{{ $url["thumbnail"] }}"
                       target="_blank"
                       data-html="true"
                       data-toggle="tooltip"
                       data-placement="right"
                       title="<img src='{{ $url["thumbnail"] }}' />"
                    >
                        <img class="small" src="{{ $url["thumbnail"] }}" width="30" height="30" alt="">
                    </a>
                @endif
            </div>
            <div>
                <a target="_blank" href="{{ $url["data"] }}">{{ $url["data"] }}</a>
            </div>
        </li>
    @endforeach
</ul>