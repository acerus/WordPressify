<div class="inputs">
    @if(!isset($settings[$name]) || !$settings[$name] || !$settings[$name][0])
        @include($include, [
            'name'      => $name . '[' . (isset($addKeys) ? 0 : '') . ']',
            'remove'    => true,
            'value'     => '',
            'dataKey'   => isset($addKeys) ? 0 : ''
        ])
    @else
        @foreach(unserialize($settings[$name][0]) as $key => $value)
            @include($include, [
                'name'      => $name . '[' . (isset($addKeys) ? $key : '') . ']',
                'remove'    => true,
                'value'     => $value,
                'dataKey'   => $key,
            ])
        @endforeach
    @endif
</div>
@if(!isset($max) || $max != 1)
    <div style="clear: both;"></div>
    <div class="actions">
        <button class="button wcc-add-new" data-max="{{isset($max) ? $max : 0}}">{{ _wpcc('Add New') }}</button>
    </div>
@endif