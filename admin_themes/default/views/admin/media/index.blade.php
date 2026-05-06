@extends('admin::layouts.admin')

@section('title', __('messages.media_manager'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.media_manager') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.media_monitoring') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.media_manager') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.media_monitoring_desc') ?? 'Monitor and manage uploaded media files across your system.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.total_files') ?? 'Total Files' }}</span>
                    <span class="admin-summary-value">{{ count($files) }}</span>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.media') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('messages.search') ?? 'Search files...' }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-select">
                        <option value="">{{ __('messages.all_types') ?? 'All Types' }}</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>{{ __('messages.images') ?? 'Images' }}</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>{{ __('messages.videos') ?? 'Videos' }}</option>
                        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>{{ __('messages.archives') ?? 'Archives' }}</option>
                        <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="php" {{ request('type') == 'php' ? 'selected' : '' }}>PHP</option>
                        <option value="zip" {{ request('type') == 'zip' ? 'selected' : '' }}>ZIP</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0">{{ __('messages.file_name') }}</th>
                            <th class="border-0">{{ __('messages.file_type') }}</th>
                            <th class="border-0">{{ __('messages.file_size') }}</th>
                            <th class="border-0">{{ __('messages.location') }}</th>
                            <th class="border-0">{{ __('messages.date') }}</th>
                            <th class="pe-4 border-0 text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-wrap me-3">
                                            <img src="{{ $file['icon'] }}" alt="{{ $file['extension'] }}" width="32">
                                        </div>
                                        <div>
                                            @if($file['url'])
                                                <a href="{{ $file['url'] }}" target="_blank" class="fw-bold text-dark text-decoration-none file-name-link">{{ $file['name'] }}</a>
                                            @else
                                                <span class="fw-bold text-dark">{{ $file['name'] }}</span>
                                            @endif
                                            <div class="text-muted small text-truncate" style="max-width: 300px;">{{ $file['path'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-secondary text-secondary text-uppercase">{{ $file['extension'] }}</span>
                                </td>
                                <td>{{ $file['size'] }}</td>
                                <td>
                                    @if($file['directory'] == 'upload')
                                        <span class="badge bg-soft-info text-info">Internal /upload</span>
                                    @else
                                        <span class="badge bg-soft-success text-success">Public /upload</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">{{ date('Y-m-d', $file['last_modified']) }}</div>
                                    <div class="text-muted smaller">{{ date('H:i', $file['last_modified']) }}</div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            @if($file['is_image'] && $file['url'])
                                                <li><button class="dropdown-item preview-btn" data-url="{{ $file['url'] }}" data-name="{{ $file['name'] }}"><i class="fa-solid fa-image me-2"></i> {{ __('messages.preview') }}</button></li>
                                            @endif
                                            @if($file['url'])
                                                <li><a class="dropdown-item" href="{{ $file['url'] }}" target="_blank"><i class="fa-solid fa-eye me-2"></i> {{ __('messages.view') ?? 'View' }}</a></li>
                                            @endif
                                            <li><button class="dropdown-item rename-btn" data-path="{{ $file['path'] }}" data-name="{{ $file['name'] }}"><i class="fa-solid fa-pen-to-square me-2"></i> {{ __('messages.rename') ?? 'Rename' }}</button></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.media.delete') }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    @csrf
                                                    <input type="hidden" name="path" value="{{ $file['path'] }}">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash-can me-2"></i> {{ __('messages.delete') }}</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa-solid fa-folder-open fs-1 mb-3 d-block"></i>
                                        {{ __('messages.no_media_found') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .file-icon-wrap {
        width: 48px;
        height: 48px;
        background: #f8f9fa;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    tr:hover .file-icon-wrap {
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .file-name-link:hover {
        color: var(--bs-primary) !important;
    }
    .smaller { font-size: 0.75rem; }
    .bg-soft-info { background-color: rgba(35, 210, 226, 0.1); }
    .bg-soft-success { background-color: rgba(30, 197, 137, 0.1); }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #6c757d; }
    .btn-icon:hover { background: #f8f9fa; color: #333; }
    .preview-image-container { max-height: 70vh; overflow: auto; display: flex; justify-content: center; background: #f8f9fa; border-radius: 8px; }
    .preview-image-container img { max-width: 100%; height: auto; object-fit: contain; }
</style>
@endsection

@section('modals')
<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('messages.rename') ?? 'Rename' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.media.rename') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <input type="hidden" name="path" id="renamePath">
                    <div class="mb-3">
                        <label for="new_name" class="form-label fw-semibold">{{ __('messages.new_name') ?? 'New Name' }}</label>
                        <input type="text" class="form-control" name="new_name" id="renameName" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="previewTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-0">
                <div class="preview-image-container">
                    <img src="" id="previewImage" alt="Preview">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <a href="" id="previewDownload" class="btn btn-primary" target="_blank" download><i class="fa-solid fa-download me-2"></i> {{ __('messages.download') ?? 'Download' }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const renameModalEl = document.getElementById('renameModal');
    const renameModal = new bootstrap.Modal(renameModalEl);
    const renameBtns = document.querySelectorAll('.rename-btn');
    const renamePathInput = document.getElementById('renamePath');
    const renameNameInput = document.getElementById('renameName');

    renameBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const path = this.getAttribute('data-path');
            const name = this.getAttribute('data-name');
            renamePathInput.value = path;
            renameNameInput.value = name;
            renameModal.show();
        });
    });

    // Preview Logic
    const previewModalEl = document.getElementById('previewModal');
    const previewModal = new bootstrap.Modal(previewModalEl);
    const previewBtns = document.querySelectorAll('.preview-btn');
    const previewImage = document.getElementById('previewImage');
    const previewTitle = document.getElementById('previewTitle');
    const previewDownload = document.getElementById('previewDownload');

    previewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const name = this.getAttribute('data-name');
            previewImage.src = url;
            previewTitle.textContent = name;
            previewDownload.href = url;
            previewModal.show();
        });
    });
});
</script>
@endpush
