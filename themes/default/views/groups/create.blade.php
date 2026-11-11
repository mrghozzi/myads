@extends('theme::layouts.master')

@section('content')
        <div class="section-banner"
            style="background: linear-gradient(135deg, rgba(97,93,250,0.95), rgba(35,210,226,0.92));">
            <p class="section-banner-title">{{ __('messages.groups_create_title') }}</p>
            <p class="section-banner-text">{{ __('messages.groups_create_description') }}</p>
        </div>

            <div class="grid grid-8-4 mobile-prefer-content">
                <div class="grid-column">
                    <div class="widget-box">
                            <div class="widget-box-content" style="padding: 32px;">
                                @if($errors->any())
                                        <div class="alert alert-danger mb-4">
                                            <ul style="margin: 0; padding-inline-start: 20px;">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                @endif

                                        <div class="mb-4 p-3 rounded"
                                            style="background: rgba(97, 93, 250, 0.08); border: 1px solid rgba(97, 93, 250, 0.15);">
                                            <p class="mb-1"><strong><i class="fa fa-shield-alt text-primary me-1"></i>
                                                    {{ __('messages.groups_creation_policy') }}:</strong>
                                                {{ __('messages.groups_policy_' . $eligibility['policy']) }}</p>
                                            @if($eligibility['initial_status'] === \App\Models\Group::STATUS_PENDING_REVIEW)
                                                <p class="mb-0 text-muted small">
                                                    {{ __('messages.groups_creation_review_notice') }}</p>
                                            @endif
                                        </div>

                                            <form method="POST" action="{{ route('groups.store') }}" id="group-create-form">
                                                @csrf

                                                <div class="form-row">
                                                    <div class="form-item">
                                                            <div class="form-input small active">
                                                                <label for="group-name">{{ __('messages.name') }} *</label>
                                                                <input id="group-name" type="text" name="name"
                                                                    value="{{ old('name') }}" required
                                                                    placeholder="e.g. Laravel Masters"
                                                                    oninput="updatePreview()">
                                                            </div>
                                                        </div>

                                                        <div class="form-item">
                                                                <div class="form-input small active">
                                                                    <label
                                                                        for="group-slug">{{ __('messages.slug') }}</label>
                                                                    <input id="group-slug" type="text" name="slug"
                                                                        value="{{ old('slug') }}"
                                                                        placeholder="e.g. laravel-masters"
                                                                        oninput="updatePreview()">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-row">
                                                            <div class="form-item">
                                                                <label class="form-label"
                                                                    style="font-weight: 700; margin-bottom: 8px;">{{ __('messages.groups_category') }}
                                                                    *</label>
                                                                <select name="category" class="form-select"
                                                                    style="height: 48px; border-radius: 12px; width: 100%; border: 1px solid #e8ebf5; padding: 0 14px;"
                                                                    onchange="updatePreview()">
                                                                    @foreach($categories as $catKey => $catLabelKey)
                                                                        <option value="{{ $catKey }}" {{ old('category', 'general') === $catKey ? 'selected' : '' }}>
                                                                            {{ __($catLabelKey) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-item">
                                                                <label class="form-label"
                                                                    style="font-weight: 700; margin-bottom: 8px;">{{ __('messages.groups_posting_permission') }}
                                                                    *</label>
                                                                <select name="posting_permission" class="form-select"
                                                                    style="height: 48px; border-radius: 12px; width: 100%; border: 1px solid #e8ebf5; padding: 0 14px;">
                                                                    <option value="all_members" {{ old('posting_permission', 'all_members') === 'all_members' ? 'selected' : '' }}>
                                                                        {{ __('messages.groups_posting_all') }}
                                                                    </option>
                                                                    <option value="moderators_only" {{ old('posting_permission') === 'moderators_only' ? 'selected' : '' }}>
                                                                        {{ __('messages.groups_posting_moderators') }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-item">
                                                                <div class="form-input small active">
                                                                    <label
                                                                        for="group-join-fee">{{ __('messages.groups_join_fee') }}</label>
                                                                    <input id="group-join-fee" type="number" min="0"
                                                                        max="100000" name="join_fee_pts"
                                                                        value="{{ old('join_fee_pts', 0) }}"
                                                                        oninput="updatePreview()">
                                                                </div>
                                                                <small
                                                                    class="text-muted d-block mt-1">{{ __('messages.groups_join_fee_hint') }}</small>
                                                            </div>

                                                            <div class="form-item">
                                                                <div class="form-input small active">
                                                                    <label
                                                                        for="group-short-description">{{ __('messages.groups_short_description') }}</label>
                                                                    <input id="group-short-description" type="text"
                                                                        name="short_description"
                                                                        value="{{ old('short_description') }}"
                                                                        maxlength="280"
                                                                        placeholder="Brief summary of your community"
                                                                        oninput="updatePreview()">
                                                                </div>
                                                            </div>

                                                            <div class="form-item">
                                                                <label class="mb-2 d-block"
                                                                    style="font-weight: 700;">{{ __('messages.groups_privacy') }}</label>
                                                                <div
                                                                    style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                                                                        <label class="widget-box"
                                                                            style="margin:0; cursor: pointer;">
                                                                            <div class="widget-box-content">
                                                                                <input type="radio" name="privacy"
                                                                                    value="public" {{ old('privacy', 'public') === 'public' ? 'checked' : '' }}
                                                                                    onchange="updatePreview()">
                                                                                <strong>{{ __('messages.groups_public') }}</strong>
                                                                                <p class="text-muted mb-0 small">
                                                                                    {{ __('messages.groups_public_hint') }}
                                                                                </p>
                                                                            </div>
                                                                        </label>
                                                                            <label class="widget-box"
                                                                                style="margin:0; cursor: pointer;">
                                                                                <div class="widget-box-content">
                                                                                    <input type="radio" name="privacy"
                                                                                        value="private_request" {{ old('privacy') === 'private_request' ? 'checked' : '' }}
                                                                                        onchange="updatePreview()">
                                                                                    <strong>{{ __('messages.groups_private') }}</strong>
                                                                                    <p class="text-muted mb-0 small">
                                                                                        {{ __('messages.groups_private_hint') }}
                                                                                    </p>
                                                                                </div>
                                                                            </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-item">
                                                                    <div class="form-input small active">
                                                                        <label
                                                                            for="group-description">{{ __('messages.description') }}</label>
                                                                        <textarea id="group-description" name="description"
                                                                            rows="5"
                                                                            placeholder="Detailed information about the group's purpose and mission">{{ old('description') }}</textarea>
                                                                    </div>
                                                                </div>

                                                                <div class="form-item">
                                                                        <div class="form-input small active">
                                                                            <label
                                                                                for="group-rules">{{ __('messages.groups_rules') }}
                                                                                (Markdown)</label>
                                                                            <textarea id="group-rules" name="rules_markdown"
                                                                                rows="5"
                                                                                placeholder="1. Be respectful&#10;2. No spam or unsolicited promotions">{{ old('rules_markdown') }}</textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px;">
                                                                        <a href="{{ route('groups.index') }}"
                                                                            class="button white">{{ __('messages.cancel') }}</a>
                                                                        <button type="submit" class="button secondary"
                                                                            style="padding: 0 32px;">{{ __('messages.groups_create_submit') }}</button>
                                                                    </div>
                                            </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Preview Column -->
                            <div class="grid-column">
                                <div class="widget-box" style="position: sticky; top: 100px;">
                                    <p class="widget-box-title">{{ __('messages.preview') ?? 'Live Preview' }}</p>
                                    <div class="widget-box-content" style="padding: 20px;">
                                        <div class="groups-card" style="margin: 0; box-shadow: none;">
                                            <div class="groups-card__cover"
                                                style="background-image: url('{{ asset('themes/default/assets/img/cover/01.jpg') }}'); height: 90px;">
                                                <div class="groups-card__cover-overlay"></div>
                                                <div class="groups-card__chips">
                                                    <span class="groups-card__chip groups-card__chip--privacy"
                                                        id="preview-privacy">
                                                        <i class="fa fa-globe me-1"></i> Public
                                                    </span>
                                                    <span class="groups-card__chip groups-card__chip--pts" id="preview-pts"
                                                        style="display: none;">
                                                        <i class="fa fa-coins me-1"></i> 0 PTS
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="groups-card__body" style="padding: 14px 16px;">
                                                <div class="groups-card__avatar-wrap" style="margin-top: -36px;">
                                                    <img class="groups-card__avatar" style="width: 52px; height: 52px;"
                                                        src="{{ asset('upload/group.png') }}" alt="group">
                                                    <span class="groups-card__category" id="preview-cat">General</span>
                                                </div>
                                                <h3 class="groups-card__title" id="preview-title"
                                                    style="font-size: 1.05rem;">
                                                    Your Community Name
                                                </h3>
                                                <p class="groups-card__desc" id="preview-desc"
                                                    style="font-size: 0.82rem; margin-bottom: 12px;">
                                                    Brief summary of your community will appear here.
                                                </p>
                                                <div class="groups-card__meta" style="padding-top: 10px;">
                                                    <span class="groups-card__stat"><i class="fa fa-users"></i> 1
                                                        Member</span>
                                                    <span class="groups-card__stat"><i class="fa fa-comments"></i> 0
                                                        Posts</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@endsection

                    @push('scripts')
                        <script>
                            function updatePreview() {
                                const nameInput = document.getElementById('group-name');
                                const descInput = document.getElementById('group-short-description');
                                const catSelect = document.querySelector('select[name="category"]');
                                const feeInput = document.getElementById('group-join-fee');
                                const privacyRadio = document.querySelector('input[name="privacy"]:checked');

                                const titleEl = document.getElementById('preview-title');
                                const descEl = document.getElementById('preview-desc');
                                const catEl = document.getElementById('preview-cat');
                                const privacyEl = document.getElementById('preview-privacy');
                                const ptsEl = document.getElementById('preview-pts');

                                if (titleEl) {
                                    titleEl.textContent = nameInput && nameInput.value.trim() ? nameInput.value.trim() : 'Your Community Name';
                                }
                                if (descEl) {
                                    descEl.textContent = descInput && descInput.value.trim() ? descInput.value.trim() : 'Brief summary of your community will appear here.';
                                }
                                if (catEl && catSelect) {
                                    catEl.textContent = catSelect.options[catSelect.selectedIndex].text;
                                }
                                if (privacyEl && privacyRadio) {
                                    const isPublic = privacyRadio.value === 'public';
                                    privacyEl.innerHTML = `<i class="fa ${isPublic ? 'fa-globe' : 'fa-lock'} me-1"></i> ${isPublic ? 'Public' : 'Private'}`;
                                }
                                if (ptsEl && feeInput) {
                                    const fee = parseInt(feeInput.value, 10) || 0;
                                    if (fee > 0) {
                                        ptsEl.style.display = 'inline-block';
                                        ptsEl.innerHTML = `<i class="fa fa-coins me-1"></i> ${fee} PTS`;
                                    } else {
                                        ptsEl.style.display = 'none';
                                    }
                                }
                            }
                            document.addEventListener('DOMContentLoaded', updatePreview);
                        </script>
                    @endpush