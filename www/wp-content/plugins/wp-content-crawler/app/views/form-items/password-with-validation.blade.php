<div class="input-group">
    <div class="input-container input-container-passwords">
        <input type="password" name="{{ $name . '_old' }}" id="{{ $name . '_old' }}" placeholder="{{ _wpcc('Old password') }}">
        <input type="password" name="{{ $name }}" id="{{ $name }}" placeholder="{{ _wpcc('New password') }}">
        <input type="password" name="{{ $name . '_validation' }}" id="{{ $name . '_validation' }}" placeholder="{{ _wpcc('New password again') }}">
    </div>
</div>