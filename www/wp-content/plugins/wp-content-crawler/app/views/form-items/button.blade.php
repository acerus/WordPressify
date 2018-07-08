<div class="input-container">
    <div class="input-group">

        <button class="button {{ isset($buttonClass) && $buttonClass ? $buttonClass : '' }}"
                type="button"
                title="{{ isset($title) ? $title : '' }}"
                @if(isset($id)) id="{{ $id }}" @endif
                @if(isset($data)) data-wcc="{{ json_encode($data) }}" @endif
        >
            @if(isset($iconClass) && $iconClass)
                <span class="{{ $iconClass }}"></span>
            @endif
            {{ $text }}
        </button>

    </div>
</div>
