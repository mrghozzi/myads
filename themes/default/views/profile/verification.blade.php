@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.profile_verification') }}</p>
    <p class="section-banner-text">{{ __('messages.verification_member_intro') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">@include('theme::profile.settings_nav')</div>
    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title"><i class="fa-solid fa-circle-check" style="color:#1687f8" aria-hidden="true"></i> {{ __('messages.profile_verification') }}</p>
            <div class="widget-box-content" style="padding:24px">
                @if(session('success')) <div class="alert alert-success" role="alert">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="alert alert-danger" role="alert">{{ session('error') }}</div> @endif
                @if($errors->any()) <div class="alert alert-danger" role="alert">{{ __('messages.please_check_errors') }}</div> @endif

                @if($user->ucheck)
                    <div class="alert alert-success d-flex align-items-center gap-2"><i class="fa-solid fa-circle-check fs-4" aria-hidden="true"></i><strong>{{ __('messages.verification_already_verified') }}</strong></div>
                @else
                    @if(!$settings['enabled'])
                        <div class="alert alert-info">{{ __('messages.verification_closed') }}</div>
                    @endif

                    @if($request)
                        @php $statusKey = 'verification_status_' . $request->status; @endphp
                        <div class="card mb-4" style="border-radius:14px;border:1px solid var(--border-color,#e5e7eb)">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <h4 class="mb-0">{{ __('messages.verification_your_request') }}</h4>
                                    <span class="badge {{ $request->status === 'approved' ? 'bg-success' : ($request->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ __('messages.' . $statusKey) }}</span>
                                </div>
                                <p class="mb-2">{{ $request->reason }}</p>
                                @if($request->evidence_links)
                                    <ul class="mb-2">@foreach($request->evidence_links as $link)<li><a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $link }}</a></li>@endforeach</ul>
                                @endif
                                @if($request->reviewer_note)
                                    <div class="alert alert-secondary mb-0"><strong>{{ __('messages.verification_reviewer_note') }}:</strong> {{ $request->reviewer_note }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($settings['terms'] !== '')
                        <div class="card mb-4" style="border-radius:14px;border:1px solid var(--border-color,#e5e7eb)">
                            <div class="card-body">
                                <h4>{{ __('messages.verification_terms') }}</h4>
                                <div style="white-space:pre-line">{{ $settings['terms'] }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="card mb-4" style="border-radius:14px;border:1px solid var(--border-color,#e5e7eb)">
                        <div class="card-body">
                            <h4 class="mb-3">{{ __('messages.verification_eligibility') }}</h4>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fa {{ $settings['enabled'] ? 'fa-check text-success' : 'fa-times text-danger' }} me-2"></i>{{ __('messages.verification_requirement_enabled') }}</li>
                                @if($settings['require_verified_email'])
                                    <li class="mb-2"><i class="fa {{ $eligibility['email_verified'] ? 'fa-check text-success' : 'fa-times text-danger' }} me-2"></i>{{ __('messages.verification_requirement_email') }}</li>
                                @endif
                                <li class="mb-2"><i class="fa {{ $eligibility['account_age'] ? 'fa-check text-success' : 'fa-times text-danger' }} me-2"></i>{{ __('messages.verification_requirement_age', ['required' => $settings['min_account_age_days'], 'current' => $eligibility['account_age_days']]) }}</li>
                                <li><i class="fa {{ $eligibility['followers'] ? 'fa-check text-success' : 'fa-times text-danger' }} me-2"></i>{{ __('messages.verification_requirement_followers', ['required' => $settings['min_followers_count'], 'current' => $eligibility['followers_count']]) }}</li>
                            </ul>
                        </div>
                    </div>

                    @if($request?->status === 'pending')
                        <div class="alert alert-info">{{ __('messages.verification_pending_exists') }}</div>
                    @elseif($eligibility['eligible'])
                        <form action="{{ route('profile.verification.submit') }}" method="POST" class="form">
                            @csrf
                            <div class="form-item">
                                <label for="reason">{{ __('messages.verification_reason') }}</label>
                                <textarea id="reason" name="reason" rows="5" maxlength="3000" required>{{ old('reason', $request?->status === 'rejected' ? $request->reason : '') }}</textarea>
                                @error('reason')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-item">
                                <label>{{ __('messages.verification_evidence_links') }}</label>
                                <p class="text-muted small">{{ __('messages.verification_evidence_help') }}</p>
                                @for($i = 0; $i < 5; $i++)
                                    <input type="url" name="evidence_links[]" value="{{ old('evidence_links.' . $i, $request?->status === 'rejected' ? ($request->evidence_links[$i] ?? '') : '') }}" placeholder="https://" class="form-input mb-2" aria-label="{{ __('messages.verification_evidence_link_number', ['number' => $i + 1]) }}">
                                @endfor
                                @error('evidence_links.*')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <label class="d-flex align-items-start gap-2 mb-3"><input type="checkbox" name="accept_terms" value="1" required {{ old('accept_terms') ? 'checked' : '' }}><span>{{ __('messages.verification_accept_terms') }}</span></label>
                            @error('accept_terms')<small class="text-danger d-block mb-2">{{ $message }}</small>@enderror
                            <button type="submit" class="button primary">{{ $request?->status === 'rejected' ? __('messages.verification_resubmit') : __('messages.verification_submit') }}</button>
                        </form>
                    @else
                        <div class="alert alert-warning">{{ __('messages.verification_not_eligible') }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
