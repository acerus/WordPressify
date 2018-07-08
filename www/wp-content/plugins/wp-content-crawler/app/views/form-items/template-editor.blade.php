<div class="inputs">
    @include('form-items.partials.short-code-buttons')
    <div class="input-group">
        <div class="input-container">
            <?php wp_editor(isset($settings[$name]) ? $settings[$name][0] : '', $name, [
                "media_buttons"         =>  false,
                "editor_height"         =>  isset($height) ? $height : 320
            ]) ?>
        </div>
    </div>
</div>