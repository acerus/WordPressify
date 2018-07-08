{{--
    Required variables:
        $options: A key-value pair with values being description of the keys.
        $name: Name of the option
--}}

<?php
    // Get the values from settings, if $value is not supplied.
    if(!isset($value) || !is_array($value) || !$value) {
        $value = isset($settings[$name]) && isset($settings[$name][0]) ? unserialize($settings[$name][0])[0] : [];

        // Make sure each key has a non-empty value. Checkboxes have weird behaviors. I am not sure if they always
        // have a value. Sometimes they are just keys. Sometimes they are key-value pairs.
        $valuesPrepared = [];
        foreach($value as $k => $v) $valuesPrepared[$k] = 1;
        $value = $valuesPrepared;
    }
?>

<div class="input-group multi-checkbox">
    <div class="input-container">
        @foreach($options as $key => $title)
            <?php
                // Store all settings under 0 key. This is important because the backend function that saves the settings
                // automatically gets the array values. So, in order not to lose the keys, we should store it under a
                // number-type index. At the end, this index will end up being 0, due to getting array values in backend.
                // So, we store the settings under 0 key.
                $preparedName = "{$name}[0][{$key}]"
            ?>

            <label for="{{ $preparedName }}">
                <input type="checkbox"
                       id="{{ $preparedName }}"
                       name="{{ $preparedName }}"
                       @if(isset($value[$key]) && $value[$key]) checked="checked" @endif />

                {{ $title }}
            </label>

        @endforeach
    </div>
</div>