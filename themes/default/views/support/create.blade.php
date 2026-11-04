@extends('theme::layouts.master')

@section('title', $pageTitle ?? (__('support.ask_question') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .support-create-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 28px;
            align-items: start;
        }

        .support-form-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 22px);
            padding: 32px 30px;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .support-form-header {
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .support-form-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 6px 0;
        }

        .support-form-desc {
            font-size: 0.92rem;
            color: var(--myads-text-muted, #64748b);
            margin: 0;
        }

        .support-form-group {
            margin-bottom: 22px;
        }

        .support-form-label {
            display: block;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 8px;
        }

        .support-form-select,
        .support-form-input,
        .support-form-textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--myads-surface-border, #cbd5e1);
            background: var(--myads-surface, #ffffff);
            font-size: 0.95rem;
            color: var(--myads-text-main, #1e293b);
            outline: none;
            transition: all 0.2s ease;
        }

        .support-form-select:focus,
        .support-form-input:focus,
        .support-form-textarea:focus {
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
        }

        .support-form-textarea {
            min-height: 220px;
            resize: vertical;
            line-height: 1.6;
        }

        .btn-submit-support {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            border: none;
            padding: 14px 32px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 6px 20px rgba(97, 93, 250, 0.35);
        }

        .btn-submit-support:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.5);
        }

        /* Sidebar tips card */
        .support-tips-card {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.06) 0%, rgba(35, 210, 226, 0.06) 100%);
            border: 1px solid var(--myads-surface-border, rgba(97, 93, 250, 0.2));
            border-radius: var(--myads-radius-xl, 20px);
            padding: 26px 22px;
        }

        .support-tips-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 16px 0;
        }

        .support-tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .support-tips-list li {
            font-size: 0.88rem;
            color: var(--myads-text-muted, #475569);
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .support-tips-list li i {
            color: #10b981;
            margin-top: 3px;
        }

        .crumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 20px;
        }

        .crumbs a {
            color: var(--myads-primary, #615dfa);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .support-create-grid { grid-template-columns: 1fr; }
        }
    </style>

    <!-- Breadcrumb -->
    <div class="crumbs">
        <a href="{{ route('support.index') }}"><i class="fa-solid fa-house"></i> {{ __('support.support_forum') }}</a>
        <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}" style="font-size: 0.75rem;"></i>
        <span>{{ __('support.ask_question') }}</span>
    </div>

    <div class="support-create-grid">
        <div class="support-form-card">
            <div class="support-form-header">
                <h1 class="support-form-title">
                    <i class="fa-solid fa-pen-to-square" style="color: var(--myads-primary, #615dfa);"></i>
                    {{ __('support.ask_question') }}
                </h1>
                <p class="support-form-desc">
                    {{ __('support.support_forum_desc', ['site' => $siteName]) }}
                </p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-right: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('support.store') }}" method="POST">
                @csrf

                <div class="support-form-group">
                    <label class="support-form-label" for="category_id">
                        {{ __('support.select_category') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="category_id" id="category_id" class="support-form-select" required>
                        <option value="">-- {{ __('support.select_category') }} --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $selectedCategoryId) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="support-form-group">
                    <label class="support-form-label" for="title">
                        {{ __('support.topic_title') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           class="support-form-input"
                           placeholder="{{ __('support.topic_title_placeholder') }}"
                           value="{{ old('title') }}"
                           required>
                </div>

                <div class="support-form-group">
                    <label class="support-form-label" for="content">
                        {{ __('support.topic_content') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="content"
                              id="content"
                              class="support-form-textarea"
                              placeholder="{{ __('support.topic_content_placeholder') }}"
                              required>{{ old('content') }}</textarea>
                </div>

                <!-- AI Support Deflection Suggestions -->
                <div id="aiDeflectionCard" style="display: none; margin-bottom: 24px; background: linear-gradient(135deg, rgba(97, 93, 250, 0.05) 0%, rgba(35, 210, 226, 0.05) 100%); border: 1px solid rgba(97, 93, 250, 0.25); border-radius: 16px; padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: var(--myads-primary, #615dfa); font-size: 0.95rem;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span>{{ __('support.ai_deflection_heading') }}</span>
                        </div>
                        <span style="font-size: 0.75rem; background: rgba(97, 93, 250, 0.12); color: var(--myads-primary, #615dfa); padding: 4px 10px; border-radius: 9999px; font-weight: 700;">
                            {{ __('support.ai_deflection_badge') }}
                        </span>
                    </div>
                    <p style="font-size: 0.85rem; color: var(--myads-text-muted, #64748b); margin-bottom: 14px;">
                        {{ __('support.ai_deflection_desc') }}
                    </p>
                    <div id="aiDeflectionList" style="display: flex; flex-direction: column; gap: 10px;"></div>
                </div>

                <x-turnstile :form="'support'" />

                <button type="submit" class="btn-submit-support">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ __('support.post_topic') }}</span>
                </button>
            </form>
        </div>

        <!-- Sidebar Guidelines -->
        <div>
            <div class="support-tips-card">
                <h3 class="support-tips-title">
                    <i class="fa-solid fa-lightbulb" style="color: #f59e0b;"></i>
                    {{ __('support.question_guidelines') }}
                </h3>
                <ul class="support-tips-list">
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.tip_title_specific') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.tip_dept_accurate') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.tip_clear_description') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.tip_no_passwords') }}</span>
                    </li>
                </ul>

                <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid rgba(97, 93, 250, 0.15); text-align: center;">
                    <p style="font-size: 0.85rem; color: var(--myads-text-muted, #64748b); margin-bottom: 12px;">
                        {{ __('support.is_private_issue') }}
                    </p>
                    <a href="{{ route('tickets.create') }}" style="color: var(--myads-primary, #615dfa); font-weight: 700; text-decoration: none; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-ticket"></i>
                        <span>{{ __('support.open_private_ticket') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ csp_nonce() }}">
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const catSelect = document.getElementById('category_id');
    const deflectionCard = document.getElementById('aiDeflectionCard');
    const deflectionList = document.getElementById('aiDeflectionList');

    let debounceTimer = null;

    function checkDeflection() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const title = titleInput ? titleInput.value.trim() : '';
            const content = contentInput ? contentInput.value.trim() : '';
            const query = (title + ' ' + content).trim();
            const cat = catSelect ? catSelect.value : '';

            if (query.length < 4) {
                if (deflectionCard) deflectionCard.style.display = 'none';
                return;
            }

            fetch('{{ route("support.deflect") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ query: query, category: cat })
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.has_matches || (!data.articles?.length && !data.topics?.length)) {
                    if (deflectionCard) deflectionCard.style.display = 'none';
                    return;
                }

                deflectionList.innerHTML = '';
                const items = [...(data.articles || []), ...(data.topics || [])].slice(0, 3);

                items.forEach(item => {
                    const row = document.createElement('div');
                    row.style.cssText = 'background: var(--myads-surface, #ffffff); border: 1px solid rgba(97,93,250,0.18); border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px;';
                    
                    const isArticle = item.type === 'article';
                    const iconClass = isArticle ? 'fa-solid fa-book-open' : 'fa-solid fa-circle-check';
                    const iconColor = isArticle ? 'color: var(--myads-primary, #615dfa);' : 'color: #10b981;';
                    const badgeText = isArticle ? (item.category || 'Guide') : 'Solved Topic';
                    const badgeColor = isArticle ? 'rgba(97,93,250,0.1)' : 'rgba(16,185,129,0.1)';
                    const badgeTextColor = isArticle ? '#615dfa' : '#10b981';

                    row.innerHTML = `
                        <div style="display: flex; align-items: flex-start; gap: 10px; flex: 1; min-width: 0;">
                            <i class="${iconClass}" style="${iconColor} margin-top: 4px; font-size: 1rem;"></i>
                            <div style="min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                    <span style="font-size: 0.72rem; font-weight: 700; background: ${badgeColor}; color: ${badgeTextColor}; padding: 2px 8px; border-radius: 6px;">${badgeText}</span>
                                </div>
                                <a href="${item.url}" target="_blank" rel="noopener" style="font-size: 0.9rem; font-weight: 700; color: var(--myads-text-main, #1e293b); text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    ${item.title} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem; color: #94a3b8; margin-left: 4px;"></i>
                                </a>
                                <p style="font-size: 0.8rem; color: var(--myads-text-muted, #64748b); margin: 3px 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.summary || ''}</p>
                            </div>
                        </div>
                        <a href="${item.url}" target="_blank" rel="noopener" class="btn btn-sm" style="background: rgba(97,93,250,0.1); color: var(--myads-primary, #615dfa); font-weight: 700; border-radius: 8px; font-size: 0.8rem; white-space: nowrap; padding: 6px 12px; text-decoration: none;">
                            <i class="fa-solid fa-eye me-1"></i> {{ __('support.view_solution') }}
                        </a>
                    `;
                    deflectionList.appendChild(row);
                });

                if (deflectionCard) deflectionCard.style.display = 'block';
            })
            .catch(() => {
                if (deflectionCard) deflectionCard.style.display = 'none';
            });
        }, 350);
    }

    if (titleInput) titleInput.addEventListener('input', checkDeflection);
    if (contentInput) contentInput.addEventListener('input', checkDeflection);
});
</script>
@endsection
