@extends('theme::layouts.admin')

@section('title', __('messages.maintenance'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.maintenance') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.maintenance') }}</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="alert alert-warning border-start border-warning border-4 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-warning"></i>
                <div>
                    <strong>{{ __('messages.maintenance_warning') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(session('success'))
    <div class="col-12 mb-4">
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-check-circle me-2"></i>
                <div>{!! session('success') !!}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="col-12 mb-4">
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <div>{!! session('error') !!}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif
</div>

<div class="row g-4">
    <!-- Clear Cache -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-primary text-primary mb-3 mx-auto">
                    <i class="fa-solid fa-broom fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.clear_cache') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.clear_cache_desc') }}</p>
                <form action="{{ route('admin.maintenance.clear_cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary w-100 mt-auto">
                        <i class="fa-solid fa-play me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Run Migrations -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-success text-success mb-3 mx-auto">
                    <i class="fa-solid fa-database fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.run_migrations') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.run_migrations_desc') }}</p>
                <form action="{{ route('admin.maintenance.migrate') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-success w-100 mt-auto">
                        <i class="fa-solid fa-upload me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- DB Repair & Optimize -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-info text-info mb-3 mx-auto">
                    <i class="fa-solid fa-wrench fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.db_repair') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.db_repair_desc') }}</p>
                <form action="{{ route('admin.maintenance.db_repair') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-info w-100 mt-auto">
                        <i class="fa-solid fa-gears me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(97, 93, 250, 0.1); }
    .bg-soft-success { background-color: rgba(30, 197, 137, 0.1); }
    .bg-soft-info { background-color: rgba(0, 204, 255, 0.1); }
    .transition-hover:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    .avatar-lg { width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
</style>
@endsection
