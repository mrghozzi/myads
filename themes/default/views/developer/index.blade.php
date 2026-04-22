@extends('theme::layouts.app')

@section('title', __('messages.dev_platform'))

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold mb-1">@lang('messages.dev_platform')</h1>
            <p class="text-muted">@lang('messages.dev_platform_desc')</p>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Documentation / General -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">@lang('messages.dev_docs')</h5>
                    <p>@lang('messages.dev_docs_intro')</p>
                    
                    <h6 class="fw-bold mt-4">1. OAuth 2.0 Authorization Code Flow</h6>
                    <p>@lang('messages.dev_oauth_desc')</p>
                    <pre class="bg-light p-3 rounded border"><code>GET /oauth/authorize?client_id=YOUR_CLIENT_ID&redirect_uri=YOUR_URL&response_type=code&scope=user.profile.read</code></pre>

                    <h6 class="fw-bold mt-4">2. Widget Integrations</h6>
                    <p>@lang('messages.dev_widgets_desc')</p>
                    <ul>
                        <li><strong>Follow Widget:</strong> <code>&lt;div id="myads-widget-follow-APPID"&gt;&lt;/div&gt;</code></li>
                        <li><strong>Profile Widget:</strong> <code>&lt;div id="myads-widget-profile-APPID"&gt;&lt;/div&gt;</code></li>
                        <li><strong>Content Widget:</strong> <code>&lt;div id="myads-widget-content-APPID"&gt;&lt;/div&gt;</code></li>
                    </ul>

                    <h6 class="fw-bold mt-4">3. Share API</h6>
                    <p>@lang('messages.dev_share_desc')</p>
                    <pre class="bg-light p-3 rounded border"><code>GET /share?text=Hello+World&url=https://example.com</code></pre>
                </div>
            </div>
        </div>

        <!-- App Management Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">@lang('messages.my_apps')</h5>
                    
                    @if(auth()->check())
                        @if($eligible)
                            @if(count($apps) > 0)
                                <div class="list-group mb-3">
                                    @foreach($apps as $app)
                                        <a href="{{ route('developer.apps.show', $app->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ $app->name }}</div>
                                                <small class="text-muted">{{ $app->status }}</small>
                                            </div>
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('developer.apps.index') }}" class="btn btn-outline-primary">@lang('messages.manage_apps')</a>
                                </div>
                            @else
                                <p class="text-muted">@lang('messages.no_apps_yet')</p>
                                <div class="d-grid">
                                    <a href="{{ route('developer.apps.create') }}" class="btn btn-primary">@lang('messages.create_app')</a>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> @lang('messages.dev_not_eligible')
                                @if($reason)
                                    <div class="mt-2 small">@lang('messages.dev_reason_' . $reason)</div>
                                @endif
                            </div>
                        @endif
                    @else
                        <p class="text-muted">@lang('messages.dev_login_required')</p>
                        <div class="d-grid">
                            <a href="{{ route('login') }}" class="btn btn-primary">@lang('messages.login')</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
