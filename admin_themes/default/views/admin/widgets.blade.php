@extends('admin::layouts.admin')

@section('title', __('messages.widgets'))

@section('content')
<div class="admin-page superdesign-widgets-page">
    <!-- HERO SECTION -->
    <section class="admin-hero" style="background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.08) 100%); border-radius: 20px; padding: 32px 28px; margin-bottom: 28px; border: 1px solid rgba(97, 93, 250, 0.15); position: relative; overflow: hidden;">
        <!-- Background Glow Orbs -->
        <div style="position: absolute; top: -50px; right: -50px; width: 140px; height: 140px; background: rgba(97, 93, 250, 0.2); filter: blur(50px); border-radius: 50%; pointer-events: none;"></div>
        <div style="position: absolute; bottom: -50px; left: -50px; width: 140px; height: 140px; background: rgba(35, 210, 226, 0.2); filter: blur(50px); border-radius: 50%; pointer-events: none;"></div>

        <div class="admin-hero__content" style="position: relative; z-index: 1;">
            <ul class="admin-breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 12px; margin-bottom: 12px; padding: 0; list-style: none; opacity: 0.85;">
                <li><a href="{{ route('admin.index') }}" style="color: inherit; text-decoration: none;">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><i class="feather-chevron-left" style="font-size: 11px;"></i></li>
                <li style="color: #615dfa; font-weight: 700;">{{ __('messages.widgets') }}</li>
            </ul>
            <div class="admin-hero__eyebrow" style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%); color: #fff; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px; text-uppercase: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);">
                <i class="feather-layout" style="font-size: 12px;"></i>
                {{ __('messages.style') ?? 'التصميم والمظهر' }}
            </div>
            <h1 class="admin-hero__title" style="font-size: 26px; font-weight: 800; margin: 0 0 8px 0; background: linear-gradient(135deg, var(--admin-heading-color, #1e293b) 0%, #615dfa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                {{ __('messages.widgets') }}
            </h1>
            <p class="admin-hero__copy" style="font-size: 14px; opacity: 0.8; margin: 0 0 20px 0; max-width: 600px;">
                {{ __('messages.manage_widgets') }} &nbsp;•&nbsp; {{ __('messages.drag_rows_to_reorder') }}
            </p>

            <!-- STAT STRIP -->
            <div class="admin-stat-strip" style="display: flex; flex-wrap: wrap; gap: 16px;">
                <div class="admin-stat-card" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(97, 93, 250, 0.15); padding: 14px 20px; border-radius: 14px; min-width: 140px;">
                    <span class="admin-stat-label" style="font-size: 11px; font-weight: 700; color: #8f91ac; display: block; text-transform: uppercase;">{{ __('messages.widgets') }}</span>
                    <span class="admin-stat-value" style="font-size: 22px; font-weight: 800; color: #615dfa;">{{ number_format($widgets->count()) }}</span>
                </div>
                <div class="admin-stat-card" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(35, 210, 226, 0.2); padding: 14px 20px; border-radius: 14px; min-width: 140px;">
                    <span class="admin-stat-label" style="font-size: 11px; font-weight: 700; color: #8f91ac; display: block; text-transform: uppercase;">{{ __('messages.place') }}</span>
                    <span class="admin-stat-value" style="font-size: 22px; font-weight: 800; color: #23d2e2;">{{ number_format(count($places)) }}</span>
                </div>
                <div class="admin-stat-card" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(97, 93, 250, 0.15); padding: 14px 20px; border-radius: 14px; min-width: 140px;">
                    <span class="admin-stat-label" style="font-size: 11px; font-weight: 700; color: #8f91ac; display: block; text-transform: uppercase;">{{ __('messages.type') }}</span>
                    <span class="admin-stat-value" style="font-size: 22px; font-weight: 800; color: #4ff461;">{{ number_format($widgets->pluck('o_mode')->unique()->count()) }}</span>
                </div>
                @if(isset($selectedPlace) && $selectedPlace)
                    <div class="admin-stat-card highlight-place-card" style="background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.15) 100%); border: 1.5px solid #615dfa; padding: 14px 20px; border-radius: 14px; min-width: 180px;">
                        <span class="admin-stat-label" style="font-size: 11px; font-weight: 700; color: #615dfa; display: block; text-transform: uppercase;">🎯 {{ __('messages.target_location') ?? 'المكان المستهدف' }}</span>
                        <span class="admin-stat-value" style="font-size: 15px; font-weight: 800; color: #1e293b;">{{ $places[$selectedPlace] ?? ('#' . $selectedPlace) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- NOTIFICATIONS & ALERTS -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 14px; padding: 14px 20px; background: rgba(79, 244, 97, 0.12); border: 1px solid rgba(79, 244, 97, 0.3); color: #15803d;">
            <i class="feather-check-circle" style="font-size: 18px;"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 14px; padding: 14px 20px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #b91c1c;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TARGET PLACE ALERT BANNER IF REDIRECTED FROM FRONTEND -->
    @if(isset($selectedPlace) && $selectedPlace)
        <div class="alert alert-info mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="border-radius: 16px; background: linear-gradient(135deg, rgba(97, 93, 250, 0.12) 0%, rgba(35, 210, 226, 0.12) 100%); border: 1px dashed #615dfa; padding: 18px 24px; box-shadow: 0 6px 20px rgba(97, 93, 250, 0.08);">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #615dfa; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                    <i class="feather-map-pin"></i>
                </div>
                <div>
                    <h5 class="mb-1" style="font-size: 15px; font-weight: 700; color: #1e293b;">
                        {{ __('messages.target_location_prompt') ?? 'أهلاً بك! تم التوجيه لإضافة ودجت في المكان:' }}
                        <span style="color: #615dfa;">{{ $places[$selectedPlace] ?? ('#' . $selectedPlace) }}</span>
                    </h5>
                    <p class="mb-0" style="font-size: 13px; color: #64748b;">
                        اختر نوع الودجت المطلوب من القائمة أدناه أو اضغط إضافة، وسيتم تعيين هذا المكان تلقائياً.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.widgets') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 10px;">
                    <i class="feather-x me-1"></i> {{ __('messages.clear_filter') ?? 'عرض الكل' }}
                </a>
            </div>
        </div>
    @endif

    <!-- WORKSPACE GRID (FORM + QUICK TYPES) -->
    <div class="admin-workspace-grid mb-4" style="display: grid; grid-template-columns: 1fr; gap: 24px;">
        <section class="admin-panel" style="background: var(--admin-card-bg, #ffffff); border-radius: 18px; border: 1px solid rgba(97, 93, 250, 0.12); padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.03);">
            <div class="admin-panel__header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <span class="admin-panel__eyebrow" style="font-size: 11px; font-weight: 700; color: #615dfa; text-transform: uppercase;">{{ __('messages.add') }} {{ __('messages.widgets') }}</span>
                    <h2 class="admin-panel__title mb-0" style="font-size: 20px; font-weight: 800;">{{ __('messages.manage_widgets') }}</h2>
                </div>

                <!-- WIDGET TYPE SELECTOR DROPDOWN -->
                <div class="admin-type-select" style="min-width: 260px;">
                    <label class="form-label mb-1" style="font-size: 12px; font-weight: 600; color: #64748b;">{{ __('messages.select') }} {{ __('messages.type') }}:</label>
                    <select id="widget_cat" class="form-select admin-form-control" style="border-radius: 12px; font-weight: 600; border-color: rgba(97, 93, 250, 0.25);">
                        <option value="">-- {{ __('messages.select') ?? 'Select Widget Type' }} --</option>
                        <option value="widget_html">🧩 {{ __('messages.html_code') ?? 'Html code / Banner' }}</option>
                        <option value="widget_members">👥 {{ __('messages.suggest_members') ?? 'Suggest Members' }}</option>
                        <option value="widget_online_members">🟢 {{ __('messages.online_members') ?? 'Online Members' }}</option>
                        <option value="widget_recent_comments">💬 {{ __('messages.recent_comments') ?? 'Recent Comments' }}</option>
                        <option value="widget_stats_box">📊 {{ __('messages.stats_box') ?? 'Stats Box' }}</option>
                        <option value="widget_forum_latest">📌 {{ __('messages.latest_topic') ?? 'Latest Topics' }}</option>
                        <option value="widget_news_latest">📰 {{ __('messages.latest_news') ?? 'Latest News' }}</option>
                        <option value="widget_points_leaderboard">🏆 {{ __('messages.points') ?? 'Points Leaderboard' }}</option>
                        <option value="widget_store_latest">🛒 {{ __('messages.latest_products') ?? 'Latest Products' }}</option>
                        <option value="widget_directory_latest">🌐 {{ __('messages.latest_sites') ?? 'Latest Sites' }}</option>
                        <option value="widget_orders_latest">💼 {{ __('messages.latest_orders') ?? 'Latest Orders' }}</option>
                        <option value="widget_badges_showcase">🎖️ {{ __('messages.badges') ?? 'Badges Showcase' }}</option>
                        <option value="widget_quests_daily">🎯 {{ __('messages.daily_quests') ?? 'Daily Quests' }}</option>
                        <option value="widget_landing_footer">⚓ {{ __('messages.landing_footer') ?? 'Landing Footer' }}</option>
                    </select>
                </div>
            </div>

            <div class="admin-panel__body p-0">
                <!-- FORM CONTAINER TARGET -->
                <div id="widget_block" data-form-url="{{ route('admin.widgets.form') }}" data-selected-place="{{ $selectedPlace ?? '' }}">
                    <div class="admin-dropzone-empty" style="border: 2px dashed rgba(97, 93, 250, 0.25); border-radius: 16px; padding: 40px 20px; text-align: center; background: linear-gradient(135deg, rgba(97, 93, 250, 0.02) 0%, rgba(35, 210, 226, 0.03) 100%);">
                        <div class="admin-modal-icon is-primary mb-3" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(97, 93, 250, 0.12); color: #615dfa; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                            <i class="feather-grid"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 16px; font-weight: 700;">{{ __('messages.manage_widgets') }}</h4>
                        <p class="admin-muted mb-0" style="font-size: 13px; color: #64748b;">
                            {{ __('messages.select') ?? 'اختر نوع الودجت من القائمة أعلاه للبدء في إنشائه وتخصيصه' }}...
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- LOCATION FILTER TABS & TABLE PANEL -->
    <section class="admin-panel" style="background: var(--admin-card-bg, #ffffff); border-radius: 18px; border: 1px solid rgba(97, 93, 250, 0.12); padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.03);">
        <div class="admin-panel__header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <span class="admin-panel__eyebrow" style="font-size: 11px; font-weight: 700; color: #615dfa; text-transform: uppercase;">{{ __('messages.widgets') }}</span>
                <h2 class="admin-panel__title mb-0" style="font-size: 20px; font-weight: 800;">{{ __('messages.widgets') }}</h2>
            </div>
            <div class="admin-chip-list">
                <span class="admin-chip" style="background: rgba(97, 93, 250, 0.12); color: #615dfa; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="feather-layers"></i> {{ $widgets->count() }} {{ __('messages.widgets') }}
                </span>
            </div>
        </div>

        <!-- PLACE FILTER CHIPS -->
        <div class="place-filter-bar mb-4 pb-3 border-bottom border-light-subtle d-flex align-items-center gap-2 flex-wrap">
            <span style="font-size: 12px; font-weight: 700; color: #64748b; margin-left: 6px;"><i class="feather-filter me-1"></i> تصفية حسب المكان:</span>
            <button type="button" class="btn btn-sm place-filter-btn {{ !isset($selectedPlace) || !$selectedPlace ? 'active btn-primary' : 'btn-light border' }}" data-place="" style="border-radius: 20px; font-size: 12px; font-weight: 600;">
                الكل (All)
            </button>
            @foreach($places as $id => $name)
                <button type="button" class="btn btn-sm place-filter-btn {{ isset($selectedPlace) && (string)$selectedPlace === (string)$id ? 'active btn-primary' : 'btn-light border' }}" data-place="{{ $id }}" style="border-radius: 20px; font-size: 12px; font-weight: 600;">
                    {{ $name }}
                </button>
            @endforeach
        </div>

        <!-- WIDGETS TABLE -->
        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap" style="overflow-x: auto;">
                <table class="table table-hover align-middle admin-table" id="widgetsTable" style="margin-bottom: 0;">
                    <thead style="background: rgba(97, 93, 250, 0.04); border-bottom: 2px solid rgba(97, 93, 250, 0.1);">
                        <tr>
                            <th style="width: 140px; padding: 14px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase;">ID</th>
                            <th style="padding: 14px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('messages.name') }}</th>
                            <th style="padding: 14px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('messages.place') }}</th>
                            <th style="padding: 14px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('messages.type') }}</th>
                            <th style="width: 140px; padding: 14px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('messages.order') }}</th>
                            <th class="text-end" style="padding: 14px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="widgetsTableBody" data-reorder-url="{{ route('admin.widgets.reorder') }}">
                        @forelse($widgets as $widget)
                            <tr data-id="{{ $widget->id }}" data-place="{{ $widget->o_parent }}" style="transition: background 0.2s ease;">
                                <td data-label="ID" style="padding: 16px 18px;">
                                    <span class="admin-reorder-handle me-2" title="{{ __('messages.drag_rows_to_reorder') }}" style="cursor: grab; color: #8f91ac;"><i class="feather-move"></i></span>
                                    <strong>#{{ $widget->id }}</strong>
                                </td>
                                <td data-label="{{ __('messages.name') }}" style="padding: 16px 18px; font-weight: 700;">
                                    {{ $widget->name }}
                                </td>
                                <td data-label="{{ __('messages.place') }}" style="padding: 16px 18px;">
                                    <span class="badge bg-light text-dark border" style="font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 8px;">
                                        <i class="feather-map-pin me-1" style="color: #23d2e2;"></i>
                                        {{ $places[$widget->o_parent] ?? $widget->o_parent }}
                                    </span>
                                </td>
                                <td data-label="{{ __('messages.type') }}" style="padding: 16px 18px;">
                                    <span class="badge" style="background: rgba(97, 93, 250, 0.12); color: #615dfa; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 8px;">
                                        {{ $widget->o_mode }}
                                    </span>
                                </td>
                                <td data-label="{{ __('messages.order') }}" style="padding: 16px 18px;">
                                    <span class="widget-order-value badge bg-secondary-subtle text-secondary" style="font-size: 12px; padding: 6px 12px; border-radius: 8px;">{{ $widget->o_order }}</span>
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end" style="padding: 16px 18px;">
                                    <div class="admin-action-cluster d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-primary widget-edit-btn" data-url="{{ route('admin.widgets.form.edit', $widget->id) }}" style="border-radius: 8px; font-weight: 600; background: #615dfa; border: none;">
                                            <i class="feather-edit-2 me-1"></i> {{ __('messages.edit') }}
                                        </button>
                                        <form action="{{ route('admin.widgets.delete', $widget->id) }}" method="POST" data-widget-delete="true" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-weight: 600;">
                                                <i class="feather-trash-2 me-1"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="feather-inbox mb-2" style="font-size: 32px; display: block; opacity: 0.5;"></i>
                                    {{ __('messages.no_results') ?? 'لا توجد ودجات مضافة حالياً.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ admin_asset('admin-duralux/vendors/js/jquery-ui.min.js') }}"></script>
<script>
    (function () {
        var widgetBlock = document.getElementById('widget_block');
        var widgetSelect = document.getElementById('widget_cat');
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        var selectedPlace = widgetBlock ? widgetBlock.getAttribute('data-selected-place') : '';

        function emptyWidgetBlock() {
            if (!widgetBlock) {
                return;
            }

            widgetBlock.innerHTML = `
                <div class="admin-dropzone-empty" style="border: 2px dashed rgba(97, 93, 250, 0.25); border-radius: 16px; padding: 40px 20px; text-align: center; background: linear-gradient(135deg, rgba(97, 93, 250, 0.02) 0%, rgba(35, 210, 226, 0.03) 100%);">
                    <div class="admin-modal-icon is-primary mb-3" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(97, 93, 250, 0.12); color: #615dfa; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                        <i class="feather-grid"></i>
                    </div>
                    <h4 class="mb-2" style="font-size: 16px; font-weight: 700;">{{ __('messages.manage_widgets') }}</h4>
                    <p class="admin-muted mb-0" style="font-size: 13px; color: #64748b;">
                        {{ __('messages.select') ?? 'اختر نوع الودجت من القائمة أعلاه للبدء في إنشائه وتخصيصه' }}...
                    </p>
                </div>
            `;
        }

        function loadWidgetForm(url) {
            if (!widgetBlock) {
                return;
            }

            // Append selected place parameter if available and not already in URL
            if (selectedPlace && url.indexOf('place=') === -1) {
                url += (url.indexOf('?') === -1 ? '?' : '&') + 'place=' + encodeURIComponent(selectedPlace);
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('failed');
                }
                return response.text();
            }).then(function (html) {
                widgetBlock.innerHTML = html;
                widgetBlock.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }).catch(function () {
                widgetBlock.innerHTML = '<div class="alert alert-danger border-0 shadow-sm" style="border-radius: 12px;">{{ __('messages.unable_to_load_widget_form') ?? 'Unable to load widget form.' }}</div>';
            });
        }

        if (widgetSelect) {
            widgetSelect.addEventListener('change', function () {
                var value = widgetSelect.value;
                if (!value) {
                    emptyWidgetBlock();
                    return;
                }

                var baseUrl = widgetBlock.getAttribute('data-form-url');
                loadWidgetForm(baseUrl + '?type=' + encodeURIComponent(value));
            });
        }

        // Handle Place Filtering Tabs
        document.querySelectorAll('.place-filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.place-filter-btn').forEach(function (b) {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-light', 'border');
                });
                btn.classList.remove('btn-light', 'border');
                btn.classList.add('active', 'btn-primary');

                var placeId = btn.getAttribute('data-place');
                selectedPlace = placeId;

                var rows = document.querySelectorAll('#widgetsTableBody tr');
                rows.forEach(function (row) {
                    var rPlace = row.getAttribute('data-place');
                    if (!placeId || rPlace === placeId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Trigger filter if selectedPlace was passed from URL
        if (selectedPlace) {
            var activeBtn = document.querySelector('.place-filter-btn[data-place="' + selectedPlace + '"]');
            if (activeBtn) {
                activeBtn.click();
            }
        }

        document.addEventListener('click', function (event) {
            var editButton = event.target.closest('.widget-edit-btn');
            if (editButton) {
                event.preventDefault();
                loadWidgetForm(editButton.getAttribute('data-url'));
            }

            var closeButton = event.target.closest('[data-widget-close]');
            if (closeButton) {
                event.preventDefault();
                emptyWidgetBlock();
                if (widgetSelect) {
                    widgetSelect.value = '';
                }
            }
        });

        document.addEventListener('submit', function (event) {
            var deleteForm = event.target.closest('[data-widget-delete]');
            if (deleteForm) {
                var confirmed = confirm('{{ __('messages.confirm_delete_widget') ?? 'Are you sure you want to delete this widget?' }}');
                if (!confirmed) {
                    event.preventDefault();
                }
            }
        });

        // Table Row Drag & Drop Reordering
        var tableBody = document.getElementById('widgetsTableBody');
        if (tableBody && window.jQuery && window.jQuery.fn.sortable) {
            window.jQuery(tableBody).sortable({
                handle: '.admin-reorder-handle',
                update: function () {
                    var order = [];

                    window.jQuery(tableBody).find('tr').each(function (index) {
                        var id = window.jQuery(this).data('id');
                        if (id) {
                            order.push(id);
                        }
                        window.jQuery(this).find('.widget-order-value').text(index);
                    });

                    window.jQuery.ajax({
                        url: tableBody.getAttribute('data-reorder-url'),
                        method: 'POST',
                        data: {order: order},
                        headers: {'X-CSRF-TOKEN': csrfToken}
                    });
                }
            });
        }
    })();
</script>
@endpush
