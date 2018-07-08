@if(is_array($value))
    <ul class="info-list-inside">
        @foreach($value as $v)
            <li>
                @include('site-tester.detail-value', ['value' => $v])
            </li>
        @endforeach
    </ul>
@else
    {!! $value !!}
@endif