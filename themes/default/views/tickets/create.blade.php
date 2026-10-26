@extends('theme::layouts.master')

@section('title', $pageTitle ?? (__('support.open_new_ticket') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .ticket-create-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 28px;
            align-items: start;
        }

        .ticket-form-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 22px);
            padding: 34px 30px;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .ticket-form-header {
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .ticket-form-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 6px 0;
        }

        .ticket-form-desc {
            font-size: 0.92rem;
            color: var(--myads-text-muted, #64748b);
            margin: 0;
        }

        .ticket-form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .ticket-form-group {
            margin-bottom: 22px;
        }

        .ticket-form-label {
            display: block;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 8px;
        }

        .ticket-form-select,
        .ticket-form-input,
        .ticket-form-textarea {
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

        .ticket-form-select:focus,
        .ticket-form-input:focus,
        .ticket-form-textarea:focus {
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
        }

        .ticket-form-textarea {
            min-height: 200px;
            resize: vertical;
            line-height: 1.6;
        }

        .file-upload-box {
            border: 2px dashed var(--myads-surface-border, #cbd5e1);
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            background: rgba(97, 93, 250, 0.02);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-upload-box:hover {
            border-color: var(--myads-primary, #615dfa);
            background: rgba(97, 93, 250, 0.05);
        }

        .btn-submit-ticket {
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

        .btn-submit-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.5);
        }

        /* Tips Sidebar */
        .ticket-sidebar-box {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.06) 0%, rgba(35, 210, 226, 0.06) 100%);
            border: 1px solid var(--myads-surface-border, rgba(97, 93, 250, 0.2));
            border-radius: var(--myads-radius-xl, 20px);
            padding: 26px 22px;
        }

        .ticket-sidebar-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 16px 0;
        }

        .ticket-sidebar-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .ticket-sidebar-list li {
            font-size: 0.88rem;
            color: var(--myads-text-muted, #475569);
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .ticket-sidebar-list li i {
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
            .ticket-create-layout { grid-template-columns: 1fr; }
            .ticket-form-grid-2 { grid-template-columns: 1fr; }
        }
    </style>

    <!-- Breadcrumb -->
    <div class="crumbs">
        <a href="{{ route('tickets.index') }}"><i class="fa-solid fa-ticket"></i> {{ __('support.my_tickets') }}</a>
        <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}" style="font-size: 0.75rem;"></i>
        <span>{{ __('support.open_new_ticket') }}</span>
    </div>

    <div class="ticket-create-layout">
        <div class="ticket-form-card">
            <div class="ticket-form-header">
                <h1 class="ticket-form-title">
                    <i class="fa-solid fa-plus-circle" style="color: var(--myads-primary, #615dfa);"></i>
                    {{ __('support.open_new_ticket') }}
                </h1>
                <p class="ticket-form-desc">
                    {{ __('support.tickets_desc') }}
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

            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="ticket-form-grid-2">
                    <div>
                        <label class="ticket-form-label" for="department">
                            {{ __('support.department') }} <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="department" id="department" class="ticket-form-select" required>
                            @foreach($departments as $key => $label)
                                <option value="{{ $key }}" {{ old('department') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="ticket-form-label" for="priority">
                            {{ __('support.priority') }} <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="priority" id="priority" class="ticket-form-select" required>
                            @foreach($priorities as $key => $label)
                                <option value="{{ $key }}" {{ (old('priority', 'medium') == $key) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="ticket-form-group">
                    <label class="ticket-form-label" for="subject">
                        {{ __('support.subject') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text"
                           name="subject"
                           id="subject"
                           class="ticket-form-input"
                           placeholder="{{ __('support.subject_placeholder') }}"
                           value="{{ old('subject') }}"
                           required>
                </div>

                <div class="ticket-form-group">
                    <label class="ticket-form-label" for="message">
                        {{ __('support.message') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="message"
                              id="message"
                              class="ticket-form-textarea"
                              placeholder="{{ __('support.message_placeholder') }}"
                              required>{{ old('message') }}</textarea>
                </div>

                <div class="ticket-form-group">
                    <label class="ticket-form-label" for="attachment">
                        {{ __('support.attachment') }}
                    </label>
                    <input type="file" name="attachment" id="attachment" class="ticket-form-input" accept=".jpg,.jpeg,.png,.pdf,.zip">
                    <div style="font-size: 0.8rem; color: var(--myads-text-muted, #64748b); margin-top: 6px;">
                        {{ __('support.attachment_hint') }}
                    </div>
                </div>

                <button type="submit" class="btn-submit-ticket">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ __('support.submit_ticket') }}</span>
                </button>
            </form>
        </div>

        <!-- Sidebar Guidelines -->
        <div>
            <div class="ticket-sidebar-box">
                <h3 class="ticket-sidebar-title">
                    <i class="fa-solid fa-shield-check" style="color: #10b981;"></i>
                    {{ __('support.support_guidelines') }}
                </h3>
                <ul class="ticket-sidebar-list">
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.guide_dept_correct') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.guide_attach_screenshot') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.guide_instant_notification') }}</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('support.guide_response_time') }}</span>
                    </li>
                </ul>

                <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid rgba(97, 93, 250, 0.15); text-align: center;">
                    <p style="font-size: 0.85rem; color: var(--myads-text-muted, #64748b); margin-bottom: 12px;">
                        {{ __('support.looking_for_community') }}
                    </p>
                    <a href="{{ route('support.index') }}" style="color: var(--myads-primary, #615dfa); font-weight: 700; text-decoration: none; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-comments"></i>
                        <span>{{ __('support.browse_support_forum') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
