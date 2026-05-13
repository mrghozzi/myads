@extends('admin::layouts.admin')

@section('title', __('messages.ffmpeg_config'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.ffmpeg_config') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_module_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.ffmpeg_config') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.ffmpeg_system_help') }}</p>
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
            <form action="{{ route('admin.settings.ffmpeg.update') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-12">
                    <div class="admin-utility-card">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="ffmpeg_system" name="ffmpeg_system" value="1" {{ ($options['ffmpeg_system']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <div>
                                <label class="form-check-label mb-0 fw-semibold" for="ffmpeg_system">{{ __('messages.ffmpeg_system') }}</label>
                                <div class="small text-muted">{{ __('messages.ffmpeg_system_help') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('messages.ffmpeg_binary_path') }}</label>
                    <input type="text" name="ffmpeg_binary_path" class="form-control" value="{{ $options['ffmpeg_binary_path']->o_valuer ?? '/usr/bin/ffmpeg' }}" placeholder="/usr/bin/ffmpeg">
                    <small class="text-muted">Example: /usr/bin/ffmpeg or C:\ffmpeg\bin\ffmpeg.exe</small>
                </div>

                <div class="col-12">
                    <div class="alert alert-warning py-3 small mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ __('messages.ffmpeg_debug_warning') }}
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <h5 class="fw-bold mb-3">{{ __('messages.ffmpeg_debug') }}</h5>
                    <div class="admin-utility-card">
                        <p class="small text-muted mb-3">{{ __('messages.ffmpeg_debug_help') }}</p>
                        <button type="button" class="btn btn-success px-4 mb-3" onclick="debugFfmpeg()">{{ __('messages.ffmpeg_debug') }}</button>
                        
                        <div class="mt-3">
                            <label class="form-label fw-semibold">{{ __('messages.ffmpeg_debug_log') }}</label>
                            <div id="ffmpeg-log" class="bg-dark text-light p-3 rounded small" style="min-height: 100px; font-family: monospace; white-space: pre-wrap;">{{ __('messages.ffmpeg_debug_click') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-2 small">
                        {{ __('messages.ffmpeg_info_doc') }}
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_changes') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function debugFfmpeg() {
    const log = document.getElementById('ffmpeg-log');
    log.innerHTML = 'Testing FFMPEG... please wait.';
    
    fetch('{{ route("admin.settings.ffmpeg.debug") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        log.innerHTML = data.message;
    })
    .catch(error => {
        log.innerHTML = 'Error: ' + error;
    });
}
</script>
@endpush
