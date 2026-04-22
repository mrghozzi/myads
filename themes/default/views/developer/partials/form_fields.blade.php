@php
    $developerApp = $app ?? null;
    $selectedScopes = old('requested_scopes', $developerApp ? ($developerApp->requested_scopes ?? []) : []);
    if (!is_array($selectedScopes)) {
        $selectedScopes = [];
    }

    $redirectUrisValue = old(
        'redirect_uris',
        $developerApp ? implode(', ', $developerApp->redirect_uris ?? []) : ''
    );
@endphp

<div class="dev-form-section">
    <div>
        <p class="dev-kicker">{{ __('messages.configuration') }}</p>
        <h3 class="dev-section-title">{{ __('messages.app_specifications') }}</h3>
    </div>

    <div class="dev-form-grid">
        <div class="dev-field">
            <label for="app_name">{{ __('messages.app_name') }} <span class="text-danger">*</span></label>
            <input
                id="app_name"
                type="text"
                name="name"
                class="form-control dev-control @error('name') is-invalid @enderror"
                value="{{ old('name', $developerApp->name ?? '') }}"
                required
            >
            @error('name')
                <span class="dev-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="dev-field">
            <label for="app_domain">{{ __('messages.domain') }} <span class="text-danger">*</span></label>
            <input
                id="app_domain"
                type="url"
                name="domain"
                class="form-control dev-control @error('domain') is-invalid @enderror"
                value="{{ old('domain', $developerApp->domain ?? '') }}"
                placeholder="https://example.com"
                required
            >
            @error('domain')
                <span class="dev-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="dev-field dev-form-grid__full">
            <label for="app_description">{{ __('messages.description') }} <span class="text-danger">*</span></label>
            <textarea
                id="app_description"
                name="description"
                rows="4"
                class="form-control dev-control dev-control--textarea @error('description') is-invalid @enderror"
                required
            >{{ old('description', $developerApp->description ?? '') }}</textarea>
            @error('description')
                <span class="dev-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="dev-form-section">
    <div>
        <p class="dev-kicker">{{ __('messages.information') }}</p>
        <h3 class="dev-section-title">{{ __('messages.redirect_uris') }}</h3>
        <p class="dev-help-text">{{ __('messages.redirect_uris_help') }}</p>
    </div>

    <div class="dev-field">
        <label for="redirect_uris">{{ __('messages.redirect_uris') }} <span class="text-danger">*</span></label>
        <textarea
            id="redirect_uris"
            name="redirect_uris"
            rows="3"
            class="form-control dev-control dev-control--textarea @error('redirect_uris') is-invalid @enderror"
            placeholder="https://example.com/callback, https://example.com/oauth/return"
            required
        >{{ $redirectUrisValue }}</textarea>
        <span class="dev-help-text">{{ __('messages.dev_https_hint') }}</span>
        @error('redirect_uris')
            <span class="dev-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="dev-form-section">
    <div class="dev-card-head">
        <div>
            <p class="dev-kicker">{{ __('messages.eligibility') }}</p>
            <h3 class="dev-section-title">{{ __('messages.requested_scopes') }}</h3>
        </div>
        <span class="dev-mini-chip">
            <i class="fa fa-shield-halved"></i>
            {{ count($selectedScopes) }}
        </span>
    </div>

    <p class="dev-help-text">{{ __('messages.dev_scopes_help') }}</p>

    @include('theme::developer.partials.scope_grid', [
        'scopes' => $scopes,
        'selectedScopes' => $selectedScopes,
        'scopeInputPrefix' => $scopeInputPrefix ?? 'developer_scope_form',
    ])
</div>
