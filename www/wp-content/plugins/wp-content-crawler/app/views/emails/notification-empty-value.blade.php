<div>
    {{ _wpcc("Empty CSS selectors are found at following URL") }}: @if(isset($url) && $url) <a href="{{ $url }}">{{ $url }}</a> @endif
</div>

<br>

<div>
    @if(isset($messagesEmptyValue) && $messagesEmptyValue)
        <b>{{ _wpcc("CSS selectors with empty values") }}:</b><br>

        <div style="margin-left: 10px;">
        @foreach($messagesEmptyValue as $message)
            {!! $message !!}<br>
        @endforeach
        </div>
    @endif
</div>

<br>

<div>
    @if(isset($site) && $site)
        <?php
            /** @var WP_Post $site */
            $siteName = $site->post_title;
            $adminUrl = admin_url();
            $editPostLink = trim($adminUrl, '/') . '/post.php?post=' . $site->ID . '&action=edit';
        ?>
        {{ _wpcc("Site name") }}: <b>{{ $siteName }}</b><br>
        {{ _wpcc("Site settings") }}: <b><a href="{{ $editPostLink }}">{{ $editPostLink }}</a></b><br>
        {{ _wpcc("Admin dashboard") }}: <b><a href="{{ $adminUrl }}">{{ $adminUrl }}</a></b><br>
    @endif
</div>