@extends('admin::layouts.admin')

@section('title', __('messages.digitalocean_spaces_config') ?? 'DigitalOcean Spaces Configuration')

@section('content')
<div class="admin-page storage-config-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4" style="background: linear-gradient(135deg, #0052cc 0%, #0080ff 100%);">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}" class="text-white-50">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.media') }}" class="text-white-50">{{ __('messages.media_manager') ?? 'Media Manager' }}</a></li>
                <li class="text-white">DigitalOcean Spaces</li>
            </ul>
            <div class="admin-hero__eyebrow text-white"><i class="fa-brands fa-digital-ocean me-1"></i> DigitalOcean Cloud Storage</div>
            <h1 class="admin-hero__title text-white">DigitalOcean Spaces Config</h1>
            <p class="admin-hero__copy text-white-50">S3-compatible object storage with built-in CDN for blazing fast global file delivery.</p>
        </div>
        <div class="admin-hero__actions d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
            @php $doActive = ($options['digitalocean_spaces_storage']->o_valuer ?? '0') == '1'; @endphp
            @if($doActive)
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
                        <div class="stat-icon-badge bg-cyan-soft text-cyan">
                            <i class="fa-brands fa-digital-ocean"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Space Credentials & Region</h5>
                            <small class="text-muted">Configure DigitalOcean Spaces S3-compatible credentials</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.digitalocean.update') }}" method="POST" id="doForm">
                        @csrf

                        <!-- Enable Switch Card -->
                        <div class="p-3 bg-light rounded-4 border mb-4">
                            <div class="form-check form-switch px-0 d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label mb-0 fw-bold text-dark cursor-pointer" for="digitalocean_spaces_storage">
                                        <i class="fa-brands fa-digital-ocean text-primary me-2"></i>Enable DigitalOcean Spaces Storage
                                    </label>
                                    <div class="small text-muted mt-1">Store and serve uploaded user assets via DigitalOcean Spaces CDN.</div>
                                </div>
                                <input class="form-check-input ms-3 cursor-pointer" type="checkbox" role="switch" id="digitalocean_spaces_storage" name="digitalocean_spaces_storage" value="1" {{ ($options['digitalocean_spaces_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 48px; height: 24px;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Space Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Space Name / Bucket <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-box-archive"></i></span>
                                    <input type="text" name="digitalocean_space_name" class="form-control border-start-0 ps-0" value="{{ $options['digitalocean_space_name']->o_valuer ?? '' }}" placeholder="my-space-name" required>
                                </div>
                                <small class="text-muted smaller">Your DigitalOcean Space name.</small>
                            </div>

                            <!-- Region -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Datacenter Region <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-location-dot"></i></span>
                                    <select name="digitalocean_bucket_region" class="form-select border-start-0 ps-0">
                                        <option value="nyc1" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'nyc1' ? 'selected' : '' }}>New York [NYC1]</option>
                                        <option value="nyc3" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'nyc3' ? 'selected' : '' }}>New York [NYC3]</option>
                                        <option value="ams3" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'ams3' ? 'selected' : '' }}>Amsterdam [AMS3]</option>
                                        <option value="sfo2" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'sfo2' ? 'selected' : '' }}>San Francisco [SFO2]</option>
                                        <option value="sfo3" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'sfo3' ? 'selected' : '' }}>San Francisco [SFO3]</option>
                                        <option value="sgp1" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'sgp1' ? 'selected' : '' }}>Singapore [SGP1]</option>
                                        <option value="lon1" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'lon1' ? 'selected' : '' }}>London [LON1]</option>
                                        <option value="fra1" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'fra1' ? 'selected' : '' }}>Frankfurt [FRA1]</option>
                                        <option value="blr1" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'blr1' ? 'selected' : '' }}>Bangalore [BLR1]</option>
                                        <option value="syd1" {{ ($options['digitalocean_bucket_region']->o_valuer ?? 'nyc3') == 'syd1' ? 'selected' : '' }}>Sydney [SYD1]</option>
                                    </select>
                                </div>
                                <small class="text-muted smaller">Select the region where your Space was created.</small>
                            </div>

                            <!-- Key -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Space Key <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-key"></i></span>
                                    <input type="text" name="digitalocean_key" class="form-control border-start-0 ps-0" value="{{ $options['digitalocean_key']->o_valuer ?? '' }}" placeholder="DO00EXAMPLEKEY">
                                </div>
                                <small class="text-muted smaller">Generated API Space key.</small>
                            </div>

                            <!-- Secret Key -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Space Secret <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" id="digitalocean_secret" name="digitalocean_secret" class="form-control border-start-0 border-end-0 ps-0" value="{{ $options['digitalocean_secret']->o_valuer ?? '' }}" placeholder="••••••••••••••••••••••••">
                                    <button type="button" class="btn btn-outline-light text-muted bg-white border" onclick="togglePasswordVisibility('digitalocean_secret', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted smaller">Secret access key matching the Space Key.</small>
                            </div>

                            <!-- Custom Endpoint -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Custom CDN Domain (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-globe"></i></span>
                                    <input type="url" name="digitalocean_custom_endpoint" class="form-control border-start-0 ps-0" value="{{ $options['digitalocean_custom_endpoint']->o_valuer ?? '' }}" placeholder="https://cdn.example.com or https://myspace.nyc3.cdn.digitaloceanspaces.com">
                                </div>
                                <small class="text-muted smaller">Leave blank to use default DigitalOcean Spaces URL endpoint.</small>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-success rounded-3 px-3 fw-semibold" id="doTestBtn" onclick="testDoConnection()">
                                    <i class="fa-solid fa-plug-circle-check me-1"></i> {{ __('messages.test_verify_connection') ?? 'Test Connection' }}
                                </button>
                                <button type="button" class="btn btn-outline-info text-dark rounded-3 px-3 fw-semibold" id="doUploadSyncBtn" onclick="uploadDoFiles()">
                                    <i class="fa-solid fa-cloud-arrow-up me-1 text-info"></i> {{ __('messages.upload_files_to_digitalocean') ?? 'Sync Media to DigitalOcean' }}
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

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-brands fa-digital-ocean me-2 text-primary"></i>DigitalOcean Spaces Tips</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 smaller">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">S3 Compatibility:</strong>
                                <div class="text-muted">DigitalOcean Spaces uses standard S3 API syntax and endpoints.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Built-in CDN:</strong>
                                <div class="text-muted">Enable CDN on your Space control panel for automatic SSL and caching.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Folder Structure:</strong>
                                <div class="text-muted">Ensure existing <code>upload/</code> files are mirrored to your Space root directory.</div>
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

function testDoConnection() {
    const btn = document.getElementById('doTestBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Testing...';
    
    fetch('{{ route("admin.settings.digitalocean.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'DigitalOcean Connection verified successfully!');
        btn.disabled = false;
        btn.innerHTML = originalText;
    })
    .catch(error => {
        alert('Connection test failed: ' + error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function uploadDoFiles() {
    if(!confirm('Are you sure you want to start uploading files to DigitalOcean Spaces? This might take a while.')) return;
    
    const btn = document.getElementById('doUploadSyncBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';
    
    fetch('{{ route("admin.settings.digitalocean.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Media upload to DigitalOcean initiated!');
        btn.disabled = false;
        btn.innerHTML = originalText;
    })
    .catch(error => {
        alert('Upload error: ' + error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endpush
