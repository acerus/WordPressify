<div class="input-group key-value {{ isset($remove) ? 'remove' : '' }} {{ isset($class) ? $class : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        <input type="text" name="{{ $name . '[key]' }}" id="{{ $name . '[key]' }}" placeholder="{{ $keyPlaceholder }}"
               value="{{ isset($value['key']) ? $value['key'] : '' }}">

        <input type="text" name="{{ $name . '[value]' }}" id="{{ $name . '[value]' }}" placeholder="{{ $valuePlaceholder }}"
               value="{{ isset($value['value']) ? $value['value'] : '' }}">
    </div>
    @if(isset($remove) && $remove)
        @include('form-items/remove-button')
    @endif
</div>