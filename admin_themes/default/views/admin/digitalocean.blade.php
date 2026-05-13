@extends('admin::layouts.admin')

@section('title', __('messages.digitalocean_spaces_config'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.digitalocean_spaces_config') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_module_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.digitalocean_spaces_config') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.digitalocean_spaces_storage') }}</p>
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
            <form action="{{ route('admin.settings.digitalocean.update') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-12">
                    <div class="admin-utility-card">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="digitalocean_spaces_storage" name="digitalocean_spaces_storage" value="1" {{ ($options['digitalocean_spaces_storage']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="digitalocean_spaces_storage">{{ __('messages.digitalocean_spaces_storage') }}</label>
                                <div class="small text-muted">Enable Digitalocean Storage to store your files in Digitalocean Spaces.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.digitalocean_space_name') }}</label>
                    <input type="text" name="digitalocean_space_name" class="form-control" value="{{ $options['digitalocean_space_name']->o_valuer ?? '' }}" placeholder="Space Name">
                    <small class="text-muted">Your Digitalocean Space Bucket name.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.digitalocean_key') }}</label>
                    <input type="text" name="digitalocean_key" class="form-control" value="{{ $options['digitalocean_key']->o_valuer ?? '' }}" placeholder="Key">
                    <small class="text-muted">Your Digitalocean Space credentials key.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.digitalocean_secret') }}</label>
                    <input type="password" name="digitalocean_secret" class="form-control" value="{{ $options['digitalocean_secret']->o_valuer ?? '' }}" placeholder="Secret">
                    <small class="text-muted">Your Digitalocean Space credentials secret key.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('messages.digitalocean_bucket_region') }}</label>
                    <select name="digitalocean_bucket_region" class="form-select">
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
                    <small class="text-muted">Your Digitalocean bucket region.</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Digitalocean Custom Endpoint (Optional)</label>
                    <input type="text" name="digitalocean_custom_endpoint" class="form-control" value="{{ $options['digitalocean_custom_endpoint']->o_valuer ?? '' }}" placeholder="https://custom-endpoint.com">
                    <small class="text-muted">Your Digitalocean custom domain name.</small>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-3 small mb-0">
                        <ul class="ps-3 mb-0">
                            <li>Before enabling Digitalocean, make sure you upload the whole "upload/" folder to your bucket.</li>
                            <li>Before disabling Digitalocean, make sure you download the whole "upload/" folder to your server.</li>
                            <li>If your site is still brand new, you can escape the upload step, but make sure to click on "Test Connection".</li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 justify-content-between mt-4">
                    <div>
                        <button type="button" class="btn btn-success px-4" onclick="testDoConnection()">{{ __('messages.test_verify_connection') }}</button>
                        <button type="button" class="btn btn-info text-white px-4" onclick="uploadDoFiles()">{{ __('messages.upload_files_to_digitalocean') }}</button>
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
function testDoConnection() {
    const btn = event.target;
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

function uploadDoFiles() {
    if(!confirm('Are you sure you want to start uploading files to DigitalOcean Spaces? This might take a while.')) return;
    
    const btn = event.target;
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
        alert(data.message);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.upload_files_to_digitalocean') }}';
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = '{{ __('messages.upload_files_to_digitalocean') }}';
    });
}
</script>
@endpush
