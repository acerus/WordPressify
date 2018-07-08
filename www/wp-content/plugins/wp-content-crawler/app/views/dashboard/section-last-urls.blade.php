{{--
    Required variables:
        $urls: (object[]) An array of URL tuple objects with each having 'site' key storing a site as a WP_Post instance
--}}

@extends('dashboard.partials.section')

@section('content-class') @overwrite

@section('header')
    @include('dashboard.partials.select-table-item-count')
@overwrite

@section('title')
    {{ $title }}
@overwrite

@section('content')
    @if(!empty($urls))
        @include('dashboard.partials.table-urls', [
            'urls'              => $urls,
            'fieldName'         => $fieldName,
            'dateColumnName'    => $dateColumnName,
        ])

    @else

        {{ _wpcc("No URLs.") }}

    @endif

@overwrite