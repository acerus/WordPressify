{{--
    Required variables:
        $urls: (object[])
        $dateColumName: (string) Name for the date column
        $fieldName: (string) Field name of a URL object used to get the date for the date column

    Optional variables:
        $tableClass: (string)
--}}

{{-- TABLE --}}
<table class="section-table {{ isset($tableClass) && $tableClass ? $tableClass : '' }}">

    {{-- THEAD --}}
    <thead>
        <tr>
            <th>{{ _wpcc("URL") }}</th>
            <th>{{ $dateColumnName }}</th>
        </tr>
    </thead>

    {{-- TBODY --}}
    <tbody>
        @foreach($urls as $url)
            <tr>
                {{-- URL --}}
                <td class="col-post">
                    {{-- URL --}}
                    <div class="post-title">
                        <a href="{!! $url->url !!}" target="_blank">
                            {!! mb_strlen($url->url) > 255 ? mb_substr($url->url, 0, 255) . "..." : $url->url !!}
                        </a>
                    </div>

                    {{-- DETAILS --}}
                    <div class="post-details">
                        {{-- SITE --}}
                        @if(isset($url->site))
                            @include('dashboard.partials.site-link', ['site' => $url->site])
                        @endif

                        {{-- ID --}}
                        <span class="id">
                            {{ _wpcc("ID") }}: {{ $url->id }}
                        </span>
                    </div>

                </td>

                {{-- DATE --}}
                <td class="col-date">
                    {{-- Diff for humans --}}
                    <span class="diff-for-humans">
                        {{ isset($url->{$fieldName}) ? \WPCCrawler\Utils::getDiffForHumans(strtotime($url->{$fieldName})) : "-" }}
                        {{ _wpcc("ago") }}
                    </span>

                    <span class="date">
                        ({{ isset($url->{$fieldName}) ? \WPCCrawler\Utils::getDateFormatted($url->{$fieldName}) : "-" }})
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>

</table>
