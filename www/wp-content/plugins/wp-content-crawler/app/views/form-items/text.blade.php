<div class="input-group text
    {{ isset($addon) ? ' addon ' : '' }}
    {{ isset($remove) ? ' remove ' : '' }}
    {{ isset($showDevTools) && $showDevTools ? ' dev-tools ' : '' }}
    {{ isset($class) ? ' ' . $class . ' ' : '' }}"
     @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif
>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
    @endif

    @if(isset($showDevTools) && $showDevTools)
        @include('form-items.dev-tools.button-dev-tools')
    @endif
    <div class="input-container">
        <input type="{{ isset($type) ? $type : 'text' }}"
               @if(isset($min)) min="{{ $min }}" @endif
               id="{{ isset($name) ? $name : '' }}"
               name="{{ isset($name) ? $name : '' }}"
               value="{{ isset($value) ? $value : (isset($settings[$name]) ? (isset($isOption) && $isOption ? $settings[$name] : $settings[$name][0]) : '') }}"
               placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
               @if(isset($required)) required="required" @endif
               @if(isset($inputClass)) class="{{ $inputClass }}" @endif
        />
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>