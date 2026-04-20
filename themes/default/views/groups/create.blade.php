@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: linear-gradient(135deg, rgba(255,107,61,0.95), rgba(97,93,250,0.92));">
    <p class="section-banner-title">{{ __('messages.groups_create_title') }}</p>
    <p class="section-banner-text">{{ __('messages.groups_create_description') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <x-widget-column side="groups_left" />
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-inline-start: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <p class="mb-2"><strong>{{ __('messages.groups_creation_policy') }}:</strong> {{ __('messages.groups_policy_' . $eligibility['policy']) }}</p>
                    @if($eligibility['initial_status'] === \App\Models\Group::STATUS_PENDING_REVIEW)
                        <p class="mb-0 text-muted">{{ __('messages.groups_creation_review_notice') }}</p>
                    @endif
                </div>

                <form method="POST" action="{{ route('groups.store') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small">
                                <label for="group-name">{{ __('messages.name') }}</label>
                                <input id="group-name" type="text" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="form-item">
                            <div class="form-input small">
                                <label for="group-slug">{{ __('messages.slug') }}</label>
                                <input id="group-slug" type="text" name="slug" value="{{ old('slug') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-input small">
                            <label for="group-short-description">{{ __('messages.groups_short_description') }}</label>
                            <input id="group-short-description" type="text" name="short_description" value="{{ old('short_description') }}">
                        </div>
                    </div>

                    <div class="form-item">
                        <label class="mb-2 d-block">{{ __('messages.groups_privacy') }}</label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                            <label class="widget-box" style="margin:0;">
                                <div class="widget-box-content">
                                    <input type="radio" name="privacy" value="public" {{ old('privacy', 'public') === 'public' ? 'checked' : '' }}>
                                    <strong>{{ __('messages.groups_public') }}</strong>
                                    <p class="text-muted mb-0">{{ __('messages.groups_public_hint') }}</p>
                                </div>
                            </label>
                            <label class="widget-box" style="margin:0;">
                                <div class="widget-box-content">
                                    <input type="radio" name="privacy" value="private_request" {{ old('privacy') === 'private_request' ? 'checked' : '' }}>
                                    <strong>{{ __('messages.groups_private') }}</strong>
                                    <p class="text-muted mb-0">{{ __('messages.groups_private_hint') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-input small">
                            <label for="group-description">{{ __('messages.description') }}</label>
                            <textarea id="group-description" name="description" rows="6">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-input small">
                            <label for="group-rules">{{ __('messages.groups_rules') }}</label>
                            <textarea id="group-rules" name="rules_markdown" rows="6">{{ old('rules_markdown') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="button secondary">{{ __('messages.groups_create_submit') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <x-widget-column side="groups_right" />
    </div>
</div>
@endsection
