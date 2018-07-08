@include('form-items/button', [
    'text'      => $isLanguagesAvailable ? _wpcc('Refresh languages') : _wpcc('Load languages'),
    // 'iconClass' => $isLanguagesAvailable ? '' : 'dashicons dashicons-warning attention',
    'buttonClass' => "load-languages {$class}",
])
@include('partials/test-result-container')