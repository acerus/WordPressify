@extends('dashboard.partials.section')

@section('content-class') whats-happening @overwrite
@section('header') @overwrite

@section('title')
    {{ _wpcc("What's happening") }}
@overwrite

@section('content')
    <?php $now = strtotime(current_time('mysql')); ?>

    {{-- CRON EVENTS --}}
    <h3>{{ _wpcc("CRON Events") }} <span>({{ sprintf(_wpcc('Now: %1$s'), \WPCCrawler\Utils::getDateFormatted(current_time('mysql'))) }})</span></h3>
    <table class="detail-card orange">
        <thead>
            <tr>
                <?php
                    $tableHeadValues = [
                        _wpcc("URL Collection") => \WPCCrawler\Factory::schedulingService()->eventCollectUrls,
                        _wpcc("Post Crawl")     => \WPCCrawler\Factory::schedulingService()->eventCrawlPost,
                        _wpcc("Post Recrawl")   => \WPCCrawler\Factory::schedulingService()->eventRecrawlPost,
                        _wpcc("Post Delete")    => \WPCCrawler\Factory::schedulingService()->eventDeletePosts,
                    ];
                ?>
                <th></th>
                @foreach($tableHeadValues as $name => $eventKey)
                    <th>
                        {{ $name }}
                        <div class="interval-description">{{ $dashboard->getCronEventIntervalDescription($eventKey) }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    $nextEventDates = [
                        [$dashboard->getNextUrlCollectionDate(),    $dashboard->getNextUrlCollectionSite()],
                        [$dashboard->getNextPostCrawlDate(),        $dashboard->getNextPostCrawlSite()],
                        [$dashboard->getNextPostRecrawlDate(),      $dashboard->getNextPostRecrawlSite()],
                        [$dashboard->getNextPostDeleteDate(),       $dashboard->getNextPostDeleteSite()],
                    ];
                ?>
                <td>{{ _wpcc("Next") }}</td>

                @foreach($nextEventDates as $v)
                    <?php $timestamp = strtotime($v[0]); ?>
                    <td>
                        <div class="diff-for-humans">
                            {{ \WPCCrawler\Utils::getDiffForHumans(strtotime($v[0])) }}
                            {{ $timestamp > $now ? _wpcc("later") : _wpcc("ago") }}
                        </div>
                        <span class="date">({{ \WPCCrawler\Utils::getDateFormatted($v[0]) }})</span>
                        @if($v[1])
                            <div class="next-site">
                                @include('dashboard.partials.site-link', ['site' => $v[1]])
                            </div>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td>{{ _wpcc("Last") }}</td>
                <?php
                    $lastEventDates = [
                        $dashboard->getLastUrlCollectionDate(),
                        $dashboard->getLastPostCrawlDate(),
                        $dashboard->getLastPostRecrawlDate(),
                        $dashboard->getLastPostDeleteDate(),
                    ];
                ?>
                @foreach($lastEventDates as $d)
                    <td><div class="diff-for-humans">{!! sprintf(_wpcc("%s ago"), \WPCCrawler\Utils::getDiffForHumans(strtotime($d))) !!}</div> <span class="date">({{ \WPCCrawler\Utils::getDateFormatted($d) }})</span> </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{-- COUNTS --}}
    <h3>{{ _wpcc("Counts") }}</h3>
    <table class="detail-card counts teal">
        <thead>
            <tr>
                <th></th>
                <th>{{ _wpcc("URLs in Queue") }}</th>
                <th>{{ _wpcc("Saved Posts") }}</th>
                <th>{{ _wpcc("Updated Posts") }}</th>
                <th>{{ _wpcc("Deleted Posts") }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ _wpcc("Today") }}</td>
                <td>{{ $dashboard->getTotalUrlsInQueueAddedToday() }}</td>
                <td>{{ $dashboard->getTotalSavedPostsToday() }}</td>
                <td>{{ $dashboard->getTotalRecrawledPostsToday() }}</td>
                <td>{{ $dashboard->getTotalDeletedPostsToday() }}</td>
            </tr>
            <tr>
                <td>{{ _wpcc("All") }}</td>
                <td>{{ $dashboard->getTotalUrlsInQueue() }}</td>
                <td>{{ $dashboard->getTotalSavedPosts() }}</td>
                <td>{{ $dashboard->getTotalRecrawledPosts() }}</td>
                <td>{{ $dashboard->getTotalDeletedPosts() }}</td>
            </tr>
        </tbody>
    </table>

    {{-- CURRENTLY - URLS --}}
    @if($dashboard->getUrlsCurrentlyBeingCrawled())
        <h3>{{ _wpcc("URLs being crawled right now") }}</h3>
        @include('dashboard.partials.table-urls', [
            'urls'          => $dashboard->getUrlsCurrentlyBeingCrawled(),
            'tableClass'    => 'detail-card green',
            'dateColumnName' => _wpcc('Created'),
            'fieldName' => 'created_at',
        ])

    @endif

    {{-- CURRENTLY - POSTS --}}
    @if($dashboard->getPostsCurrentlyBeingSaved())
        <h3>{{ _wpcc("Posts being saved right now") }}</h3>
        @include('dashboard.partials.table-posts', [
            'posts'         => $dashboard->getPostsCurrentlyBeingSaved(),
            'tableClass'    => 'detail-card green'
        ])

    @endif

@overwrite