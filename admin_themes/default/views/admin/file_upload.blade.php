@extends('admin::layouts.admin')

@section('title', __('messages.file_upload_config'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.file_upload_config') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_module_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.file_upload_config') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.upload_sharing_config') }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__body">
            <form action="{{ route('admin.settings.upload.update') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-12">
                    <h5 class="fw-bold mb-3">{{ __('messages.file_upload_sharing') }}</h5>
                    <div class="admin-utility-card mb-3">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="file_sharing" name="file_sharing" value="1" {{ ($options['file_sharing']->o_valuer ?? '1') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="file_sharing">{{ __('messages.file_upload_sharing') }}</label>
                                <div class="small text-muted">{{ __('messages.file_upload_sharing_help') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-utility-card mb-3">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="video_sharing" name="video_sharing" value="1" {{ ($options['video_sharing']->o_valuer ?? '1') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="video_sharing">{{ __('messages.video_upload_sharing') }}</label>
                                <div class="small text-muted">{{ __('messages.video_upload_sharing_help') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-utility-card mb-3">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="clips_upload" name="clips_upload" value="1" {{ ($options['clips_upload']->o_valuer ?? '1') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="clips_upload">{{ __('messages.clips_upload') }}</label>
                                <div class="small text-muted">{{ __('messages.clips_upload_help') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-utility-card mb-3">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="audio_sharing" name="audio_sharing" value="1" {{ ($options['audio_sharing']->o_valuer ?? '1') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="audio_sharing">{{ __('messages.audio_upload_sharing') }}</label>
                                <div class="small text-muted">{{ __('messages.audio_upload_sharing_help') }}</div>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>

                <div class="col-12">
                    <hr>
                    <h5 class="fw-bold mb-3">{{ __('messages.upload_file_limits') }}</h5>
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ __('messages.upload_security_warning') }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.allowed_extensions') }}</label>
                    <input type="text" name="allowed_extensions" class="form-control" value="{{ $options['allowed_extensions']->o_valuer ?? 'jpg,png,jpeg,gif,mp4,mp3,pdf,zip' }}" placeholder="jpg,png,gif...">
                    <small class="text-muted">Comma-separated list of allowed file extensions.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.allowed_mime_types') }}</label>
                    <input type="text" name="allowed_mime_types" class="form-control" value="{{ $options['allowed_mime_types']->o_valuer ?? 'image/jpeg,image/png,image/gif,video/mp4,audio/mpeg,application/pdf,application/zip' }}" placeholder="image/jpeg,image/png...">
                    <small class="text-muted">Comma-separated list of allowed MIME types.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.max_upload_size') }}</label>
                    <div class="input-group">
                        <input type="number" name="max_upload_size" class="form-control" value="{{ $options['max_upload_size']->o_valuer ?? '10' }}">
                        <span class="input-group-text">MB</span>
                    </div>
                    <small class="text-muted">Maximum file size per upload in Megabytes.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.image_compression_level') }}</label>
                    <select name="image_compression" class="form-select">
                        <option value="low" {{ ($options['image_compression']->o_valuer ?? 'medium') == 'low' ? 'selected' : '' }}>{{ __('messages.low') }} (90%)</option>
                        <option value="medium" {{ ($options['image_compression']->o_valuer ?? 'medium') == 'medium' ? 'selected' : '' }}>{{ __('messages.medium') }} (70%)</option>
                        <option value="high" {{ ($options['image_compression']->o_valuer ?? 'medium') == 'high' ? 'selected' : '' }}>{{ __('messages.high') }} (50%)</option>
                    </select>
                    <small class="text-muted">Higher compression means smaller files but lower quality.</small>
                </div>

                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
