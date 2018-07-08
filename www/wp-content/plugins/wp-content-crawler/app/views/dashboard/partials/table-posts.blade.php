{{--
    Required variables:
        $posts: (WP_Post[])

    Optional variables:
        $tableClass: (string)
--}}

<?php
    $isRecrawl = false;
    $isRecrawl = isset($type) && $type && $type != 'crawl' ? true : false;
    $now = strtotime(current_time('mysql'));
?>

{{-- TABLE --}}
<table class="section-table {{ isset($tableClass) && $tableClass ? $tableClass : '' }}">

    {{-- THEAD --}}
    <thead>
        <tr>
            <th>{{ _wpcc("Post") }}</th>
            <th>{{ $isRecrawl ? _wpcc("Recrawled") : _wpcc("Saved") }}</th>
            @if($isRecrawl)
                <th class="col-update-count">{{ _wpcc("Update Count") }}</th>
            @endif
        </tr>
    </thead>

    {{-- TBODY --}}
    <tbody>
        @foreach($posts as $post)
            @if(!isset($post->wpcc) || !isset($post->wpcc->site)) @continue @endif
            <tr>
                {{-- POST --}}
                <td class="col-post">
                    {{-- TITLE --}}
                    <div class="post-title">
                        <a href="{!! get_permalink($post->ID) !!}" target="_blank">
                            {{ $post->post_title }}
                        </a>

                        {{-- EDIT LINK --}}
                        <span class="edit-link">
                            - <a href="{!! get_edit_post_link($post->ID) !!}" target="_blank">
                                {{ _wpcc("Edit") }}
                            </a>
                        </span>
                    </div>

                    {{-- DETAILS --}}
                    <div class="post-details">
                        {{-- SITE --}}
                        @include('dashboard.partials.site-link', ['site' => $post->wpcc->site])

                        {{-- ID --}}
                        <span class="id">
                            {{ _wpcc("ID") }}: {{ $post->ID }}
                        </span> -

                        {{-- TARGET URL --}}
                        <span class="target-url">
                            <a href="{!! $post->wpcc->url !!}" target="_blank">
                                {!! mb_strlen($post->wpcc->url) > 255 ? mb_substr($post->wpcc->url, 0, 255) . "..." : $post->wpcc->url !!}
                            </a>
                        </span>
                    </div>

                </td>

                {{-- DATE --}}
                <td class="col-date">
                    {{-- Diff for humans --}}
                    <span class="diff-for-humans">
                        <?php
                            $timestamp = strtotime($isRecrawl ? $post->wpcc->recrawled_at : $post->wpcc->saved_at);
                        ?>
                        {{ \WPCCrawler\Utils::getDiffForHumans($timestamp) }}
                        {{ $timestamp > $now ? _wpcc("later") : _wpcc("ago") }}
                    </span>

                    <span class="date">
                        ({{ \WPCCrawler\Utils::getDateFormatted($isRecrawl ? $post->wpcc->recrawled_at : $post->wpcc->saved_at) }})
                    </span>
                </td>

                {{-- UPDATE COUNT --}}
                @if($isRecrawl)
                    <td class="col-update-count">
                        {{ $post->wpcc->update_count }}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>

</table>