<div class="wcc-settings-title">
    <h3>{{ _wpcc('Post Settings') }}</h3>
    <span>{{ _wpcc('Set post settings') }}</span>
</div>

<table class="wcc-settings">
    {{-- ALLOW COMMENTS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_allow_comments',
                'title' =>  _wpcc('Allow Comments'),
                'info'  =>  _wpcc('If you want to allow comments for automatically inserted posts,
                    check this.')
            ])
        </td>
        <td>
            @include('form-items/checkbox', [
                'name'      =>  '_wpcc_allow_comments',
            ])
        </td>
    </tr>

    {{-- POST STATUS --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_post_status',
                'title' =>  _wpcc('Post Status'),
                'info'  =>  _wpcc('Set the status of automatically inserted posts.')
            ])
        </td>
        <td>
            @include('form-items/select', [
                'name'      =>  '_wpcc_post_status',
                'options'   =>  $postStatuses,
                'isOption'  =>  $isOption,
            ])
        </td>
    </tr>

    {{-- POST TYPE --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_post_type',
                'title' =>  _wpcc('Post Type'),
                'info'  =>  _wpcc('Set the type of automatically inserted posts.')
            ])
        </td>
        <td>
            @include('form-items/select', [
                'name'      =>  '_wpcc_post_type',
                'options'   =>  $postTypes,
                'isOption'  =>  $isOption,
            ])
        </td>
    </tr>

    {{-- POST AUTHOR --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_post_author',
                'title' =>  _wpcc('Post Author'),
                'info'  =>  _wpcc('Set the author of automatically inserted posts.')
            ])
        </td>
        <td>
            @include('form-items/select', [
                'name'      =>  '_wpcc_post_author',
                'options'   =>  $authors,
                'isOption'  =>  $isOption,
            ])
        </td>
    </tr>

    {{-- POST TAG LIMIT --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_post_tag_limit',
                'title' =>  _wpcc('Maximum number of tags'),
                'info'  =>  _wpcc('How many tags at maximum can be added to a post? Set this <b>0</b> if you do not
                    want to set a limit and get all available tags. The default value is 0.')
            ])
        </td>
        <td>
            @include('form-items/text', [
                'name'      =>  '_wpcc_post_tag_limit',
                'isOption'  =>  $isOption,
                'type'      =>  'number',
                'min'       =>  0,
            ])
        </td>
    </tr>

    {{-- CHANGE POST PASSWORD --}}
    <tr>
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_change_password',
                'title' =>  _wpcc('Change Password'),
                'info'  =>  _wpcc('If you want to change post password, check this.')
            ])
        </td>
        <td>
            @include('form-items/checkbox', [
                'name'          =>  '_wpcc_change_password',
                'dependants'    =>  '["#post-password"]'
            ])
        </td>
    </tr>

    {{-- POST PASSWORD --}}
    <tr id="post-password">
        <td>
            @include('form-items/label', [
                'for'   =>  '_wpcc_post_password',
                'title' =>  _wpcc('Post Password'),
                'info'  =>  _wpcc('Set the password for automatically inserted posts. The value you
                    enter here will be stored as raw text in the database, without encryption.
                    If anyone accesses your database, he/she will be able to see your password.
                    <br /><br />
                    If you want to delete the password, just leave the new password fields empty.
                    When you change the password, new password will be effective for new posts,
                    and passwords for old posts will not be changed.
                    <br /><br />
                    <b>Leave old password field empty if you did not set any password before.</b>')
            ])
        </td>
        <td>
            @include('form-items/password-with-validation', [
                'name'      =>  '_wpcc_post_password',
            ])
        </td>
    </tr>

    <?php

    /**
     * Fires before closing table tag in post tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/post', $settings, $isGeneralPage, $isOption);

    ?>

</table>