<div class="wcc-settings-title">
    <h3>{{ _wpcc('Import/Export Settings') }}</h3>
    <span>{{ _wpcc('Import settings from another site or copy the settings to import for another site') }}</span>
</div>

<table class="wcc-settings">
    {{-- IMPORT SETTINGS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_import_settings',
                'title' => _wpcc('Import Settings'),
                'info'  => _wpcc('Paste the settings exported from another site to import. <b>Current settings
                    will be overridden.</b>')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_post_import_settings',
                'placeholder'   =>  _wpcc('Paste settings and update. Note: This will override all settings.')
            ])
        </td>
    </tr>

    {{-- EXPORT SETTINGS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   => '_post_export_settings',
                'title' => _wpcc('Export Settings'),
                'info'  => _wpcc('You can copy the settings here and use the copied code to export settings to
                    another site.')
            ])
        </td>
        <td>
            @include('form-items/textarea', [
                'name'          =>  '_post_export_settings',
                'value'         =>  $settingsForExport,
                'readOnly'      =>  true,
                'noName'        =>  true,
            ])
        </td>
    </tr>
</table>