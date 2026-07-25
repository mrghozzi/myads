@extends('admin::layouts.admin')

@section('title', __('messages.media_manager'))

@section('content')
<div class="admin-page media-manager-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.media_manager') }}</li>
            </ul>
            <div class="admin-hero__eyebrow"><i class="fa-solid fa-photo-film me-1"></i> {{ __('messages.media_monitoring') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.media_manager') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.media_monitoring_desc') ?? 'Monitor, upload, and organize media assets across your system.' }}</p>
        </div>
        <div class="admin-hero__actions d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
            <button type="button" class="btn btn-primary btn-hero-action" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i>{{ __('messages.upload_media') }}
            </button>
            <form action="{{ route('admin.media.clear_cache') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light text-dark bg-white border border-secondary-subtle btn-hero-action" title="{{ __('messages.clear_media_cache') }}">
                    <i class="fa-solid fa-arrows-rotate me-1 text-primary"></i> {{ __('messages.clear_media_cache') }}
                </button>
            </form>
        </div>
    </section>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check fs-5 me-3"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation fs-5 me-3"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Storage Analytics Overview Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Storage -->
        <div class="col-6 col-md-4 col-xl-2-4">
            <div class="media-stat-card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon-badge bg-primary-soft text-primary me-3">
                        <i class="fa-solid fa-hard-drive"></i>
                    </div>
                    <div>
                        <span class="text-muted smaller fw-bold text-uppercase d-block">{{ __('messages.storage_used') }}</span>
                        <h4 class="fw-bold mb-0 text-dark fs-5">{{ $stats['total_size_formatted'] }}</h4>
                    </div>
                </div>
                <div class="text-muted smaller d-flex align-items-center justify-content-between pt-2 border-top border-light">
                    <span>{{ __('messages.total_files') }}:</span>
                    <span class="badge bg-primary text-white rounded-pill px-2">{{ $stats['total_files'] }}</span>
                </div>
            </div>
        </div>
        <!-- Images -->
        <div class="col-6 col-md-4 col-xl-2-4">
            <div class="media-stat-card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon-badge bg-cyan-soft text-cyan me-3">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <div>
                        <span class="text-muted smaller fw-bold text-uppercase d-block">{{ __('messages.images') }}</span>
                        <h4 class="fw-bold mb-0 text-dark fs-5">{{ $stats['images_size_formatted'] }}</h4>
                    </div>
                </div>
                <div class="text-muted smaller d-flex align-items-center justify-content-between pt-2 border-top border-light">
                    <span>{{ __('messages.files') }}:</span>
                    <span class="badge bg-info text-white rounded-pill px-2">{{ $stats['images_count'] }}</span>
                </div>
            </div>
        </div>
        <!-- Videos -->
        <div class="col-6 col-md-4 col-xl-2-4">
            <div class="media-stat-card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon-badge bg-amber-soft text-amber me-3">
                        <i class="fa-solid fa-film"></i>
                    </div>
                    <div>
                        <span class="text-muted smaller fw-bold text-uppercase d-block">{{ __('messages.videos') }}</span>
                        <h4 class="fw-bold mb-0 text-dark fs-5">{{ $stats['videos_size_formatted'] }}</h4>
                    </div>
                </div>
                <div class="text-muted smaller d-flex align-items-center justify-content-between pt-2 border-top border-light">
                    <span>{{ __('messages.files') }}:</span>
                    <span class="badge bg-warning text-dark rounded-pill px-2">{{ $stats['videos_count'] }}</span>
                </div>
            </div>
        </div>
        <!-- Audio -->
        <div class="col-6 col-md-4 col-xl-2-4">
            <div class="media-stat-card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon-badge bg-success-soft text-success me-3">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <div>
                        <span class="text-muted smaller fw-bold text-uppercase d-block">{{ __('messages.audio') }}</span>
                        <h4 class="fw-bold mb-0 text-dark fs-5">{{ $stats['audio_size_formatted'] }}</h4>
                    </div>
                </div>
                <div class="text-muted smaller d-flex align-items-center justify-content-between pt-2 border-top border-light">
                    <span>{{ __('messages.files') }}:</span>
                    <span class="badge bg-success text-white rounded-pill px-2">{{ $stats['audio_count'] }}</span>
                </div>
            </div>
        </div>
        <!-- Archives & Docs -->
        <div class="col-6 col-md-4 col-xl-2-4">
            <div class="media-stat-card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center mb-2">
                    <div class="stat-icon-badge bg-purple-soft text-purple me-3">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <div>
                        <span class="text-muted smaller fw-bold text-uppercase d-block">{{ __('messages.archives') }}</span>
                        <h4 class="fw-bold mb-0 text-dark fs-5">{{ $stats['archives_size_formatted'] }}</h4>
                    </div>
                </div>
                <div class="text-muted smaller d-flex align-items-center justify-content-between pt-2 border-top border-light">
                    <span>{{ __('messages.files') }}:</span>
                    <span class="badge bg-secondary text-white rounded-pill px-2">{{ $stats['archives_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Toolbar Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.media') }}" method="GET" id="mediaFilterForm" class="row g-3 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="input-group search-input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('messages.search') }}..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Directory Filter -->
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="directory" class="form-select" onchange="document.getElementById('mediaFilterForm').submit()">
                        <option value="all" {{ request('directory') == 'all' || !request('directory') ? 'selected' : '' }}>{{ __('messages.all_directories') }}</option>
                        <option value="public_upload" {{ request('directory') == 'public_upload' ? 'selected' : '' }}>{{ __('messages.public_upload') }}</option>
                        <option value="upload" {{ request('directory') == 'upload' ? 'selected' : '' }}>{{ __('messages.internal_upload') }}</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="type" class="form-select" onchange="document.getElementById('mediaFilterForm').submit()">
                        <option value="all" {{ request('type') == 'all' || !request('type') ? 'selected' : '' }}>{{ __('messages.all_types') ?? 'All Types' }}</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>🖼️ {{ __('messages.images') }}</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>🎬 {{ __('messages.videos') }}</option>
                        <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>🎵 {{ __('messages.audio') }}</option>
                        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>📦 {{ __('messages.archives') }}</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>📄 {{ __('messages.documents') }}</option>
                        <option value="code" {{ request('type') == 'code' ? 'selected' : '' }}>⚡ Code / Scripts</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="col-6 col-md-4 col-lg-3">
                    <select name="sort" class="form-select" onchange="document.getElementById('mediaFilterForm').submit()">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>📅 {{ __('messages.newest') }}</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>📅 {{ __('messages.oldest') }}</option>
                        <option value="size_desc" {{ request('sort') == 'size_desc' ? 'selected' : '' }}>📊 {{ __('messages.size_desc') }}</option>
                        <option value="size_asc" {{ request('sort') == 'size_asc' ? 'selected' : '' }}>📊 {{ __('messages.size_asc') }}</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>🔤 {{ __('messages.name_asc') }}</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>🔤 {{ __('messages.name_desc') }}</option>
                    </select>
                </div>

                <!-- View Switcher -->
                <div class="col-6 col-md-2 col-lg-2 d-flex justify-content-end">
                    <div class="btn-group view-switcher-group w-100" role="group" aria-label="View Switcher">
                        <button type="button" class="btn btn-outline-secondary active" id="btnGridView" title="{{ __('messages.grid_view') }}">
                            <i class="fa-solid fa-border-all"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnListView" title="{{ __('messages.list_view') }}">
                            <i class="fa-solid fa-list"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Selection Bar -->
    <div id="bulkActionBar" class="card border-0 shadow-lg rounded-4 mb-4 bg-dark text-white collapse">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                </div>
                <span class="fw-semibold me-3"><span id="selectedCount">0</span> {{ __('messages.selected_files') }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                    <i class="fa-solid fa-trash-can me-1"></i> {{ __('messages.bulk_delete') }}
                </button>
                <button type="button" class="btn btn-link text-white text-decoration-none btn-sm" id="btnDeselectAll">
                    {{ __('messages.cancel') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Media Container: Grid & List Modes -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">

            @if($files->count() > 0)
                <!-- 1. GRID VIEW CONTAINER (Superdesign Architecture) -->
                <div id="mediaGridView" class="row g-3">
                    @foreach($files as $file)
                        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                            <div class="superdesign-card border shadow-xs" style="position: relative !important; height: 230px !important; min-height: 230px !important; max-height: 230px !important; border-radius: 16px !important; overflow: hidden !important; background: #ffffff !important; display: flex !important; flex-direction: column !important; margin: 0 !important; padding: 0 !important;">
                                
                                <!-- Top Bar Badges -->
                                <div class="superdesign-top-bar d-flex justify-content-between align-items-center p-2 position-absolute top-0 start-0 end-0" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; width: 100% !important; display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 10px !important; z-index: 10 !important; pointer-events: none !important;">
                                    <div class="form-check m-0" style="pointer-events: auto !important;">
                                        <input class="form-check-input file-select-item shadow-sm cursor-pointer" type="checkbox" value="{{ $file['path'] }}">
                                    </div>
                                    <div style="pointer-events: auto !important;">
                                        @if($file['directory'] == 'upload')
                                            <span class="badge bg-dark-glass text-white border border-white-20 rounded-pill smaller px-2 shadow-xs">Internal</span>
                                        @else
                                            <span class="badge bg-success-glass text-white border border-success-20 rounded-pill smaller px-2 shadow-xs">Public</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Media Preview Zone (Fixed 155px Height) -->
                                <div class="superdesign-preview-box position-relative overflow-hidden" style="position: relative !important; width: 100% !important; height: 155px !important; min-height: 155px !important; max-height: 155px !important; overflow: hidden !important; background: #0f172a !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 !important; padding: 0 !important;">
                                    @if($file['is_image'] && $file['url'])
                                        <div class="superdesign-img-container w-100 h-100" style="width: 100% !important; height: 155px !important; max-height: 155px !important; overflow: hidden !important;">
                                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="superdesign-img" style="width: 100% !important; height: 155px !important; min-height: 155px !important; max-height: 155px !important; object-fit: cover !important; object-position: center !important; display: block !important; margin: 0 !important; padding: 0 !important;" loading="lazy">
                                        </div>
                                    @elseif($file['is_video'])
                                        <div class="superdesign-tile superdesign-tile-video w-100 h-100 text-white" style="width: 100% !important; height: 155px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%) !important;">
                                            <i class="fa-solid fa-circle-play fs-1 mb-1"></i>
                                            <span class="badge bg-black-50 text-uppercase smaller px-2 py-1">Video</span>
                                        </div>
                                    @elseif($file['is_audio'])
                                        <div class="superdesign-tile superdesign-tile-audio w-100 h-100 text-white" style="width: 100% !important; height: 155px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important;">
                                            <i class="fa-solid fa-file-audio fs-1 mb-1"></i>
                                            <span class="badge bg-black-50 text-uppercase smaller px-2 py-1">Audio</span>
                                        </div>
                                    @elseif($file['is_archive'])
                                        <div class="superdesign-tile superdesign-tile-archive w-100 h-100 text-white" style="width: 100% !important; height: 155px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #6366f1 0%, #3730a3 100%) !important;">
                                            <i class="fa-solid fa-file-zipper fs-1 mb-1"></i>
                                            <span class="badge bg-black-50 text-uppercase smaller px-2 py-1">{{ strtoupper($file['extension']) }}</span>
                                        </div>
                                    @elseif($file['extension'] === 'pdf')
                                        <div class="superdesign-tile superdesign-tile-pdf w-100 h-100 text-white" style="width: 100% !important; height: 155px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%) !important;">
                                            <i class="fa-solid fa-file-pdf fs-1 mb-1"></i>
                                            <span class="badge bg-black-50 text-uppercase smaller px-2 py-1">PDF</span>
                                        </div>
                                    @elseif($file['is_code'])
                                        <div class="superdesign-tile superdesign-tile-code w-100 h-100 text-white" style="width: 100% !important; height: 155px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #8b5cf6 0%, #5b21b6 100%) !important;">
                                            <i class="fa-solid fa-code fs-1 mb-1"></i>
                                            <span class="badge bg-black-50 text-uppercase smaller px-2 py-1">{{ strtoupper($file['extension']) }}</span>
                                        </div>
                                    @else
                                        <div class="superdesign-tile superdesign-tile-generic w-100 h-100 text-secondary" style="width: 100% !important; height: 155px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;">
                                            <i class="fa-solid fa-file fs-1 mb-1"></i>
                                            <span class="badge bg-secondary text-white text-uppercase smaller px-2 py-1">{{ strtoupper($file['extension']) }}</span>
                                        </div>
                                    @endif

                                    <!-- Quick Action Hover Overlay -->
                                    <div class="superdesign-hover-overlay">
                                        <button type="button" class="sd-action-btn preview-btn" 
                                                data-url="{{ $file['url'] }}" 
                                                data-name="{{ $file['name'] }}"
                                                data-ext="{{ $file['extension'] }}"
                                                data-size="{{ $file['size'] }}"
                                                data-path="{{ $file['path'] }}"
                                                data-is-image="{{ $file['is_image'] ? '1' : '0' }}"
                                                data-is-video="{{ $file['is_video'] ? '1' : '0' }}"
                                                data-is-audio="{{ $file['is_audio'] ? '1' : '0' }}"
                                                data-is-code="{{ $file['is_code'] ? '1' : '0' }}"
                                                title="{{ __('messages.preview') }}">
                                            <i class="fa-solid fa-eye text-primary"></i>
                                        </button>
                                        @if($file['url'])
                                            <button type="button" class="sd-action-btn copy-url-btn" data-url="{{ $file['url'] }}" title="{{ __('messages.copy_url') }}">
                                                <i class="fa-solid fa-link text-info"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="sd-action-btn rename-btn" data-path="{{ $file['path'] }}" data-name="{{ $file['name'] }}" title="{{ __('messages.rename') }}">
                                            <i class="fa-solid fa-pen-to-square text-secondary"></i>
                                        </button>
                                        <button type="button" class="sd-action-btn delete-single-btn" data-path="{{ $file['path'] }}" data-name="{{ $file['name'] }}" title="{{ __('messages.delete') }}">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Footer File Info -->
                                <div class="superdesign-card-footer bg-white border-top mt-auto" style="height: 75px !important; min-height: 75px !important; max-height: 75px !important; padding: 10px 14px !important; display: flex !important; flex-direction: column !important; justify-content: center !important;">
                                    <div class="fw-bold text-dark text-truncate small mb-1" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-secondary text-uppercase smaller border">{{ $file['extension'] }}</span>
                                        <span class="text-muted smaller fw-medium">{{ $file['size'] }}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 2. LIST VIEW CONTAINER (Hidden by default) -->
                <div id="mediaListView" class="table-responsive d-none">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="40" class="ps-3 border-0">
                                    <input class="form-check-input" type="checkbox" id="listSelectAll">
                                </th>
                                <th class="border-0">{{ __('messages.file_name') }}</th>
                                <th class="border-0">{{ __('messages.file_type') }}</th>
                                <th class="border-0">{{ __('messages.file_size') }}</th>
                                <th class="border-0">{{ __('messages.location') }}</th>
                                <th class="border-0">{{ __('messages.date') }}</th>
                                <th class="pe-4 border-0 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td class="ps-3">
                                        <input class="form-check-input file-select-item" type="checkbox" value="{{ $file['path'] }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-wrap me-3 rounded-3 bg-light p-2 text-center" style="width: 44px; height: 44px;">
                                                @if($file['is_image'] && $file['url'])
                                                    <img src="{{ $file['url'] }}" alt="{{ $file['extension'] }}" class="rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <img src="{{ $file['icon'] }}" alt="{{ $file['extension'] }}" width="28">
                                                @endif
                                            </div>
                                            <div>
                                                @if($file['url'])
                                                    <a href="{{ $file['url'] }}" target="_blank" class="fw-bold text-dark text-decoration-none file-name-link">{{ $file['name'] }}</a>
                                                @else
                                                    <span class="fw-bold text-dark">{{ $file['name'] }}</span>
                                                @endif
                                                <div class="text-muted smaller text-truncate" style="max-width: 320px;">{{ $file['path'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border text-uppercase">{{ $file['extension'] }}</span>
                                    </td>
                                    <td><span class="fw-medium text-dark">{{ $file['size'] }}</span></td>
                                    <td>
                                        @if($file['directory'] == 'upload')
                                            <span class="badge bg-secondary-subtle text-secondary border">Internal /upload</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border">Public /upload</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-medium">{{ date('Y-m-d', $file['last_modified']) }}</div>
                                        <div class="text-muted smaller">{{ date('H:i', $file['last_modified']) }}</div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-light border preview-btn" 
                                                    data-url="{{ $file['url'] }}" 
                                                    data-name="{{ $file['name'] }}"
                                                    data-ext="{{ $file['extension'] }}"
                                                    data-size="{{ $file['size'] }}"
                                                    data-path="{{ $file['path'] }}"
                                                    data-is-image="{{ $file['is_image'] ? '1' : '0' }}"
                                                    data-is-video="{{ $file['is_video'] ? '1' : '0' }}"
                                                    data-is-audio="{{ $file['is_audio'] ? '1' : '0' }}"
                                                    data-is-code="{{ $file['is_code'] ? '1' : '0' }}"
                                                    title="{{ __('messages.preview') }}">
                                                <i class="fa-solid fa-eye text-primary"></i>
                                            </button>
                                            @if($file['url'])
                                                <button type="button" class="btn btn-sm btn-light border copy-url-btn" data-url="{{ $file['url'] }}" title="{{ __('messages.copy_url') }}">
                                                    <i class="fa-solid fa-link text-info"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-light border rename-btn" data-path="{{ $file['path'] }}" data-name="{{ $file['name'] }}" title="{{ __('messages.rename') }}">
                                                <i class="fa-solid fa-pen-to-square text-secondary"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light border text-danger delete-single-btn" data-path="{{ $file['path'] }}" data-name="{{ $file['name'] }}" title="{{ __('messages.delete') }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <!-- EMPTY STATE -->
                <div class="text-center py-5">
                    <div class="avatar avatar-xl bg-light text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-folder-open fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ __('messages.no_media_found') ?? 'No media files found' }}</h5>
                    <p class="text-muted smaller mb-3">Try adjusting your filter search or upload new files to your system.</p>
                    <button type="button" class="btn btn-primary rounded-3 px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i>{{ __('messages.upload_media') }}
                    </button>
                </div>
            @endif

        </div>

        <!-- Pagination Footer -->
        @if($files->hasPages())
            <div class="card-footer bg-white border-0 py-3 rounded-bottom-4">
                <div class="d-flex justify-content-center">
                    {{ $files->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- TOAST CONTAINER FOR URL COPY FEEDBACK -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="mediaToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
                <i class="fa-solid fa-circle-check text-success fs-5 me-2"></i>
                <span id="toastMessage">{{ __('messages.url_copied') }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- 1. UPLOAD MEDIA MODAL -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <i class="fa-solid fa-cloud-arrow-up text-primary me-2 fs-4"></i> {{ __('messages.upload_media') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="modal-body py-4">
                    <!-- Directory Selector -->
                    <div class="mb-3">
                        <label for="target_dir" class="form-label fw-semibold smaller text-muted text-uppercase">{{ __('messages.location') }}</label>
                        <select name="target_dir" id="target_dir" class="form-select">
                            <option value="public_upload" selected>Public Directory (/public/upload) - Recommended</option>
                            <option value="upload">Internal Root Directory (/upload)</option>
                        </select>
                    </div>

                    <!-- Dropzone Container -->
                    <div class="dropzone-area rounded-4 border-2 border-dashed p-5 text-center bg-light position-relative" id="dropzoneArea">
                        <input type="file" name="files[]" id="fileInput" class="position-absolute inset-0 opacity-0 cursor-pointer w-100 h-100" multiple required>
                        <div class="dropzone-icon mb-3">
                            <i class="fa-solid fa-cloud-arrow-up text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ __('messages.drop_files_here') }}</h6>
                        <p class="text-muted smaller mb-0">Supports Images, Videos, Audio, Archives, and Documents up to 50MB per file.</p>
                        <div id="fileSelectedList" class="mt-3 text-start d-none">
                            <div class="fw-semibold smaller text-dark mb-2">Selected Files:</div>
                            <ul class="list-group list-group-flush rounded-3 border smaller" id="fileListNames"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4" id="btnSubmitUpload">
                        <i class="fa-solid fa-upload me-1"></i> {{ __('messages.upload_media') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MULTI-MEDIA PREVIEW MODAL -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold text-truncate pe-3" id="previewTitle">{{ __('messages.media_preview') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Viewer Screen (Left/Top) -->
                    <div class="col-12 col-lg-8 bg-dark d-flex align-items-center justify-content-center p-3" style="min-height: 380px; max-height: 70vh;">
                        <!-- Image Container -->
                        <div id="previewImageContainer" class="w-100 h-100 d-none text-center">
                            <img src="" id="previewImage" alt="Preview" class="img-fluid rounded" style="max-height: 65vh; object-fit: contain;">
                        </div>
                        <!-- Video Container -->
                        <div id="previewVideoContainer" class="w-100 h-100 d-none text-center">
                            <video id="previewVideo" controls class="w-100 rounded" style="max-height: 65vh;">
                                <source src="" type="video/mp4">
                                Your browser does not support video preview.
                            </video>
                        </div>
                        <!-- Audio Container -->
                        <div id="previewAudioContainer" class="w-100 d-none text-center py-5">
                            <div class="avatar avatar-xl bg-primary text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                                <i class="fa-solid fa-music fs-1"></i>
                            </div>
                            <audio id="previewAudio" controls class="w-75 mx-auto">
                                <source src="" type="audio/mpeg">
                                Your browser does not support audio preview.
                            </audio>
                        </div>
                        <!-- Fallback / Code Container -->
                        <div id="previewFallbackContainer" class="w-100 text-center p-4 d-none">
                            <i class="fa-solid fa-file-lines text-muted display-1 mb-3"></i>
                            <p class="text-white-50">Direct inline preview is not supported for this file type.</p>
                        </div>
                    </div>

                    <!-- Metadata Sidebar (Right/Bottom) -->
                    <div class="col-12 col-lg-4 p-4 bg-white d-flex flex-column justify-content-between border-start">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted smaller mb-3"><i class="fa-solid fa-circle-info me-1"></i> {{ __('messages.file_details') }}</h6>
                            <dl class="row g-2 smaller mb-0">
                                <dt class="col-4 text-muted">File Name:</dt>
                                <dd class="col-8 fw-semibold text-dark text-break" id="metaFileName"></dd>

                                <dt class="col-4 text-muted">Extension:</dt>
                                <dd class="col-8"><span class="badge bg-light text-dark border text-uppercase" id="metaFileExt"></span></dd>

                                <dt class="col-4 text-muted">File Size:</dt>
                                <dd class="col-8 fw-medium text-dark" id="metaFileSize"></dd>

                                <dt class="col-4 text-muted">Path:</dt>
                                <dd class="col-8 text-muted text-break" id="metaFilePath"></dd>
                            </dl>
                        </div>

                        <div class="pt-4 border-top mt-3 d-grid gap-2">
                            <button type="button" class="btn btn-outline-info rounded-3" id="btnPreviewCopyUrl">
                                <i class="fa-solid fa-link me-1"></i> {{ __('messages.copy_url') }}
                            </button>
                            <a href="" id="previewDownload" class="btn btn-primary rounded-3" target="_blank" download>
                                <i class="fa-solid fa-download me-1"></i> {{ __('messages.download') ?? 'Download' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. RENAME MODAL -->
<div class="modal fade" id="renameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square text-secondary me-2"></i>{{ __('messages.rename') ?? 'Rename File' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.media.rename') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <input type="hidden" name="path" id="renamePath">
                    <div class="mb-3">
                        <label for="new_name" class="form-label fw-semibold smaller text-muted text-uppercase">{{ __('messages.new_name') ?? 'New Name' }}</label>
                        <input type="text" class="form-control rounded-3" name="new_name" id="renameName" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. SINGLE DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="singleDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-3">
            <div class="modal-body pt-4">
                <div class="avatar avatar-lg bg-danger-soft text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-trash-can fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ __('messages.delete') }}</h5>
                <p class="text-muted smaller mb-4">Are you sure you want to delete <strong id="deleteSingleFileName"></strong>? This action cannot be undone.</p>
                <form action="{{ route('admin.media.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="path" id="deleteSinglePath">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-3 w-50" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-danger rounded-3 w-50">{{ __('messages.delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 5. BULK DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-3">
            <div class="modal-body pt-4">
                <div class="avatar avatar-lg bg-danger-soft text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-triangle-exclamation fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ __('messages.bulk_delete') }}</h5>
                <p class="text-muted smaller mb-4">{{ __('messages.confirm_bulk_delete') }}</p>
                <form action="{{ route('admin.media.bulk_delete') }}" method="POST" id="bulkDeleteForm">
                    @csrf
                    <div id="bulkDeleteInputs"></div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-3 w-50" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-danger rounded-3 w-50">{{ __('messages.bulk_delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Superdesign Media Manager Styles */
    .col-xl-2-4 { flex: 0 0 auto; width: 20%; }
    @media (max-width: 1200px) { .col-xl-2-4 { width: 33.333%; } }
    @media (max-width: 768px) { .col-xl-2-4 { width: 50%; } }

    .media-stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .media-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.06) !important;
    }

    .stat-icon-badge {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .bg-primary-soft { background: rgba(97, 93, 250, 0.12); }
    .text-primary { color: #615dfa !important; }
    .bg-cyan-soft { background: rgba(35, 210, 226, 0.12); }
    .text-cyan { color: #23d2e2 !important; }
    .bg-amber-soft { background: rgba(251, 191, 36, 0.15); }
    .text-amber { color: #d97706 !important; }
    .bg-success-soft { background: rgba(79, 244, 97, 0.15); }
    .text-success { color: #16a34a !important; }
    .bg-purple-soft { background: rgba(139, 92, 246, 0.12); }
    .text-purple { color: #8b5cf6 !important; }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.12); }

    /* SUPERDESIGN MEDIA GRID STYLES */
    #mediaGridView .superdesign-card {
        position: relative !important;
        height: 220px !important;
        min-height: 220px !important;
        max-height: 220px !important;
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        overflow: hidden !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        display: flex !important;
        flex-direction: column !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #mediaGridView .superdesign-card:hover {
        transform: translateY(-4px) !important;
        border-color: #615dfa !important;
        box-shadow: 0 16px 32px rgba(97, 93, 250, 0.16) !important;
    }

    #mediaGridView .superdesign-top-bar {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 8px 10px !important;
        z-index: 10 !important;
        pointer-events: none !important;
    }

    #mediaGridView .superdesign-top-bar * {
        pointer-events: auto !important;
    }

    .bg-dark-glass {
        background: rgba(15, 23, 42, 0.75) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }
    .bg-success-glass {
        background: rgba(16, 185, 129, 0.85) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }
    .border-white-20 { border-color: rgba(255, 255, 255, 0.2) !important; }
    .border-success-20 { border-color: rgba(255, 255, 255, 0.3) !important; }

    #mediaGridView .superdesign-preview-box {
        position: relative !important;
        width: 100% !important;
        height: 145px !important;
        min-height: 145px !important;
        max-height: 145px !important;
        overflow: hidden !important;
        background-color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #mediaGridView .superdesign-img-container {
        width: 100% !important;
        height: 145px !important;
        max-height: 145px !important;
        overflow: hidden !important;
    }

    #mediaGridView .superdesign-img {
        width: 100% !important;
        height: 145px !important;
        min-height: 145px !important;
        max-height: 145px !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        transition: transform 0.35s ease !important;
    }

    #mediaGridView .superdesign-card:hover .superdesign-img {
        transform: scale(1.08) !important;
    }

    #mediaGridView .superdesign-tile {
        width: 100% !important;
        height: 145px !important;
        min-height: 145px !important;
        max-height: 145px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .superdesign-tile-pdf { background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%) !important; }
    .superdesign-tile-video { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%) !important; }
    .superdesign-tile-audio { background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important; }
    .superdesign-tile-archive { background: linear-gradient(135deg, #6366f1 0%, #3730a3 100%) !important; }
    .superdesign-tile-code { background: linear-gradient(135deg, #8b5cf6 0%, #5b21b6 100%) !important; }
    .superdesign-tile-generic { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important; }

    .bg-black-50 { background: rgba(0, 0, 0, 0.45) !important; }
    .shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important; }

    /* Hover Overlay Strict Positioning & Hiding */
    #mediaGridView .superdesign-hover-overlay {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(15, 23, 42, 0.82) !important;
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        z-index: 20 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #mediaGridView .superdesign-card:hover .superdesign-hover-overlay {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    /* Action Buttons inside Overlay */
    #mediaGridView .sd-action-btn {
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        min-height: 36px !important;
        max-width: 36px !important;
        max-height: 36px !important;
        padding: 0 !important;
        margin: 0 !important;
        border-radius: 50% !important;
        background-color: #ffffff !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
        cursor: pointer !important;
        transition: transform 0.15s ease, background-color 0.15s ease !important;
        float: none !important;
        outline: none !important;
    }

    #mediaGridView .sd-action-btn:hover {
        transform: scale(1.15) !important;
        background-color: #ffffff !important;
    }

    #mediaGridView .superdesign-card-footer {
        height: 75px !important;
        min-height: 75px !important;
        max-height: 75px !important;
        padding: 12px 14px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        background-color: #ffffff !important;
        border-top: 1px solid #f1f5f9 !important;
        margin-top: auto !important;
    }

    .dropzone-area {
        transition: all 0.2s ease;
        border-color: #cbd5e1 !important;
    }
    .dropzone-area.drag-over {
        background-color: rgba(97, 93, 250, 0.08) !important;
        border-color: #615dfa !important;
    }

    .smaller { font-size: 0.78rem; }
    .cursor-pointer { cursor: pointer; }
    .btn-hero-action { border-radius: 10px; font-weight: 600; padding: 0.5rem 1.25rem; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. GRID / LIST VIEW SWITCHER WITH LOCALSTORAGE MEMORY
    const btnGridView = document.getElementById('btnGridView');
    const btnListView = document.getElementById('btnListView');
    const mediaGridView = document.getElementById('mediaGridView');
    const mediaListView = document.getElementById('mediaListView');

    function setViewMode(mode) {
        if (mode === 'list') {
            mediaGridView?.classList.add('d-none');
            mediaListView?.classList.remove('d-none');
            btnListView?.classList.add('active');
            btnGridView?.classList.remove('active');
            localStorage.setItem('myads_media_view_mode', 'list');
        } else {
            mediaListView?.classList.add('d-none');
            mediaGridView?.classList.remove('d-none');
            btnGridView?.classList.add('active');
            btnListView?.classList.remove('active');
            localStorage.setItem('myads_media_view_mode', 'grid');
        }
    }

    const savedViewMode = localStorage.getItem('myads_media_view_mode') || 'grid';
    setViewMode(savedViewMode);

    btnGridView?.addEventListener('click', () => setViewMode('grid'));
    btnListView?.addEventListener('click', () => setViewMode('list'));

    // 2. BULK SELECTION MANAGEMENT
    const fileCheckboxes = document.querySelectorAll('.file-select-item');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const listSelectAll = document.getElementById('listSelectAll');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.file-select-item:checked');
        const count = checked.length;
        if (count > 0) {
            selectedCountSpan.textContent = count;
            bulkActionBar.classList.remove('collapse');
            bulkActionBar.classList.add('show');
        } else {
            bulkActionBar.classList.remove('show');
            bulkActionBar.classList.add('collapse');
        }

        // populate bulk delete hidden form inputs
        if (bulkDeleteInputs) {
            bulkDeleteInputs.innerHTML = '';
            checked.forEach(cb => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'paths[]';
                hidden.value = cb.value;
                bulkDeleteInputs.appendChild(hidden);
            });
        }
    }

    fileCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    function toggleSelectAll(isMasterChecked) {
        fileCheckboxes.forEach(cb => {
            cb.checked = isMasterChecked;
        });
        if (selectAllCheckbox) selectAllCheckbox.checked = isMasterChecked;
        if (listSelectAll) listSelectAll.checked = isMasterChecked;
        updateBulkBar();
    }

    selectAllCheckbox?.addEventListener('change', function() { toggleSelectAll(this.checked); });
    listSelectAll?.addEventListener('change', function() { toggleSelectAll(this.checked); });

    btnDeselectAll?.addEventListener('click', function() {
        toggleSelectAll(false);
    });

    // 3. COPY DIRECT URL TOAST
    const mediaToastEl = document.getElementById('mediaToast');
    const mediaToast = mediaToastEl ? new bootstrap.Toast(mediaToastEl) : null;
    const copyUrlBtns = document.querySelectorAll('.copy-url-btn');

    copyUrlBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (url) {
                navigator.clipboard.writeText(url).then(() => {
                    if (mediaToast) mediaToast.show();
                }).catch(err => {
                    console.error('Failed to copy URL:', err);
                });
            }
        });
    });

    // 4. MULTI-MEDIA PREVIEW MODAL LOGIC
    const previewModalEl = document.getElementById('previewModal');
    const previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;
    const previewBtns = document.querySelectorAll('.preview-btn');

    const previewTitle = document.getElementById('previewTitle');
    const previewImageContainer = document.getElementById('previewImageContainer');
    const previewImage = document.getElementById('previewImage');
    const previewVideoContainer = document.getElementById('previewVideoContainer');
    const previewVideo = document.getElementById('previewVideo');
    const previewAudioContainer = document.getElementById('previewAudioContainer');
    const previewAudio = document.getElementById('previewAudio');
    const previewFallbackContainer = document.getElementById('previewFallbackContainer');

    const metaFileName = document.getElementById('metaFileName');
    const metaFileExt = document.getElementById('metaFileExt');
    const metaFileSize = document.getElementById('metaFileSize');
    const metaFilePath = document.getElementById('metaFilePath');
    const btnPreviewCopyUrl = document.getElementById('btnPreviewCopyUrl');
    const previewDownload = document.getElementById('previewDownload');

    previewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const name = this.getAttribute('data-name');
            const ext = this.getAttribute('data-ext');
            const size = this.getAttribute('data-size');
            const path = this.getAttribute('data-path');

            const isImage = this.getAttribute('data-is-image') === '1';
            const isVideo = this.getAttribute('data-is-video') === '1';
            const isAudio = this.getAttribute('data-is-audio') === '1';

            // Populate Metadata
            previewTitle.textContent = name;
            metaFileName.textContent = name;
            metaFileExt.textContent = ext;
            metaFileSize.textContent = size;
            metaFilePath.textContent = path;
            previewDownload.href = url || '#';

            if (btnPreviewCopyUrl) {
                btnPreviewCopyUrl.onclick = function() {
                    if (url) {
                        navigator.clipboard.writeText(url).then(() => {
                            if (mediaToast) mediaToast.show();
                        });
                    }
                };
            }

            // Hide containers
            previewImageContainer.classList.add('d-none');
            previewVideoContainer.classList.add('d-none');
            previewAudioContainer.classList.add('d-none');
            previewFallbackContainer.classList.add('d-none');

            // Pause media players
            if (previewVideo) previewVideo.pause();
            if (previewAudio) previewAudio.pause();

            if (isImage && url) {
                previewImage.src = url;
                previewImageContainer.classList.remove('d-none');
            } else if (isVideo && url) {
                previewVideo.src = url;
                previewVideoContainer.classList.remove('d-none');
            } else if (isAudio && url) {
                previewAudio.src = url;
                previewAudioContainer.classList.remove('d-none');
            } else {
                previewFallbackContainer.classList.remove('d-none');
            }

            previewModal?.show();
        });
    });

    // Clean up media players on modal hide
    previewModalEl?.addEventListener('hidden.bs.modal', function () {
        if (previewVideo) previewVideo.pause();
        if (previewAudio) previewAudio.pause();
    });

    // 5. RENAME MODAL
    const renameModalEl = document.getElementById('renameModal');
    const renameModal = renameModalEl ? new bootstrap.Modal(renameModalEl) : null;
    const renameBtns = document.querySelectorAll('.rename-btn');
    const renamePathInput = document.getElementById('renamePath');
    const renameNameInput = document.getElementById('renameName');

    renameBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            renamePathInput.value = this.getAttribute('data-path');
            renameNameInput.value = this.getAttribute('data-name');
            renameModal?.show();
        });
    });

    // 6. SINGLE DELETE MODAL
    const singleDeleteModalEl = document.getElementById('singleDeleteModal');
    const singleDeleteModal = singleDeleteModalEl ? new bootstrap.Modal(singleDeleteModalEl) : null;
    const deleteSingleBtns = document.querySelectorAll('.delete-single-btn');
    const deleteSinglePath = document.getElementById('deleteSinglePath');
    const deleteSingleFileName = document.getElementById('deleteSingleFileName');

    deleteSingleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            deleteSinglePath.value = this.getAttribute('data-path');
            deleteSingleFileName.textContent = this.getAttribute('data-name');
            singleDeleteModal?.show();
        });
    });

    // 7. DRAG AND DROP UPLOAD ZONE FEEDBACK
    const dropzoneArea = document.getElementById('dropzoneArea');
    const fileInput = document.getElementById('fileInput');
    const fileSelectedList = document.getElementById('fileSelectedList');
    const fileListNames = document.getElementById('fileListNames');

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneArea?.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzoneArea.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneArea?.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzoneArea.classList.remove('drag-over');
        });
    });

    fileInput?.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileListNames.innerHTML = '';
            Array.from(this.files).forEach(f => {
                const li = document.createElement('li');
                li.className = 'list-group-item bg-white d-flex justify-content-between align-items-center py-2';
                li.innerHTML = `
                    <span class="text-truncate fw-medium me-2"><i class="fa-solid fa-file text-primary me-2"></i>${f.name}</span>
                    <span class="badge bg-light text-dark border">${(f.size / (1024*1024)).toFixed(2)} MB</span>
                `;
                fileListNames.appendChild(li);
            });
            fileSelectedList.classList.remove('d-none');
        } else {
            fileSelectedList.classList.add('d-none');
        }
    });
});
</script>
@endpush
