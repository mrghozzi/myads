<div class="widget-box dev-panel">
    <p class="widget-box-title">{{ __('messages.dev_platform') }}</p>
    <div class="widget-box-content padding-none">
        <a href="{{ route('developer.index') }}" class="dev-nav-link {{ ($active ?? 'overview') === 'overview' ? 'is-active' : '' }}">
            <span><i class="fa fa-compass me-2"></i>{{ __('messages.overview') }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.apps.index') }}" class="dev-nav-link {{ ($active ?? '') === 'apps' ? 'is-active' : '' }}">
            <span><i class="fa fa-cubes me-2"></i>{{ __('messages.my_apps') }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.apps.create') }}" class="dev-nav-link {{ ($active ?? '') === 'create' ? 'is-active' : '' }}">
            <span><i class="fa fa-plus-circle me-2"></i>{{ __('messages.create_app') }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.tokens.index') }}" class="dev-nav-link {{ ($active ?? '') === 'tokens' ? 'is-active' : '' }}">
            <span><i class="fa fa-key me-2"></i>{{ __('messages.personal_tokens') ?? 'API Tokens (PAT)' }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.playground') }}" class="dev-nav-link {{ ($active ?? '') === 'playground' ? 'is-active' : '' }}">
            <span><i class="fa fa-terminal me-2"></i>{{ __('messages.api_playground') ?? 'API Playground' }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.directory.index') }}" class="dev-nav-link {{ ($active ?? '') === 'directory' ? 'is-active' : '' }}">
            <span><i class="fa fa-store me-2"></i>{{ __('messages.app_directory') ?? 'App Directory' }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.guides') }}" class="dev-nav-link {{ ($active ?? '') === 'guides' ? 'is-active' : '' }}">
            <span><i class="fa fa-book me-2"></i>{{ __('messages.dev_guides') ?? 'Documentation' }}</span>
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ route('developer.openapi') }}" target="_blank" class="dev-nav-link">
            <span><i class="fa fa-file-code me-2"></i>OpenAPI 3.1 JSON</span>
            <i class="fa fa-external-link-alt" style="font-size: 11px;"></i>
        </a>
    </div>
</div>
