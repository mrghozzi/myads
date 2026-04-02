<div class="alert alert-warning" role="alert" style="margin-bottom: 20px;">
    <strong>{{ $upgradeNotice['title'] ?? __('messages.upgrade_incomplete_title') }}</strong>
    <div style="margin-top: 6px;">
        {{ $upgradeNotice['message'] ?? __('messages.upgrade_legacy_mode_notice') }}
    </div>
</div>
