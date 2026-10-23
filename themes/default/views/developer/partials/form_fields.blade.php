@php
    $developerApp = (isset($app) && $app instanceof \App\Models\DeveloperApp) ? $app : null;
    $rawScopes = old('requested_scopes', $developerApp ? ($developerApp->requested_scopes ?? []) : []);
    if (is_string($rawScopes)) {
        $selectedScopes = json_decode($rawScopes, true) ?? [];
    } elseif (is_array($rawScopes)) {
        $selectedScopes = $rawScopes;
    } else {
        $selectedScopes = [];
    }

    $rawRedirectUris = old('redirect_uris', $developerApp ? ($developerApp->redirect_uris ?? '') : '');
    if (is_array($rawRedirectUris)) {
        $redirectUrisValue = implode(', ', $rawRedirectUris);
    } elseif (is_string($rawRedirectUris)) {
        $decoded = json_decode($rawRedirectUris, true);
        $redirectUrisValue = is_array($decoded) ? implode(', ', $decoded) : $rawRedirectUris;
    } else {
        $redirectUrisValue = '';
    }
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

        <div class="dev-field">
            <label for="app_type">{{ __('messages.app_type') ?? 'Application Type' }}</label>
            <select id="app_type" name="app_type" class="form-control dev-control">
                <option value="confidential" {{ old('app_type', $developerApp->app_type ?? 'confidential') === 'confidential' ? 'selected' : '' }}>
                    {{ __('messages.app_type_confidential') ?? 'Confidential (Web Server with Secret)' }}
                </option>
                <option value="public" {{ old('app_type', $developerApp->app_type ?? '') === 'public' ? 'selected' : '' }}>
                    {{ __('messages.app_type_public') ?? 'Public Client (SPA / Mobile with PKCE)' }}
                </option>
            </select>
            <span class="dev-help-text" style="font-size: 11px;">Public applications authenticate with PKCE (RFC 7636).</span>
        </div>

        <div class="dev-field">
            <label for="app_category">{{ __('messages.category') ?? 'Category' }}</label>
            <select id="app_category" name="category" class="form-control dev-control">
                <option value="">-- {{ __('messages.select_category') ?? 'Select Category' }} --</option>
                <option value="social" {{ old('category', $developerApp->category ?? '') === 'social' ? 'selected' : '' }}>Social & Community</option>
                <option value="tools" {{ old('category', $developerApp->category ?? '') === 'tools' ? 'selected' : '' }}>Productivity & Tools</option>
                <option value="content" {{ old('category', $developerApp->category ?? '') === 'content' ? 'selected' : '' }}>Content & Media</option>
                <option value="marketplace" {{ old('category', $developerApp->category ?? '') === 'marketplace' ? 'selected' : '' }}>Marketplace & Orders</option>
                <option value="bots" {{ old('category', $developerApp->category ?? '') === 'bots' ? 'selected' : '' }}>Bots & Automation</option>
            </select>
        </div>

        <div class="dev-field dev-form-grid__full">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="public_listed" value="1" {{ old('public_listed', $developerApp->public_listed ?? false) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span><strong>{{ __('messages.list_in_app_directory') ?? 'Publish this application to the Public App Directory' }}</strong></span>
            </label>
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
