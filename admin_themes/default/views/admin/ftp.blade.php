@extends('admin::layouts.admin')

@section('title', __('messages.ftp_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.ftp_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_module_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.ftp_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.ftp_storage_help') }}</p>
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
            <form action="{{ route('admin.settings.ftp.update') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-12">
                    <div class="admin-utility-card">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="ftp_storage" name="ftp_storage" value="1" {{ ($options['ftp_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="ftp_storage">{{ __('messages.ftp_storage') }}</label>
                                <div class="small text-muted">{{ __('messages.ftp_storage_help') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.ftp_hostname') }}</label>
                    <input type="text" name="ftp_hostname" class="form-control" value="{{ $options['ftp_hostname']->o_valuer ?? '' }}" placeholder="ftp.example.com">
                    <small class="text-muted">Your FTP hostname, could be IP or domain name.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.ftp_username') }}</label>
                    <input type="text" name="ftp_username" class="form-control" value="{{ $options['ftp_username']->o_valuer ?? '' }}" placeholder="username">
                    <small class="text-muted">Your FTP account's username.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.ftp_password') }}</label>
                    <input type="password" name="ftp_password" class="form-control" value="{{ $options['ftp_password']->o_valuer ?? '' }}" placeholder="password">
                    <small class="text-muted">Your FTP account's password.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.ftp_port') }}</label>
                    <input type="number" name="ftp_port" class="form-control" value="{{ $options['ftp_port']->o_valuer ?? '21' }}" placeholder="21">
                    <small class="text-muted">Your FTP server's port.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.ftp_path') }}</label>
                    <input type="text" name="ftp_path" class="form-control" value="{{ $options['ftp_path']->o_valuer ?? './' }}" placeholder="./">
                    <small class="text-muted">The path to /upload files.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.ftp_endpoint') }}</label>
                    <input type="text" name="ftp_endpoint" class="form-control" value="{{ $options['ftp_endpoint']->o_valuer ?? '' }}" placeholder="https://cdn.example.com">
                    <small class="text-muted">IP or domain where the FTP server is pointed to.</small>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-3 small mb-0">
                        <ul class="ps-3 mb-0">
                            <li>{{ __('messages.ftp_upload_warning') }}</li>
                            <li>{{ __('messages.ftp_download_warning') }}</li>
                            <li>{{ __('messages.s3_brand_new_site') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 justify-content-between mt-4">
                    <button type="button" class="btn btn-success px-4" onclick="testFtpConnection()">{{ __('messages.ftp_test_connection') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function testFtpConnection() {
    const btn = event.target;
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
        alert(data.message);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.ftp_test_connection') }}';
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.ftp_test_connection') }}';
    });
}
</script>
@endpush
