@extends('admin::layouts.admin')

@section('title', __('messages.ftp_settings') ?? 'FTP Storage Configuration')

@section('content')
<div class="admin-page storage-config-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}" class="text-white-50">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.media') }}" class="text-white-50">{{ __('messages.media_manager') ?? 'Media Manager' }}</a></li>
                <li class="text-white">FTP Server</li>
            </ul>
            <div class="admin-hero__eyebrow text-white"><i class="fa-solid fa-network-wired me-1"></i> Remote File Transfer Protocol (FTP / SFTP)</div>
            <h1 class="admin-hero__title text-white">FTP Server Storage Config</h1>
            <p class="admin-hero__copy text-white-50">Offload uploaded media assets to a dedicated remote FTP server or external storage node.</p>
        </div>
        <div class="admin-hero__actions d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
            @php $ftpActive = ($options['ftp_storage']->o_valuer ?? '0') == '1'; @endphp
            @if($ftpActive)
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
                        <div class="stat-icon-badge bg-success-soft text-success">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">FTP Host & Credentials</h5>
                            <small class="text-muted">Configure connection parameters for remote storage server</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.ftp.update') }}" method="POST" id="ftpForm">
                        @csrf

                        <!-- Enable Switch Card -->
                        <div class="p-3 bg-light rounded-4 border mb-4">
                            <div class="form-check form-switch px-0 d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label mb-0 fw-bold text-dark cursor-pointer" for="ftp_storage">
                                        <i class="fa-solid fa-network-wired text-success me-2"></i>Enable Remote FTP Storage
                                    </label>
                                    <div class="small text-muted mt-1">When enabled, media uploads will be stored on your external FTP server.</div>
                                </div>
                                <input class="form-check-input ms-3 cursor-pointer" type="checkbox" role="switch" id="ftp_storage" name="ftp_storage" value="1" {{ ($options['ftp_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 48px; height: 24px;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Hostname -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">FTP Hostname / IP <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-server"></i></span>
                                    <input type="text" name="ftp_hostname" class="form-control border-start-0 ps-0" value="{{ $options['ftp_hostname']->o_valuer ?? '' }}" placeholder="ftp.example.com or 192.168.1.100" required>
                                </div>
                                <small class="text-muted smaller">IP address or domain name of your FTP server.</small>
                            </div>

                            <!-- Port -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">FTP Port <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-ethernet"></i></span>
                                    <input type="number" name="ftp_port" class="form-control border-start-0 ps-0" value="{{ $options['ftp_port']->o_valuer ?? '21' }}" placeholder="21" required>
                                </div>
                                <small class="text-muted smaller">Standard FTP port is 21 (or custom port).</small>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">FTP Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" name="ftp_username" class="form-control border-start-0 ps-0" value="{{ $options['ftp_username']->o_valuer ?? '' }}" placeholder="ftp_user">
                                </div>
                                <small class="text-muted smaller">User account name with write access to destination folder.</small>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">FTP Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" id="ftp_password" name="ftp_password" class="form-control border-start-0 border-end-0 ps-0" value="{{ $options['ftp_password']->o_valuer ?? '' }}" placeholder="••••••••••••••••">
                                    <button type="button" class="btn btn-outline-light text-muted bg-white border" onclick="togglePasswordVisibility('ftp_password', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted smaller">FTP user password.</small>
                            </div>

                            <!-- Path -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Remote Storage Directory Path</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-folder-tree"></i></span>
                                    <input type="text" name="ftp_path" class="form-control border-start-0 ps-0" value="{{ $options['ftp_path']->o_valuer ?? './' }}" placeholder="./public_html/upload">
                                </div>
                                <small class="text-muted smaller">Target directory path on the remote FTP server.</small>
                            </div>

                            <!-- Endpoint -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Public CDN / Media Base URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-globe"></i></span>
                                    <input type="url" name="ftp_endpoint" class="form-control border-start-0 ps-0" value="{{ $options['ftp_endpoint']->o_valuer ?? '' }}" placeholder="https://cdn.example.com">
                                </div>
                                <small class="text-muted smaller">Public URL pointing to your FTP upload directory.</small>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-success rounded-3 px-3 fw-semibold" id="ftpTestBtn" onclick="testFtpConnection()">
                                    <i class="fa-solid fa-plug-circle-check me-1"></i> {{ __('messages.ftp_test_connection') ?? 'Test Connection' }}
                                </button>
                                <button type="button" class="btn btn-outline-info text-dark rounded-3 px-3 fw-semibold" id="ftpUploadSyncBtn" onclick="uploadFtpFiles()">
                                    <i class="fa-solid fa-cloud-arrow-up me-1 text-info"></i> {{ __('messages.upload_files_to_ftp') ?? 'Sync Media to FTP Server' }}
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
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-network-wired me-2 text-success"></i>FTP Node Security</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 smaller">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Passive Mode:</strong>
                                <div class="text-muted">Ensure your FTP server supports Passive Mode (PASV) for firewall compatibility.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong class="text-dark">Permissions:</strong>
                                <div class="text-muted">Set target directory permissions to <code>0755</code> for web readability.</div>
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

function testFtpConnection() {
    const btn = document.getElementById('ftpTestBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Testing...';
    
    fetch('{{ route("admin.settings.ftp.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'FTP Server connection verified successfully!');
        btn.disabled = false;
        btn.innerHTML = originalText;
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function uploadFtpFiles() {
    if(!confirm('Are you sure you want to start uploading files to FTP Server? This might take a while.')) return;
    
    const btn = document.getElementById('ftpUploadSyncBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';
    
    fetch('{{ route("admin.settings.ftp.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Media upload to FTP Server initiated!');
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
