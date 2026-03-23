@extends('theme::layouts.admin')

@section('title', __('messages.themes'))

@section('content')
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.themes') }}</h2>
        <p class="text-muted">{{ __('messages.themes_desc') ?? 'Manage the look and feel of your website.' }}</p>
    </div>
</div>

<div class="main-content container-lg px-4">
    <div class="row">
        @if(empty($themes))
            <div class="col-12">
                <div class="text-center py-5 card">
                    <div class="card-body">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mb-3 mx-auto">
                            <i class="feather-layout"></i>
                        </div>
                        <h4>{{ __('messages.no_themes_found') }}</h4>
                        <p class="text-muted">{{ __('messages.no_themes_desc') ?? 'No themes found in the themes directory.' }}</p>
                    </div>
                </div>
            </div>
        @else
            @foreach($themes as $theme)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card h-100 {{ $theme['is_active'] ? 'border-primary shadow-sm' : '' }}">
                    <div class="position-relative">
                        @if($theme['screenshot'])
                            <img src="{{ $theme['screenshot'] }}" class="card-img-top" alt="{{ $theme['name'] }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="feather-image text-muted display-4"></i>
                            </div>
                        @endif
                        
                        <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-1 align-items-end">
                            @if($theme['is_active'])
                                <span class="badge bg-primary">{{ __('messages.active') }}</span>
                            @endif
                            @if(isset($updates[$theme['slug']]))
                                <span class="badge bg-warning text-dark border border-dark">
                                    <i class="feather-arrow-up-circle me-1"></i>
                                    {{ __('messages.update_available') ?? 'Update Available' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $theme['name'] }}</h5>
                            <span class="badge bg-light text-dark">{{ $theme['version'] ?? '1.0' }}</span>
                        </div>
                        <p class="card-text text-muted small flex-grow-1">{{ $theme['description'] ?? '' }}</p>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ __('messages.by') }} {{ $theme['author'] ?? 'Unknown' }}</small>
                            
                            <div class="d-flex gap-2">
                                @if(isset($updates[$theme['slug']]))
                                    @if(isset($updates[$theme['slug']]['changelog']))
                                        <button type="button" class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#themeChangelogModal{{ $loop->index }}" title="{{ __('messages.view_changelog') }}">
                                            <i class="feather-info"></i>
                                        </button>
                                    @endif
                                @endif

                                @if(!$theme['is_active'])
                                    <form action="{{ route('admin.themes.activate') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="slug" value="{{ $theme['slug'] }}">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            {{ __('messages.activate') }}
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-soft-success" disabled>
                                        <i class="feather-check me-1"></i> {{ __('messages.Activated') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
@section('modals')
@if(!empty($themes))
    @foreach($themes as $theme)
        @if(isset($updates[$theme['slug']]) && isset($updates[$theme['slug']]['changelog']))
        <!-- Changelog Modal -->
        <div class="modal fade" id="themeChangelogModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.changelog') ?? 'Changelog' }} - {{ $theme['name'] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="bg-light p-4 rounded border">
                            <pre style="white-space: pre-wrap; font-family: inherit; margin-bottom: 0;">{{ $updates[$theme['slug']]['changelog'] }}</pre>
                        </div>
                        @if(isset($updates[$theme['slug']]['github_url']))
                            <div class="mt-3 text-center">
                                <a href="{{ $updates[$theme['slug']]['github_url'] }}" target="_blank" class="btn btn-sm btn-link">
                                    <i class="feather-github me-1"></i> {{ __('messages.view_on_github') ?? 'View on GitHub' }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif
@endsection

@endsection
