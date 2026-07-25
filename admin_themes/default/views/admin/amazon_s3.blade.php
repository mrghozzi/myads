@extends('admin::layouts.admin')

@section('title', __('messages.amazon_s3_config') ?? 'Amazon S3 Configuration')

@section('content')
<div class="admin-page storage-config-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}" class="text-white-50">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.media') }}" class="text-white-50">{{ __('messages.media_manager') ?? 'Media Manager' }}</a></li>
                <li class="text-white">Amazon S3</li>
            </ul>
            <div class="admin-hero__eyebrow text-amber"><i class="fa-brands fa-aws me-1"></i> Amazon Web Services (AWS)</div>
            <h1 class="admin-hero__title text-white">Amazon S3 Storage Config</h1>
            <p class="admin-hero__copy text-white-50">Offload your system uploads and media assets directly to Amazon Simple Storage Service (S3).</p>
        </div>
        <div class="admin-hero__actions d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
            @php $s3Active = ($options['amazon_s3_storage']->o_valuer ?? '0') == '1'; @endphp
            @if($s3Active)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('messages.active_primary_storage') ?? 'Active Driver' }}
                </span>
            @else
                <span class="badge bg-secondary-subtle text-white-50 border border-secondary-subtle px-3 py-2 rounded-pill fs-6">
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
                        <div class="stat-icon-badge bg-amber-soft text-amber">
                            <i class="fa-brands fa-aws"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">S3 Bucket & Credentials</h5>
                            <small class="text-muted">Configure AWS access keys and bucket region</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.amazon_s3.update') }}" method="POST" id="s3Form">
                        @csrf

                        <!-- Enable Switch Card -->
                        <div class="p-3 bg-light rounded-4 border mb-4">
                            <div class="form-check form-switch px-0 d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label mb-0 fw-bold text-dark cursor-pointer" for="amazon_s3_storage">
                                        <i class="fa-solid fa-cloud-arrow-up text-amber me-2"></i>Enable Amazon S3 Cloud Storage
                                    </label>
                                    <div class="small text-muted mt-1">When enabled, new media uploads will be dispatched to your AWS S3 bucket.</div>
                                </div>
                                <input class="form-check-input ms-3 cursor-pointer" type="checkbox" role="switch" id="amazon_s3_storage" name="amazon_s3_storage" value="1" {{ ($options['amazon_s3_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 48px; height: 24px;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Bucket Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Bucket Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-bucket"></i></span>
                                    <input type="text" name="amazon_bucket_name" class="form-control border-start-0 ps-0" value="{{ $options['amazon_bucket_name']->o_valuer ?? '' }}" placeholder="my-app-media-bucket" required>
                                </div>
                                <small class="text-muted smaller">Exact name of your AWS S3 storage bucket.</small>
                            </div>

                            <!-- Region -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Bucket Region <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-globe"></i></span>
                                    <input type="text" name="amazon_s3_bucket_region" class="form-control border-start-0 ps-0" value="{{ $options['amazon_s3_bucket_region']->o_valuer ?? 'us-east-1' }}" placeholder="us-east-1" required>
                                </div>
                                <small class="text-muted smaller">e.g. us-east-1, eu-central-1, ap-southeast-1</small>
                            </div>

                            <!-- Access Key ID -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">AWS Access Key ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-key"></i></span>
                                    <input type="text" name="amazon_s3_key" class="form-control border-start-0 ps-0" value="{{ $options['amazon_s3_key']->o_valuer ?? '' }}" placeholder="AKIAIOSFODNN7EXAMPLE">
                                </div>
                                <small class="text-muted smaller">IAM user access key with S3 Read/Write permissions.</small>
                            </div>

                            <!-- Secret Access Key -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">AWS Secret Access Key <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" id="amazon_s3_secret_key" name="amazon_s3_secret_key" class="form-control border-start-0 border-end-0 ps-0" value="{{ $options['amazon_s3_secret_key']->o_valuer ?? '' }}" placeholder="••••••••••••••••••••••••">
                                    <button type="button" class="btn btn-outline-light text-muted bg-white border" onclick="togglePasswordVisibility('amazon_s3_secret_key', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted smaller">Secret access key matching the IAM Access Key ID.</small>
                            </div>

                            <!-- Custom Endpoint (Optional) -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Custom CDN / S3 Endpoint (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-link"></i></span>
                                    <input type="url" name="amazon_s3_custom_endpoint" class="form-control border-start-0 ps-0" value="{{ $options['amazon_s3_custom_endpoint']->o_valuer ?? '' }}" placeholder="https://cdn.yourdomain.com or https://s3.us-east-1.amazonaws.com">
                                </div>
                                <small class="text-muted smaller">Leave blank to use default AWS endpoint URL.</small>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-success rounded-3 px-3 fw-semibold" id="testBtn" onclick="testConnection()">
                                    <i class="fa-solid fa-plug-circle-check me-1"></i> {{ __('messages.test_verify_connection') ?? 'Test Connection' }}
                                </button>
                                <button type="button" class="btn btn-outline-info text-dark rounded-3 px-3 fw-semibold" id="uploadSyncBtn" onclick="uploadFiles()">
                                    <i class="fa-solid fa-cloud-arrow-up me-1 text-info"></i> {{ __('messages.upload_files_to_amazon') ?? 'Sync Local Media to S3' }}
                                </button>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> {{ __('messages.save_changes') ?? 'Save Settings' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Diagnostics & Quick Info -->
        <div class="col-lg-4">
            <!-- AWS S3 Setup Checklist -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check me-2 text-amber"></i>AWS S3 Checklist</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 smaller">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Create Bucket:</strong>
                                <div class="text-muted">Set ACLs to public-read or configure a bucket policy for media serving.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">CORS Policy:</strong>
                                <div class="text-muted">Allow <code>GET</code>, <code>PUT</code>, <code>POST</code> requests from your website domain.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">IAM Permissions:</strong>
                                <div class="text-muted">Grant <code>s3:PutObject</code>, <code>s3:GetObject</code>, and <code>s3:DeleteObject</code>.</div>
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
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}

function testConnection() {
    const btn = document.getElementById('testBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...';
    
    fetch('{{ route("admin.settings.amazon_s3.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'AWS S3 Connection verified successfully!');
        btn.disabled = false;
        btn.innerHTML = originalText;
    })
    .catch(error => {
        alert('Connection test failed: ' + error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function uploadFiles() {
    if(!confirm('Are you sure you want to start syncing local media files to Amazon S3? This process will run in the background.')) return;
    
    const btn = document.getElementById('uploadSyncBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Syncing...';
    
    fetch('{{ route("admin.settings.amazon_s3.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Media sync to S3 initiated!');
        btn.disabled = false;
        btn.innerHTML = originalText;
    })
    .catch(error => {
        alert('Sync error: ' + error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endpush
