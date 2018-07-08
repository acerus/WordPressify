<label for="{{ isset($for) ? $for : '' }}" @if(isset($class)) class="{{ $class }}" @endif>{{ $title }}</label>
@if(isset($info))
    <div class="info-button"><span class="dashicons dashicons-info"></span></div>
    <div style="clear: both;"></div>
    <div class="info-text hidden">{!! $info !!}</div>
@endif