@extends('theme::layouts.master')

@section('title', __('messages.personal_tokens') ?? 'Personal Access Tokens (PAT)')

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-key" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.personal_tokens') ?? 'Personal Access Tokens (PAT)' }}</p>
    <p class="section-banner-text">Direct API keys for automation scripts, cron jobs, and bots without OAuth redirects.</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'tokens'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if(session('new_token_plain'))
            <!-- One-time Plain Token Display Warning Alert -->
            <div style="background: rgba(31, 157, 98, 0.12); border: 2px solid #1f9d62; border-radius: 12px; padding: 22px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fa fa-shield-halved text-success" style="font-size: 20px;"></i>
                    <strong style="font-size: 16px; color: #1f9d62;">Token Generated Successfully!</strong>
                </div>
                <p style="font-size: 13px; color: var(--dev-text); margin-bottom: 14px;">
                    <strong>Important:</strong> Copy your personal access token now. For your security, it will <strong>never be shown again</strong>.
                </p>
                <div class="dev-credential-input" style="display: flex; gap: 8px;">
                    <input id="new-pat-token-display" type="text" class="form-control dev-control" value="{{ session('new_token_plain') }}" readonly style="font-family: monospace; font-size: 14px; font-weight: 700;">
                    <button type="button" class="button primary js-dev-copy" data-copy-target="#new-pat-token-display" style="white-space: nowrap;">
                        <i class="fa fa-copy me-1"></i> Copy
                    </button>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Create Token Form Box -->
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <p class="widget-box-title">Generate New Personal Access Token</p>
            <div class="widget-box-content" style="padding: 26px;">
                <form action="{{ route('developer.tokens.store') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 18px;">
                        <div class="dev-field">
                            <label for="token_name"><strong>Token Name / Purpose</strong> <span class="text-danger">*</span></label>
                            <input id="token_name" type="text" name="name" class="form-control dev-control" placeholder="e.g. Content Publisher Bot, Backup Script" required>
                        </div>
                        <div class="dev-field">
                            <label for="expires_days"><strong>Expiration</strong></label>
                            <select id="expires_days" name="expires_days" class="form-control dev-control">
                                <option value="30">30 Days</option>
                                <option value="60">60 Days</option>
                                <option value="90" selected>90 Days (Recommended)</option>
                                <option value="365">1 Year</option>
                                <option value="0">No Expiration</option>
                            </select>
                        </div>
                    </div>

                    <div class="dev-field" style="margin-bottom: 20px;">
                        <label><strong>Select Scopes</strong> <span class="text-danger">*</span></label>
                        <p class="dev-help-text">Grant only the minimal permissions required for your automation script.</p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 10px; margin-top: 10px;">
                            @foreach($scopes as $scopeKey => $scopeMeta)
                                <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px; border: 1px solid var(--dev-border); border-radius: 8px; cursor: pointer; background: var(--dev-surface-soft);">
                                    <input type="checkbox" name="scopes[]" value="{{ $scopeKey }}" style="margin-top: 3px;">
                                    <div>
                                        <strong style="font-size: 13px; color: var(--dev-title); font-family: monospace;">{{ $scopeKey }}</strong>
                                        <p style="font-size: 11px; color: var(--dev-muted); margin: 2px 0 0;">{{ __($scopeMeta['name']) }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="button primary">
                        <i class="fa fa-key me-1"></i> Generate Token
                    </button>
                </form>
            </div>
        </div>

        <!-- Existing Tokens List -->
        <div class="widget-box dev-panel">
            <p class="widget-box-title">Active Personal Access Tokens ({{ $tokens->count() }})</p>
            <div class="widget-box-content padding-none">
                @if($tokens->isEmpty())
                    <div style="text-align: center; padding: 36px; color: var(--dev-muted);">
                        <i class="fa fa-key" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <p>You have not generated any Personal Access Tokens yet.</p>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dev-border); text-align: left; background: var(--dev-surface-soft);">
                                    <th style="padding: 12px 16px;">Name</th>
                                    <th style="padding: 12px 16px;">Scopes</th>
                                    <th style="padding: 12px 16px;">Last Used</th>
                                    <th style="padding: 12px 16px;">Expires</th>
                                    <th style="padding: 12px 16px; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokens as $tok)
                                    <tr style="border-bottom: 1px solid var(--dev-border);">
                                        <td style="padding: 12px 16px;">
                                            <strong style="color: var(--dev-title); font-size: 14px;">{{ $tok->name }}</strong>
                                            <div style="font-size: 11px; color: var(--dev-muted); font-family: monospace;">Created {{ $tok->created_at->format('Y-m-d') }}</div>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <div style="display: flex; gap: 4px; flex-wrap: wrap; max-width: 250px;">
                                                @foreach((array) $tok->scopes as $sc)
                                                    <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; background: rgba(97, 93, 250, 0.1); color: var(--dev-accent); font-family: monospace;">{{ $sc }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--dev-muted); font-size: 12px;">
                                            {{ $tok->last_used_at ? $tok->last_used_at->diffForHumans() : 'Never' }}
                                        </td>
                                        <td style="padding: 12px 16px; font-size: 12px;">
                                            @if($tok->expires_at)
                                                <span class="{{ $tok->isExpired() ? 'text-danger' : 'text-muted' }}">
                                                    {{ $tok->expires_at->format('Y-m-d') }} ({{ $tok->expires_at->diffForHumans() }})
                                                </span>
                                            @else
                                                <span class="text-success">Never</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 16px; text-align: right;">
                                            <form action="{{ route('developer.tokens.destroy', $tok->id) }}" method="POST" onsubmit="return confirm('Revoke this personal access token immediately? Any script using it will stop working.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button white text-danger" style="font-size: 12px; height: 30px; line-height: 30px; padding: 0 10px;">
                                                    <i class="fa fa-trash me-1"></i> Revoke
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var copyBtn = document.querySelector('.js-dev-copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            var input = document.getElementById('new-pat-token-display');
            if (input) {
                input.select();
                navigator.clipboard.writeText(input.value);
                this.innerHTML = '<i class="fa fa-check me-1"></i> Copied!';
                setTimeout(function() {
                    copyBtn.innerHTML = '<i class="fa fa-copy me-1"></i> Copy';
                }, 2000);
            }
        });
    }
});
</script>
@endpush
@endsection
