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
                        
                        @if($theme['is_active'])
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-primary">{{ __('messages.active') }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $theme['name'] }}</h5>
                            <span class="badge bg-light text-dark">{{ $theme['version'] ?? '1.0' }}</span>
                        </div>
                        <p class="card-text text-muted small flex-grow-1">{{ $theme['description'] ?? '' }}</p>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ __('messages.by') }} {{ $theme['author'] ?? 'Unknown' }}</small>
                            
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
            @endforeach
        @endif
    </div>
</div>
@endsection
