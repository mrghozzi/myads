@extends('theme::layouts.app')

@section('title', __('messages.authorize_app'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        @if($app->logo)
                            <img src="{{ asset($app->logo) }}" alt="{{ $app->name }}" class="rounded mb-3" style="width: 64px; height: 64px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                {{ substr($app->name, 0, 1) }}
                            </div>
                        @endif
                        <h4 class="fw-bold mb-1">{{ $app->name }}</h4>
                        <p class="text-muted small mb-0">{{ $app->domain }}</p>
                    </div>

                    <p class="text-center mb-4">
                        @lang('messages.app_wants_access', ['app' => '<strong>'.$app->name.'</strong>', 'site' => $site_settings->titer ?? 'MYADS'])
                    </p>

                    <div class="bg-light rounded p-3 mb-4">
                        <p class="fw-bold small mb-2 text-uppercase text-muted">@lang('messages.requested_permissions')</p>
                        <ul class="list-unstyled mb-0">
                            @foreach($scopeDetails as $scope)
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-check text-success mt-1 me-2"></i>
                                    <div>
                                        <strong>@lang($scope['name'])</strong>
                                        <div class="small text-muted">@lang($scope['description'])</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <form action="{{ route('oauth.authorize.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                        <input type="hidden" name="redirect_uri" value="{{ request('redirect_uri') }}">
                        <input type="hidden" name="response_type" value="{{ request('response_type') }}">
                        <input type="hidden" name="state" value="{{ request('state') }}">
                        <input type="hidden" name="scope" value="{{ request('scope') }}">

                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="accept" class="btn btn-primary btn-lg fw-bold">@lang('messages.authorize')</button>
                            <button type="submit" name="action" value="reject" class="btn btn-light">@lang('messages.cancel')</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted">@lang('messages.authorize_disclaimer')</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
