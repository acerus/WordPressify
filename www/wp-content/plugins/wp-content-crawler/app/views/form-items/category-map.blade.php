<div class="input-group category-map {{ isset($addon) ? 'addon' : '' }} {{ isset($remove) ? 'remove' : '' }}" @if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
    @endif
    <div class="input-container">
        <select name="{{ $name . '[cat_id]' }}" id="{{ $name . '[select]' }}">
            <?php $selectedId = isset($value["cat_id"]) ? $value["cat_id"] : null; ?>
            @foreach($categories as $id => $categoryName)
                <option value="{{ $id }}" @if($selectedId && $id == $selectedId) selected="selected" @endif>{{ $categoryName }}</option>
            @endforeach
        </select>
        <input type="text" id="{{ isset($name) ? $name . '[url]' : '' }}" name="{{ isset($name) ? $name . '[url]' : '' }}" value="{{ isset($value["url"]) ? $value['url'] : '' }}" placeholder="{{ isset($placeholder) ? $placeholder : '' }}" />
    </div>
    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>