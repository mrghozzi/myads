@extends('theme::layouts.master')

@section('title', __('messages.create_app'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-plus-circle" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.create_app') }}</p>
    <p class="section-banner-text">{{ __('messages.dev_create_help') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'create'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if(session('success'))
            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="dev-note dev-note--danger" style="margin-bottom: 20px;">
                <strong>{{ session('error') }}</strong>
            </div>
        @endif

        @if($errors->any())
            <div class="dev-note dev-note--danger">
                <strong>{{ __('messages.save') }}</strong>
                <div class="dev-card-copy">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div id="dev-form-alert" class="dev-note dev-note--danger" style="display: none; margin-bottom: 20px;"></div>

        <div class="widget-box dev-panel">
            <p class="widget-box-title">{{ __('messages.create_app') }}</p>
            <div class="widget-box-content" style="padding: 32px;">
                <form action="{{ route('developer.apps.store', [], false) }}" method="POST" class="dev-form-layout" id="dev-create-app-form">
                    @csrf

                    @include('theme::developer.partials.form_fields', [
                        'scopes' => $scopes,
                        'scopeInputPrefix' => 'developer_create_scope',
                    ])

                    <div class="dev-form-actions">
                        <a href="{{ route('developer.apps.index') }}" class="button secondary">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="button primary" id="dev-submit-btn">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="dev-side-stack">
            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ __('messages.information') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="dev-card-copy">{{ __('messages.dev_create_help') }}</p>
                    <ul class="dev-list-reset" style="margin-top: 18px;">
                        <li>
                            <i class="fa fa-check-circle"></i>
                            <span>{{ __('messages.dev_https_hint') }}</span>
                        </li>
                        <li>
                            <i class="fa fa-check-circle"></i>
                            <span>{{ __('messages.dev_scopes_help') }}</span>
                        </li>
                        <li>
                            <i class="fa fa-check-circle"></i>
                            <span>{{ __('messages.submit_for_review') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ __('messages.dev_docs') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="dev-card-copy">{{ __('messages.dev_widgets_desc') }}</p>
                    <div class="dev-chip-row" style="margin-top: 18px;">
                        <span class="dev-chip">
                            <i class="fa fa-user-shield"></i>
                            OAuth 2.0
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-share-nodes"></i>
                            Share API
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('dev-create-app-form');
    if (!form) return;

    const getCsrfToken = function () {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || form.querySelector('input[name="_token"]')?.value 
            || '';
    };

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const submitBtn = document.getElementById('dev-submit-btn');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.saving") ?? "Saving..." }}';
        }

        const alertContainer = document.getElementById('dev-form-alert');
        if (alertContainer) alertContainer.style.display = 'none';

        try {
            const csrfToken = getCsrfToken();
            const scopes = [];
            form.querySelectorAll('input[name="requested_scopes[]"]:checked').forEach(function (cb) {
                scopes.push(cb.value);
            });

            const payload = {
                _token: csrfToken,
                name: form.querySelector('input[name="name"]')?.value || '',
                domain: form.querySelector('input[name="domain"]')?.value || '',
                description: form.querySelector('textarea[name="description"]')?.value || '',
                redirect_uris: form.querySelector('textarea[name="redirect_uris"]')?.value || '',
                requested_scopes: scopes
            };

            const storeUrl = '{{ route("developer.apps.store") }}';

            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const rawText = await response.text();
            let data = {};
            try {
                data = JSON.parse(rawText);
            } catch (err) {
                data = {};
            }

            if (response.ok && data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
                return;
            }

            let errorMsg = '';
            if (data.message) {
                errorMsg = data.message;
            } else if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            } else if (response.status === 419) {
                errorMsg = 'Session/CSRF expired (HTTP 419). Please refresh the page and log in again.';
            } else {
                let specificError = '';
                try {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(rawText, 'text/html');
                    const title = doc.querySelector('.error-title')?.innerText || doc.querySelector('h1')?.innerText || doc.title || '';
                    const message = doc.querySelector('.error-message')?.innerText || doc.querySelector('.error-card p')?.innerText || doc.querySelector('p')?.innerText || '';
                    specificError = (title + (message ? ' — ' + message : '')).trim();
                } catch (e) {}

                const cleanSnippet = rawText.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
                                           .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
                                           .replace(/<[^>]+>/g, ' ')
                                           .replace(/\s+/g, ' ')
                                           .trim()
                                           .substring(0, 300);

                errorMsg = '[HTTP ' + response.status + ' ' + response.statusText + ']<br><strong>Target URL:</strong> ' + response.url + '<br><strong>Details:</strong> ' + (specificError || cleanSnippet || 'Permission Denied / Blocked');
            }

            if (alertContainer) {
                alertContainer.className = 'dev-note dev-note--danger';
                alertContainer.innerHTML = '<strong>' + errorMsg + '</strong>';
                alertContainer.style.display = 'block';
                alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert(errorMsg);
            }
        } catch (err) {
            console.error('AJAX Submit Exception:', err);
            if (alertContainer) {
                alertContainer.className = 'dev-note dev-note--danger';
                alertContainer.innerHTML = '<strong>Network / Script Error: ' + err.message + '</strong>';
                alertContainer.style.display = 'block';
            } else {
                alert('Network Error: ' + err.message);
            }
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    });
});
</script>
@endpush
