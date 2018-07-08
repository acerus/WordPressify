<div class="input-group remove-element-attributes {{ isset($addon) ? 'addon dev-tools' : '' }} {{ isset($remove) ? 'remove' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
        @include('form-items.dev-tools.button-dev-tools')
    @endif
    <div class="input-container">
        <input type="text" name="{{ $name . '[selector]' }}" id="{{ $name . '[selector]' }}" placeholder="{{ _wpcc('Selector') }}"
               value="{{ isset($value['selector']) ? $value['selector'] : '' }}"
               class="css-selector">

        <input type="text" name="{{ $name . '[attr]' }}" id="{{ $name . '[attr]' }}" placeholder="{{ _wpcc('Comma-separated attributes') }}"
               value="{{ isset($value['attr']) ? $value['attr'] : '' }}">
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>