@if(isset($message))
    <p>{!! $message !!}</p>
@endif
<ul>
    @foreach($results as $result)
        <li><code>{{ $result }}</code></li>
    @endforeach
</ul>
@if(empty($results))
    <span class="no-result">{{ _wpcc('No result') }}</span>
@endif