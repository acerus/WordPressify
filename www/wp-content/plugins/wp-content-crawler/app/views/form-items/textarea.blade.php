<div class="inputs">
    @include('form-items.partials.short-code-buttons')
    <div class="input-group textarea {{ isset($addon) ? ' addon ' : '' }}">
        @if(isset($addon))
            @include('form-items.partials.button-addon-test')
        @endif

        <div class="input-container">
            <textarea @if(!isset($noName) || !$noName) name="{{ $name }}" @endif id="{{ $name }}"
                      @if(isset($cols)) cols="{{ $cols }}" @endif
                      rows="{{ isset($rows) ? $rows : '10' }}"
                      @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
                      @if(isset($disabled)) disabled @endif
                      @if(isset($readOnly)) readonly="readonly" @endif
            >{!! isset($value) ? $value : (isset($settings[$name]) ? (is_array($settings[$name]) ? $settings[$name][0] : $settings[$name]) : '') !!}</textarea>
        </div>
    </div>
</div>