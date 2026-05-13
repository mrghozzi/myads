@extends('admin::layouts.admin')

@section('title', __('messages.amazon_s3_config'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.amazon_s3_config') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_module_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.amazon_s3_config') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.amazon_s3_storage_help') }}</p>
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
            <form action="{{ route('admin.settings.amazon_s3.update') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-12">
                    <div class="admin-utility-card">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="amazon_s3_storage" name="amazon_s3_storage" value="1" {{ ($options['amazon_s3_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="amazon_s3_storage">{{ __('messages.amazon_s3_storage') }}</label>
                                <div class="small text-muted">{{ __('messages.amazon_s3_storage_help') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.amazon_bucket_name') }}</label>
                    <input type="text" name="amazon_bucket_name" class="form-control" value="{{ $options['amazon_bucket_name']->o_valuer ?? '' }}" placeholder="Bucket Name">
                    <small class="text-muted">{{ __('messages.amazon_bucket_name_help') }}</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.amazon_s3_bucket_region') }}</label>
                    <input type="text" name="amazon_s3_bucket_region" class="form-control" value="{{ $options['amazon_s3_bucket_region']->o_valuer ?? 'us-east-1' }}" placeholder="us-east-1">
                    <small class="text-muted">{{ __('messages.amazon_s3_bucket_region_help') }}</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.amazon_s3_key') }}</label>
                    <input type="text" name="amazon_s3_key" class="form-control" value="{{ $options['amazon_s3_key']->o_valuer ?? '' }}" placeholder="Access Key ID">
                    <small class="text-muted">{{ __('messages.amazon_s3_key_help') }}</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.amazon_s3_secret_key') }}</label>
                    <input type="password" name="amazon_s3_secret_key" class="form-control" value="{{ $options['amazon_s3_secret_key']->o_valuer ?? '' }}" placeholder="Secret Access Key">
                    <small class="text-muted">{{ __('messages.amazon_s3_secret_key_help') }}</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('messages.amazon_s3_custom_endpoint') }}</label>
                    <input type="text" name="amazon_s3_custom_endpoint" class="form-control" value="{{ $options['amazon_s3_custom_endpoint']->o_valuer ?? '' }}" placeholder="https://custom-endpoint.com">
                    <small class="text-muted">{{ __('messages.amazon_s3_custom_endpoint_help') }}</small>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-3 small mb-0">
                        <p class="mb-2"><strong><i class="fas fa-info-circle me-1"></i> {{ __('messages.important_note') ?? 'Important' }}:</strong></p>
                        <ul class="ps-3 mb-0">
                            <li>{{ __('messages.s3_upload_warning') }}</li>
                            <li>{{ __('messages.s3_download_warning') }}</li>
                            <li>{{ __('messages.s3_recommend_s3cmd') }}</li>
                            <li>{{ __('messages.s3_brand_new_site') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 justify-content-between mt-4">
                    <div>
                        <button type="button" class="btn btn-success px-4" onclick="testConnection()">{{ __('messages.test_verify_connection') }}</button>
                        <button type="button" class="btn btn-info text-white px-4" onclick="uploadFiles()">{{ __('messages.upload_files_to_amazon') }}</button>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Testing...';
    
    fetch('{{ route("admin.settings.amazon_s3.test") }}', {
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
        btn.innerHTML = '{{ __('messages.test_verify_connection') }}';
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.test_verify_connection') }}';
    });
}

function uploadFiles() {
    if(!confirm('Are you sure you want to start uploading files to Amazon S3? This might take a while.')) return;
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';
    
    fetch('{{ route("admin.settings.amazon_s3.upload") }}', {
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
        btn.innerHTML = '{{ __('messages.upload_files_to_amazon') }}';
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.upload_files_to_amazon') }}';
    });
}
</script>
@endpush
