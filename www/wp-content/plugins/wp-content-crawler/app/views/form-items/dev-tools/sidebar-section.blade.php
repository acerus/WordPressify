<div class="sidebar-section expanded {{ isset($class) ? $class : '' }}">
    {{-- Title container --}}
    <div class="section-title">
        {{-- Title --}}
        <span>{{ $title }}</span>

        {{-- Control buttons --}}
        <div class="section-controls">
            @if(isset($buttons) && is_array($buttons))
                @foreach($buttons as $class)
                    <span class="section-title-button {{ $class }}"></span>
                @endforeach
            @endif

            <span class="section-title-button dashicons dashicons-arrow-up toggleExpand"></span>
        </div>
    </div>

    {{-- Content --}}
    <div class="section-content"></div>
</div>