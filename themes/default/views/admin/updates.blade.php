@extends('theme::layouts.admin')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ __('messages.updates_myads') ?? 'Updates MYads' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.admin_panel') ?? 'Admin' }}</a></li>
                <li class="breadcrumb-item">{{ __('messages.updates') ?? 'Updates' }}</li>
            </ul>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="feather-check-circle fs-4 me-2"></i>
            <div>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="feather-alert-circle fs-4 me-2"></i>
            <div>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="card stretch stretch-full">
        <div class="card-header">
            <h5 class="card-title">{{ __('messages.system_update') ?? 'System Update' }}</h5>
        </div>
        
        <div class="card-body">
            <div class="d-flex flex-column align-items-center justify-content-center text-center p-4">
                
                @if($latestVersion && version_compare($latestVersion, $currentVersion, '>'))
                    <!-- Update Available -->
                    <div class="alert alert-soft-warning w-100 mw-500">
                        <div class="d-flex flex-column align-items-center gap-3">
                            <i class="feather-alert-triangle fs-1 text-warning"></i>
                            <h2 class="fs-4 fw-bold text-dark">{{ __('messages.new_version_available') ?? 'New Version Available!' }}</h2>
                            <p class="fs-14 text-muted">
                                {{ __('messages.current_version') ?? 'Current Version:' }} <strong>v{{ $currentVersion }}</strong><br>
                                {{ __('messages.latest_version') ?? 'Latest Version:' }} <strong>v{{ $latestVersion }}</strong>
                            </p>
                            
                            <div class="mt-2">
                                <div class="mb-3 fs-12 text-muted">
                                    <p>{{ __('messages.update_warning') ?? 'Important: Please backup your database and files before updating.' }}</p>
                                </div>
                                
                                <form action="{{ route('admin.updates.process') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                        <i class="feather-download me-2"></i> {{ __('messages.update_now') ?? 'Update Now' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                @else
                    <!-- No Updates -->
                    <div class="alert alert-soft-success w-100 mw-500">
                        <div class="d-flex flex-column align-items-center gap-3">
                            <i class="feather-check-circle fs-1 text-success"></i>
                            <h2 class="fs-4 fw-bold text-dark">{{ __('messages.system_up_to_date') ?? 'Your System is Up to Date' }}</h2>
                            <p class="fs-14 text-muted">
                                {{ __('messages.current_version') ?? 'Current Version:' }} <strong>v{{ $currentVersion }}</strong>
                            </p>
                            <a href="{{ route('admin.updates') }}" class="btn btn-outline-success btn-sm mt-2">
                                <i class="feather-refresh-cw me-2"></i> {{ __('messages.check_again') ?? 'Check Again' }}
                            </a>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
@endsection
