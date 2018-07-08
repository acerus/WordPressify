{{--
    Required variables:
        $activeSites: (WP_Post[])
--}}

@extends('dashboard.partials.section')

@section('content-class') @overwrite
@section('header') @overwrite

@section('title')
    {{ _wpcc("Active sites") }} ({{ sizeof($activeSites) }})
@overwrite

@section('content')
    @if(!empty($activeSites))
        <table class="section-table detail-card white">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ _wpcc("Last") }}</th>
                    <th>{{ _wpcc("Active") }}</th>
                    <th>{{ _wpcc("Today") }}</th>
                    <th>{{ _wpcc("All") }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeSites as $activeSite)
                    <tr>
                        <td class="site-name">
                            <a href="{!! get_edit_post_link($activeSite->ID) !!}" target="_blank">
                                {{ $activeSite->post_title }}
                            </a>
                        </td>
                        <td>
                            <?php
                                $lastEventDates = [
                                    _wpcc("URL Collection") => $activeSite->lastCheckedAt,
                                    _wpcc("Post Crawl")     => $activeSite->lastCrawledAt,
                                    _wpcc("Post Recrawl")   => $activeSite->lastRecrawledAt,
                                    _wpcc("Post Delete")    => $activeSite->lastDeletedAt
                                ];
                            ?>

                            @foreach($lastEventDates as $eventName => $dateStr)
                                <div><span>{{ $eventName }}:</span> <span class="diff-for-humans">{!! sprintf(_wpcc('%1$s ago'), \WPCCrawler\Utils::getDiffForHumans(strtotime($dateStr))) !!}</span> <span class="date">({{ \WPCCrawler\Utils::getDateFormatted($dateStr) }})</span> </div>
                            @endforeach
                        </td>
                        <td>
                            <?php
                                $activeStatuses = [
                                    _wpcc("Scheduling") => $activeSite->activeScheduling,
                                    _wpcc("Recrawling") => $activeSite->activeRecrawling,
                                    _wpcc("Deleting") => $activeSite->activeDeleting
                                ];
                            ?>

                            @foreach($activeStatuses as $eventName => $isActive)
                                <div><span>{{ $eventName }}</span>: <span class="dashicons dashicons-{{ $isActive ? 'yes' : 'no'}}"></span></div>
                            @endforeach
                        </td>
                        <td>
                            <?php
                                $countsToday = [
                                    _wpcc("Queue")   => $activeSite->countQueueToday,
                                    _wpcc("Saved")   => $activeSite->countSavedToday,
                                    _wpcc("Updated") => $activeSite->countRecrawledToday,
                                    _wpcc("Deleted") => $activeSite->countDeletedToday,
                                ];
                            ?>

                            @foreach($countsToday as $mName => $mValue)
                                <div><span>{{ $mName }}:</span> {{ $mValue }}</div>
                            @endforeach
                        </td>
                        <td>
                            <?php
                                $countsAll = [
                                    _wpcc("Queue")   => $activeSite->countQueue,
                                    _wpcc("Saved")   => $activeSite->countSaved,
                                    _wpcc("Updated") => $activeSite->countRecrawled,
                                    _wpcc("Deleted") => $activeSite->countDeleted,
                                ];
                            ?>

                            @foreach($countsAll as $mName => $mValue)
                                <div><span>{{ $mName }}:</span> {{ $mValue }}</div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else

        {{ _wpcc("No active sites.") }}

    @endif

@overwrite