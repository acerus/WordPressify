<div class="input-group find-replace-in-custom-meta {{ isset($addon) ? 'addon' : '' }} {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
    @endif
    <div class="input-container">
        <input type="checkbox" name="{{ $name . '[regex]' }}" id="{{ $name . '[regex]' }}" data-toggle="tooltip" title="{{ _wpcc('Regex?') }}"
            @if(isset($value['regex'])) checked="checked" @endif>

        <input type="text" name="{{ $name . '[meta_key]' }}" id="{{ $name . '[meta_key]' }}" placeholder="{{ _wpcc('Meta key') }}"
               value="{{ isset($value['meta_key']) ? $value['meta_key'] : '' }}" class="meta-key">

        <input type="text" name="{{ $name . '[find]' }}" id="{{ $name . '[find]' }}" placeholder="{{ _wpcc('Find') }}"
            value="{{ isset($value['find']) ? $value['find'] : '' }}">

        <input type="text" name="{{ $name . '[replace]' }}" id="{{ $name . '[replace]' }}" placeholder="{{ _wpcc('Replace') }}"
               value="{{ isset($value['replace']) ? $value['replace'] : '' }}">
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>