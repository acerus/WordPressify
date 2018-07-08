{{--
    Required variables:
        $title: (string)
        $posts: (\WP_Post[]),
        $countOptionName: (string) Option name that will be used to show a select element that can be used to select how many items can be shown

    Optional variables:
        $type: (string) 'recrawl' or 'crawl'
--}}

<?php
    $isRecrawl = false;
    $isRecrawl = isset($type) && $type && $type != 'crawl' ? true : false;
?>

@extends('dashboard.partials.section')

@section('content-class') @overwrite

@section('header')
    @include('dashboard.partials.select-table-item-count')
@overwrite

@section('title')
    {{ $title }}
@overwrite

@section('content')

    @if(!empty($posts))
        @include('dashboard.partials.table-posts', [
            'posts' => $posts
        ])

    @else

        {{ _wpcc("No posts.") }}

    @endif

@overwrite