@extends('theme::layouts.app')

@section('title', __('messages.authorized_apps'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3">
            @include('theme::partials.profile.settings_nav')
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom p-4">
                    <h1 class="h5 fw-bold mb-0">@lang('messages.authorized_apps')</h1>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">@lang('messages.authorized_apps_desc')</p>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(count($authorizations) > 0)
                        <div class="list-group list-group-flush border-top">
                            @foreach($authorizations as $auth)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        @if($auth->app->logo)
                                            <img src="{{ asset($auth->app->logo) }}" alt="" class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 20px;">
                                                <i class="fas fa-cube text-secondary"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $auth->app->name }}</h6>
                                            <div class="small text-muted">@lang('messages.authorized_on') {{ $auth->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                    <form action="{{ route('profile.apps.revoke', $auth->id) }}" method="POST" onsubmit="return confirm('@lang('messages.revoke_app_confirm')')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">@lang('messages.revoke_access')</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted p-5 bg-light rounded">
                            <i class="fas fa-shield-alt fa-3x mb-3 text-secondary"></i>
                            <p class="mb-0">@lang('messages.no_authorized_apps')</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
