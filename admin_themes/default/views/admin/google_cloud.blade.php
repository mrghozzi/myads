@extends('admin::layouts.admin')

@section('title', __('messages.google_cloud_settings') ?? 'Google Cloud Storage Configuration')

@section('content')
<div class="admin-page storage-config-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4" style="background: linear-gradient(135deg, #1a73e8 0%, #4285f4 100%);">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}" class="text-white-50">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.media') }}" class="text-white-50">{{ __('messages.media_manager') ?? 'Media Manager' }}</a></li>
                <li class="text-white">Google Cloud</li>
            </ul>
            <div class="admin-hero__eyebrow text-white"><i class="fa-brands fa-google me-1"></i> Google Cloud Platform (GCP)</div>
            <h1 class="admin-hero__title text-white">Google Cloud Storage Config</h1>
            <p class="admin-hero__copy text-white-50">Enterprise object storage for application assets powered by Google Cloud infrastructure.</p>
        </div>
        <div class="admin-hero__actions d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
            @php $gcpActive = ($options['google_cloud_storage']->o_valuer ?? '0') == '1'; @endphp
            @if($gcpActive)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('messages.active_primary_storage') ?? 'Active Driver' }}
                </span>
            @else
                <span class="badge bg-white-glass text-white border border-white-20 px-3 py-2 rounded-pill fs-6">
                    <i class="fa-solid fa-pause me-1"></i> {{ __('messages.inactive_driver') ?? 'Inactive Driver' }}
                </span>
            @endif
        </div>
    </section>

    <!-- Superdesign Storage Sub-Nav -->
    @include('admin::admin.partials.storage_nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-badge bg-danger-soft text-danger">
                            <i class="fa-brands fa-google"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">GCS Bucket & Service Account</h5>
                            <small class="text-muted">Upload service account JSON key file and bucket details</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.google_cloud.update') }}" method="POST" enctype="multipart/form-data" id="gcpForm">
                        @csrf

                        <!-- Enable Switch Card -->
                        <div class="p-3 bg-light rounded-4 border mb-4">
                            <div class="form-check form-switch px-0 d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label mb-0 fw-bold text-dark cursor-pointer" for="google_cloud_storage">
                                        <i class="fa-brands fa-google text-danger me-2"></i>Enable Google Cloud Storage
                                    </label>
                                    <div class="small text-muted mt-1">Store and serve uploaded user assets using Google Cloud Storage buckets.</div>
                                </div>
                                <input class="form-check-input ms-3 cursor-pointer" type="checkbox" role="switch" id="google_cloud_storage" name="google_cloud_storage" value="1" {{ ($options['google_cloud_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 48px; height: 24px;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Bucket Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">GCS Bucket Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-bucket"></i></span>
                                    <input type="text" name="google_cloud_bucket_name" class="form-control border-start-0 ps-0" value="{{ $options['google_cloud_bucket_name']->o_valuer ?? '' }}" placeholder="my-gcp-media-bucket" required>
                                </div>
                                <small class="text-muted smaller">Exact name of your Google Cloud Storage bucket.</small>
                            </div>

                            <!-- Service Account JSON Key Upload -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Service Account JSON Key File</label>
                                <div class="input-group">
                                    <input type="file" name="google_cloud_file" class="form-control" accept=".json">
                                </div>
                                <small class="text-muted smaller">Upload the <code>.json</code> key file generated from GCP IAM Console.</small>
                            </div>

                            <!-- Active Key File Path Display -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Active Key File Location</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-file-code"></i></span>
                                    <input type="text" name="google_cloud_file_path" class="form-control border-start-0 ps-0 bg-light" value="{{ $options['google_cloud_file_path']->o_valuer ?? 'Not Uploaded Yet' }}" readonly>
                                </div>
                                <small class="text-muted smaller">Server path where the active GCP service key is stored.</small>
                            </div>

                            <!-- Custom Endpoint -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Custom CDN / Domain (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-globe"></i></span>
                                    <input type="url" name="google_cloud_custom_endpoint" class="form-control border-start-0 ps-0" value="{{ $options['google_cloud_custom_endpoint']->o_valuer ?? '' }}" placeholder="https://cdn.example.com">
                                </div>
                                <small class="text-muted smaller">Custom domain mapped to your GCS bucket.</small>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-success rounded-3 px-3 fw-semibold" id="gcpTestBtn" onclick="testCloudConnection()">
                                <i class="fa-solid fa-plug-circle-check me-1"></i> {{ __('messages.google_cloud_test_connection') ?? 'Test Connection' }}
                            </button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> {{ __('messages.save_changes') ?? 'Save Settings' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-brands fa-google me-2 text-danger"></i>GCP Storage Guidelines</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 smaller">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Service Account:</strong>
                                <div class="text-muted">Create a service account with <code>Storage Admin</code> or <code>Storage Object Admin</code> role.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Security:</strong>
                                <div class="text-muted">Keep the uploaded JSON key file secure and protected inside your server storage.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Public Access:</strong>
                                <div class="text-muted">Grant <code>allUsers</code> the <code>Storage Object Viewer</code> role for media asset accessibility.</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Navigation to Media Manager -->
            <div class="card border-0 shadow-sm rounded-4 bg-primary-soft p-4 text-center">
                <div class="stat-icon-badge bg-primary text-white mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 16px;">
                    <i class="fa-solid fa-photo-film fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Manage Uploaded Assets</h6>
                <p class="text-muted smaller mb-3">View, filter, rename, preview, and clean up files stored across local and cloud drives.</p>
                <a href="{{ route('admin.media') }}" class="btn btn-primary rounded-3 w-100 fw-bold">
                    <i class="fa-solid fa-folder-open me-2"></i>Open Media Manager
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testCloudConnection() {
    const btn = document.getElementById('gcpTestBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Testing...';
    
    fetch('{{ route("admin.settings.google_cloud.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Google Cloud connection verified successfully!');
        btn.disabled = false;
        btn.innerHTML = originalText;
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endpush
