@if($site)
    <span class="site">
        <a href="{!! get_edit_post_link($site->ID) !!}" target="_blank">
            {{ $site->post_title }}
        </a>
    </span>
@endif