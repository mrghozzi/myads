@php
    $turnstileService = app(\App\Services\TurnstileService::class);
    $formName = $form ?? null;
@endphp

@if($turnstileService->isEnabled($formName))
    <div class="turnstile-wrapper my-3" id="cf-turnstile-box-{{ $formName ?? 'default' }}">
        <script nonce="{{ csp_nonce() }}" src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <div class="cf-turnstile"
             data-sitekey="{{ $turnstileService->getSiteKey() }}"
             data-action="{{ $formName ?? 'submit' }}"
             data-theme="auto"
             data-size="flexible"></div>
        @error('cf-turnstile-response')
            <div class="text-danger small mt-1 font-weight-bold">{{ $message }}</div>
        @enderror
    </div>
@endif

