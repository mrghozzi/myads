@extends('theme::layouts.master')

@section('content')
    @include('theme::profile.partials.relationship_page', [
        'selectedTab' => 'followers',
        'relationshipTitle' => __('messages.Followers'),
        'relationshipItems' => $followers,
        'relationshipType' => 'followers',
        'emptyMessage' => __('messages.no_followers'),
    ])
@endsection
