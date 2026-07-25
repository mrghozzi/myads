@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="storage-superdesign-nav mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2 bg-white rounded-4 border shadow-xs">
        <div class="d-flex flex-wrap align-items-center gap-1">
            <!-- Media Manager -->
            <a href="{{ route('admin.media') }}" class="storage-nav-pill {{ $currentRoute === 'admin.media' ? 'active' : '' }}">
                <i class="fa-solid fa-photo-film text-purple me-2"></i>
                <span>{{ __('messages.media_manager') ?? 'Media Manager' }}</span>
            </a>

            <!-- Upload Settings -->
            <a href="{{ route('admin.settings.upload') }}" class="storage-nav-pill {{ $currentRoute === 'admin.settings.upload' ? 'active' : '' }}">
                <i class="fa-solid fa-sliders text-primary me-2"></i>
                <span>{{ __('messages.file_upload_settings') ?? 'Upload Settings' }}</span>
            </a>

            <!-- Amazon S3 -->
            <a href="{{ route('admin.settings.amazon_s3') }}" class="storage-nav-pill {{ $currentRoute === 'admin.settings.amazon_s3' ? 'active' : '' }}">
                <i class="fa-brands fa-aws text-amber me-2"></i>
                <span>Amazon S3</span>
            </a>

            <!-- DigitalOcean Spaces -->
            <a href="{{ route('admin.settings.digitalocean') }}" class="storage-nav-pill {{ $currentRoute === 'admin.settings.digitalocean' ? 'active' : '' }}">
                <i class="fa-brands fa-digital-ocean text-cyan me-2"></i>
                <span>DigitalOcean</span>
            </a>

            <!-- Google Cloud -->
            <a href="{{ route('admin.settings.google_cloud') }}" class="storage-nav-pill {{ $currentRoute === 'admin.settings.google_cloud' ? 'active' : '' }}">
                <i class="fa-brands fa-google text-danger me-2"></i>
                <span>Google Cloud</span>
            </a>

            <!-- FTP Server -->
            <a href="{{ route('admin.settings.ftp') }}" class="storage-nav-pill {{ $currentRoute === 'admin.settings.ftp' ? 'active' : '' }}">
                <i class="fa-solid fa-network-wired text-success me-2"></i>
                <span>FTP Server</span>
            </a>
        </div>

        <div class="d-none d-md-flex align-items-center gap-2 pe-2">
            <span class="badge bg-light text-secondary border rounded-pill smaller px-3 py-2">
                <i class="fa-solid fa-hard-drive me-1 text-primary"></i> Multi-Drive Cloud Ready
            </span>
        </div>
    </div>
</div>

<style>
    .storage-superdesign-nav .storage-nav-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
    }
    .storage-superdesign-nav .storage-nav-pill:hover {
        color: #1e293b;
        background: #f8fafc;
    }
    .storage-superdesign-nav .storage-nav-pill.active {
        color: #615dfa !important;
        background: rgba(97, 93, 250, 0.08) !important;
        box-shadow: 0 2px 6px rgba(97, 93, 250, 0.12);
    }
    .text-purple { color: #8b5cf6 !important; }
    .text-cyan { color: #0284c7 !important; }
</style>
