<div class="input-group">
    <div class="input-container">
        <input type="checkbox"
               id="{{ isset($name) ? $name : '' }}"
               name="{{ isset($name) ? $name : '' }}"
               @if(isset($dependants)) data-dependants='{{ $dependants }}' @endif
               @if(isset($settings[$name]) && !empty($settings[$name]) && $settings[$name][0]) checked="checked" @endif />
    </div>
</div>