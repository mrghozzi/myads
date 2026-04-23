@extends('theme::layouts.master')

@section('content')
    @include('theme::profile.partials.relationship_page', [
        'selectedTab' => 'following',
        'relationshipTitle' => __('messages.following'),
        'relationshipItems' => $following,
        'relationshipType' => 'following',
        'emptyMessage' => __('messages.no_following'),
    ])
@endsection
