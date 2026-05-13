@extends('admin::layouts.admin')

@section('title', __('messages.google_cloud_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.google_cloud_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_module_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.google_cloud_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.google_cloud_storage') }}</p>
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
            <form action="{{ route('admin.settings.google_cloud.update') }}" method="POST" class="row g-4" enctype="multipart/form-data">
                @csrf
                
                <div class="col-12">
                    <div class="admin-utility-card">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="google_cloud_storage" name="google_cloud_storage" value="1" {{ ($options['google_cloud_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="google_cloud_storage">{{ __('messages.google_cloud_storage') }}</label>
                                <div class="small text-muted">Enable Google Cloud Storage to store your files in Google Cloud.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.google_cloud_bucket_name') }}</label>
                    <input type="text" name="google_cloud_bucket_name" class="form-control" value="{{ $options['google_cloud_bucket_name']->o_valuer ?? '' }}" placeholder="Bucket Name">
                    <small class="text-muted">Your Google Cloud Bucket Name.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.google_cloud_file') }}</label>
                    <input type="file" name="google_cloud_file" class="form-control">
                    <small class="text-muted">Should be a JSON file.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.google_cloud_file_path') }}</label>
                    <input type="text" name="google_cloud_file_path" class="form-control" value="{{ $options['google_cloud_file_path']->o_valuer ?? '' }}" readonly>
                    <small class="text-muted">Path to your Google Cloud File in your server.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.google_cloud_custom_endpoint') }}</label>
                    <input type="text" name="google_cloud_custom_endpoint" class="form-control" value="{{ $options['google_cloud_custom_endpoint']->o_valuer ?? '' }}" placeholder="https://custom-endpoint.com">
                    <small class="text-muted">Your Google Cloud custom domain name.</small>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-3 small mb-0">
                        <ul class="ps-3 mb-0">
                            <li>Make sure you upload the whole "upload/" folder to your bucket.</li>
                            <li>Make sure to keep (Google Cloud File) on your server.</li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 justify-content-between mt-4">
                    <button type="button" class="btn btn-success px-4" onclick="testCloudConnection()">{{ __('messages.google_cloud_test_connection') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function testCloudConnection() {
    const btn = event.target;
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
        alert(data.message);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.google_cloud_test_connection') }}';
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.google_cloud_test_connection') }}';
    });
}
</script>
@endpush
