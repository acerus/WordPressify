<div class="input-group selector-custom-post-meta {{ isset($addon) ? 'addon dev-tools' : '' }} {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
        @include('form-items.dev-tools.button-dev-tools')
    @endif
    <div class="input-container">
        <input type="checkbox" name="{{ $name . '[multiple]' }}" id="{{ $name . '[multiple]' }}" data-toggle="tooltip" title="{{ _wpcc('Multiple?') }}"
               @if(isset($value['multiple'])) checked="checked" @endif>

        <input type="text" name="{{ $name . '[selector]' }}" id="{{ $name . '[selector]' }}" placeholder="{{ _wpcc('Selector') }}"
               value="{{ isset($value['selector']) ? $value['selector'] : '' }}"
               class="css-selector">

        <input type="text" name="{{ $name . '[attr]' }}" id="{{ $name . '[attr]' }}" placeholder="{{ sprintf(_wpcc('Attribute (default: %s)'), $defaultAttr) }}"
               value="{{ isset($value['attr']) ? $value['attr'] : '' }}" class="css-selector-attr">

        <input type="text" name="{{ $name . '[meta_key]' }}" id="{{ $name . '[meta_key]' }}" placeholder="{{ _wpcc('Meta key') }}"
               value="{{ isset($value['meta_key']) ? $value['meta_key'] : '' }}" class="meta-key">
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>