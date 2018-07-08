@if(isset($buttons))
    <div class="input-group">
        <div class="input-container input-button-container">
            @foreach($buttons as $button)
                <button class="button"
                        data-clipboard-text="{{ $button["code"] }}"
                        data-toggle="tooltip"
                        data-placement="top"
                        id="{{ $button["code"] }}"
                        title="{{ $button["description"] }}">
                    {{ $button["code"] }}
                </button>
            @endforeach
        </div>
    </div>
@endif