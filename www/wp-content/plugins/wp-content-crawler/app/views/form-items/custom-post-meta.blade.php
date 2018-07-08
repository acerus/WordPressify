<div class="input-group custom-post-meta {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    <div class="input-container">
        <input type="checkbox" name="{{ $name . '[multiple]' }}" id="{{ $name . '[multiple]' }}" data-toggle="tooltip" title="{{ _wpcc('Multiple?') }}"
               @if(isset($value['multiple'])) checked="checked" @endif>

        <input type="text" name="{{ $name . '[key]' }}" id="{{ $name . '[key]' }}" placeholder="{{ _wpcc('Post meta key') }}"
               value="{{ isset($value['key']) ? $value['key'] : '' }}" class="meta-key">

        <input type="text" name="{{ $name . '[value]' }}" id="{{ $name . '[value]' }}" placeholder="{{ _wpcc('Meta value') }}"
               value="{{ isset($value['value']) ? $value['value'] : '' }}">
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>