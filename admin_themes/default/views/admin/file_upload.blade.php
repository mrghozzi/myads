@extends('admin::layouts.admin')

@section('title', __('messages.file_upload_config') ?? 'File Upload Configuration')

@section('content')
<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.file_upload_config') ?? 'File Upload Configuration' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1"><i class="feather-sliders me-1"></i>{{ __('messages.admin_module_settings') ?? 'System Settings' }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-upload-cloud text-primary"></i>
                {{ __('messages.file_upload_config') ?? 'File Upload Configuration' }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.upload_sharing_config') ?? 'Configure media upload rules, security whitelists, storage limits, and image optimization engines.' }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-alert-triangle fs-5"></i>
                <div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Server Diagnostics Banner -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-dark text-white rounded-3 overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                        <i class="feather-server fs-5"></i>
                    </span>
                    <div>
                        <h5 class="card-title text-white mb-0 fw-bold">{{ __('messages.server_diagnostics') ?? 'Server Environment Diagnostics' }}</h5>
                        <small class="text-muted">{{ __('messages.server_diagnostics_help') ?? 'Actual server directives affecting upload capabilities' }}</small>
                    </div>
                </div>
                <span class="badge bg-dark border border-secondary text-light px-3 py-2">
                    <i class="feather-clock me-1 text-warning"></i> Max Execution: {{ $serverInfo['max_execution_time'] ?? '30s' }}
                </span>
            </div>

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-dark-subtle border border-secondary border-opacity-25 h-100">
                        <span class="text-muted small d-block mb-1">{{ __('messages.server_upload_limit') ?? 'PHP Upload Max Size' }}</span>
                        <div class="fs-4 fw-bold text-info d-flex align-items-center gap-1">
                            <i class="feather-arrow-up-circle fs-6"></i>
                            <span id="php-upload-limit">{{ $serverInfo['upload_max_filesize'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-dark-subtle border border-secondary border-opacity-25 h-100">
                        <span class="text-muted small d-block mb-1">{{ __('messages.server_post_limit') ?? 'PHP Post Max Size' }}</span>
                        <div class="fs-4 fw-bold text-primary d-flex align-items-center gap-1">
                            <i class="feather-package fs-6"></i>
                            <span>{{ $serverInfo['post_max_size'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-dark-subtle border border-secondary border-opacity-25 h-100">
                        <span class="text-muted small d-block mb-1">Image Processors</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge {{ ($serverInfo['gd_installed'] ?? false) ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                GD: {{ ($serverInfo['gd_installed'] ?? false) ? 'ON' : 'OFF' }}
                            </span>
                            <span class="badge {{ ($serverInfo['imagick_installed'] ?? false) ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                Imagick: {{ ($serverInfo['imagick_installed'] ?? false) ? 'ON' : 'OFF' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-dark-subtle border border-secondary border-opacity-25 h-100">
                        <span class="text-muted small d-block mb-1">{{ __('messages.free_disk_space') ?? 'Free Storage Space' }}</span>
                        <div class="fs-4 fw-bold text-success d-flex align-items-center gap-1">
                            <i class="feather-hard-drive fs-6"></i>
                            <span>{{ $serverInfo['disk_free_space'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.upload.update') }}" method="POST" id="upload-settings-form">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Module Sharing & Optimization -->
            <div class="col-lg-6">
                <!-- Section 1: Media Sharing Services -->
                <div class="card border-0 shadow-sm mb-4 h-100">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 text-primary">
                            <i class="feather-share-2"></i>
                            {{ __('messages.file_upload_sharing') ?? 'Media Sharing Services' }}
                        </h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex flex-column gap-3">
                            <!-- File Sharing Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-primary-subtle text-primary fs-4">
                                        <i class="feather-file-text"></i>
                                    </div>
                                    <div>
                                        <label for="file_sharing" class="fw-bold mb-0 cursor-pointer">{{ __('messages.file_upload_sharing') }}</label>
                                        <div class="small text-muted">{{ __('messages.file_upload_sharing_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="file_sharing" name="file_sharing" value="1" {{ ($options['file_sharing']->o_valuer ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Video Sharing Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-danger-subtle text-danger fs-4">
                                        <i class="feather-video"></i>
                                    </div>
                                    <div>
                                        <label for="video_sharing" class="fw-bold mb-0 cursor-pointer">{{ __('messages.video_upload_sharing') }}</label>
                                        <div class="small text-muted">{{ __('messages.video_upload_sharing_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="video_sharing" name="video_sharing" value="1" {{ ($options['video_sharing']->o_valuer ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Clips Upload Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-info-subtle text-info fs-4">
                                        <i class="feather-film"></i>
                                    </div>
                                    <div>
                                        <label for="clips_upload" class="fw-bold mb-0 cursor-pointer">{{ __('messages.clips_upload') }}</label>
                                        <div class="small text-muted">{{ __('messages.clips_upload_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="clips_upload" name="clips_upload" value="1" {{ ($options['clips_upload']->o_valuer ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Audio Sharing Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-warning-subtle text-warning fs-4">
                                        <i class="feather-music"></i>
                                    </div>
                                    <div>
                                        <label for="audio_sharing" class="fw-bold mb-0 cursor-pointer">{{ __('messages.audio_upload_sharing') }}</label>
                                        <div class="small text-muted">{{ __('messages.audio_upload_sharing_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="audio_sharing" name="audio_sharing" value="1" {{ ($options['audio_sharing']->o_valuer ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Performance & Security Shield -->
            <div class="col-lg-6">
                <!-- Section 2: Performance & Security Shield -->
                <div class="card border-0 shadow-sm mb-4 h-100">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 text-primary">
                            <i class="feather-shield"></i>
                            {{ __('messages.performance_security') ?? 'Performance & Security Optimization' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            <!-- Image Compression Select -->
                            <div class="p-3 rounded border bg-body-tertiary">
                                <label class="form-label fw-bold d-flex align-items-center justify-content-between mb-1">
                                    <span><i class="feather-sliders me-1 text-primary"></i> {{ __('messages.image_compression_level') }}</span>
                                    <span class="badge bg-primary-subtle text-primary">Quality vs File Size</span>
                                </label>
                                <select name="image_compression" class="form-select border-primary-subtle">
                                    <option value="low" {{ ($options['image_compression']->o_valuer ?? 'medium') == 'low' ? 'selected' : '' }}>{{ __('messages.low') }} (90% Quality)</option>
                                    <option value="medium" {{ ($options['image_compression']->o_valuer ?? 'medium') == 'medium' ? 'selected' : '' }}>{{ __('messages.medium') }} (70% Quality - Recommended)</option>
                                    <option value="high" {{ ($options['image_compression']->o_valuer ?? 'medium') == 'high' ? 'selected' : '' }}>{{ __('messages.high') }} (50% Quality - Smallest Files)</option>
                                </select>
                                <div class="small text-muted mt-1">Controls lossy compression ratio for user uploaded JPEG/PNG images.</div>
                            </div>

                            <!-- Auto Convert WebP Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-success-subtle text-success fs-4">
                                        <i class="feather-zap"></i>
                                    </div>
                                    <div>
                                        <label for="auto_convert_webp" class="fw-bold mb-0 cursor-pointer">{{ __('messages.auto_convert_webp') ?? 'Auto Convert Images to WebP' }}</label>
                                        <div class="small text-muted">{{ __('messages.auto_convert_webp_help') ?? 'Compresses images into WebP format to save 30-50% bandwidth.' }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="auto_convert_webp" name="auto_convert_webp" value="1" {{ ($options['auto_convert_webp']->o_valuer ?? '0') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Sanitize Filenames Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-info-subtle text-info fs-4">
                                        <i class="feather-type"></i>
                                    </div>
                                    <div>
                                        <label for="sanitize_filenames" class="fw-bold mb-0 cursor-pointer">{{ __('messages.sanitize_filenames') ?? 'Sanitize Uploaded Filenames' }}</label>
                                        <div class="small text-muted">{{ __('messages.sanitize_filenames_help') ?? 'Removes special characters and spaces from file names.' }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="sanitize_filenames" name="sanitize_filenames" value="1" {{ ($options['sanitize_filenames']->o_valuer ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Block Dangerous Extensions Shield Toggle -->
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-danger-subtle text-danger fs-4">
                                        <i class="feather-lock"></i>
                                    </div>
                                    <div>
                                        <label for="block_dangerous_extensions" class="fw-bold mb-0 cursor-pointer">{{ __('messages.block_dangerous_extensions') ?? 'Dangerous Extensions Security Shield' }}</label>
                                        <div class="small text-muted">{{ __('messages.block_dangerous_extensions_help') ?? 'Blocks executable & web script files (php, exe, sh, bat) strictly.' }}</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="block_dangerous_extensions" name="block_dangerous_extensions" value="1" {{ ($options['block_dangerous_extensions']->o_valuer ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Width: File Limits & Security Constraints -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 text-primary">
                            <i class="feather-hard-drive"></i>
                            {{ __('messages.upload_file_limits') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
                            <i class="feather-alert-triangle fs-3 text-warning"></i>
                            <div>
                                <strong>{{ __('messages.note') ?? 'Security Note' }}:</strong> {{ __('messages.upload_security_warning') }}
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Allowed Extensions Input & Live Tags -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                    <span><i class="feather-check-square text-success me-1"></i> {{ __('messages.allowed_extensions') }}</span>
                                    <small class="text-muted">Comma-separated</small>
                                </label>
                                <input type="text" name="allowed_extensions" id="allowed_extensions_input" class="form-control" value="{{ $options['allowed_extensions']->o_valuer ?? 'jpg,png,jpeg,gif,mp4,mp3,pdf,zip' }}" placeholder="jpg,png,gif,mp4...">
                                <div class="mt-2" id="extensions-preview-tags">
                                    <!-- Dynamic badge tags generated via JS -->
                                </div>
                            </div>

                            <!-- Allowed MIME Types -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                    <span><i class="feather-list text-info me-1"></i> {{ __('messages.allowed_mime_types') }}</span>
                                    <small class="text-muted">Comma-separated</small>
                                </label>
                                <input type="text" name="allowed_mime_types" class="form-control" value="{{ $options['allowed_mime_types']->o_valuer ?? 'image/jpeg,image/png,image/gif,video/mp4,audio/mpeg,application/pdf,application/zip' }}" placeholder="image/jpeg,image/png,video/mp4...">
                                <small class="text-muted d-block mt-1">Exact MIME types validated during HTTP request inspection.</small>
                            </div>

                            <!-- Max Upload Size -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('messages.max_upload_size') }}</label>
                                <div class="input-group">
                                    <input type="number" name="max_upload_size" id="max_upload_size_input" class="form-control" value="{{ $options['max_upload_size']->o_valuer ?? '10' }}" min="1" max="1024">
                                    <span class="input-group-text fw-bold">MB</span>
                                </div>
                                <div class="small text-muted mt-1" id="max-upload-size-notice">Max single file upload size in Megabytes.</div>
                            </div>

                            <!-- Max Avatar & Cover Size -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('messages.max_avatar_size') ?? 'Max Avatar & Cover Size' }}</label>
                                <div class="input-group">
                                    <input type="number" name="max_avatar_size" class="form-control" value="{{ $options['max_avatar_size']->o_valuer ?? '2' }}" min="1" max="50">
                                    <span class="input-group-text fw-bold">MB</span>
                                </div>
                                <div class="small text-muted mt-1">{{ __('messages.max_avatar_size_help') ?? 'Maximum file size for user avatars & profile cover photos.' }}</div>
                            </div>

                            <!-- Max Attachments Per Post -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('messages.max_files_per_post') ?? 'Max Attachments Per Post' }}</label>
                                <div class="input-group">
                                    <input type="number" name="max_files_per_post" class="form-control" value="{{ $options['max_files_per_post']->o_valuer ?? '10' }}" min="1" max="50">
                                    <span class="input-group-text fw-bold">Items</span>
                                </div>
                                <div class="small text-muted mt-1">{{ __('messages.max_files_per_post_help') ?? 'Maximum number of files/images attached per post.' }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions Footer -->
                    <div class="card-footer bg-transparent border-top py-3 d-flex align-items-center justify-content-between">
                        <div class="text-muted small">
                            <i class="feather-info me-1 text-primary"></i> Changes apply immediately across Web & Mobile API.
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm">
                            <i class="feather-save"></i>
                            <span>{{ __('messages.save_changes') ?? 'Save Changes' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic Allowed Extensions Pills Previewer
    const extInput = document.getElementById('allowed_extensions_input');
    const tagsContainer = document.getElementById('extensions-preview-tags');

    function updateExtensionTags() {
        if (!extInput || !tagsContainer) return;
        const val = extInput.value.trim();
        if (!val) {
            tagsContainer.innerHTML = '<span class="text-muted small">No extensions specified.</span>';
            return;
        }

        const extArray = val.split(',').map(e => e.trim().toLowerCase()).filter(e => e.length > 0);
        tagsContainer.innerHTML = '';

        extArray.forEach(ext => {
            let badgeClass = 'bg-secondary';
            if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(ext)) {
                badgeClass = 'bg-success-subtle text-success border border-success-subtle';
            } else if (['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(ext)) {
                badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
            } else if (['mp3', 'wav', 'ogg', 'aac', 'flac'].includes(ext)) {
                badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
            } else if (['pdf', 'doc', 'docx', 'zip', 'rar', 'txt'].includes(ext)) {
                badgeClass = 'bg-info-subtle text-info border border-info-subtle';
            }

            const badge = document.createElement('span');
            badge.className = `badge me-1 mb-1 px-2 py-1 ${badgeClass}`;
            badge.textContent = `.${ext}`;
            tagsContainer.appendChild(badge);
        });
    }

    if (extInput) {
        extInput.addEventListener('input', updateExtensionTags);
        updateExtensionTags();
    }

    // PHP Upload Limit Warning Check
    const sizeInput = document.getElementById('max_upload_size_input');
    const phpLimitSpan = document.getElementById('php-upload-limit');
    const noticeDiv = document.getElementById('max-upload-size-notice');

    if (sizeInput && phpLimitSpan && noticeDiv) {
        function checkPhpLimit() {
            const phpLimitText = phpLimitSpan.textContent.trim();
            const phpLimitMb = parseInt(phpLimitText, 10);
            const valMb = parseInt(sizeInput.value, 10);

            if (!isNaN(phpLimitMb) && !isNaN(valMb) && valMb > phpLimitMb) {
                noticeDiv.innerHTML = `<span class="text-danger fw-bold"><i class="feather-alert-circle me-1"></i> Warning: Your setting (${valMb}MB) exceeds server limit (${phpLimitText}). Uploads will be capped at ${phpLimitText}.</span>`;
            } else {
                noticeDiv.textContent = 'Max single file upload size in Megabytes.';
            }
        }

        sizeInput.addEventListener('input', checkPhpLimit);
        checkPhpLimit();
    }
});
</script>
@endpush
@endsection
