@extends('theme::layouts.admin')

@section('title', __('messages.widgets'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.widgets') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.style') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.widgets') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.manage_widgets') }} / {{ __('messages.drag_rows_to_reorder') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.widgets') }}</span>
                    <span class="admin-stat-value">{{ number_format($widgets->count()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.place') }}</span>
                    <span class="admin-stat-value">{{ number_format(count($places)) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.type') }}</span>
                    <span class="admin-stat-value">{{ number_format($widgets->pluck('o_mode')->unique()->count()) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card justify-content-between">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.add') }} {{ __('messages.widgets') }}</span>
                    <div class="admin-muted">{{ __('messages.drag_rows_to_reorder') }}</div>
                </div>
                <div class="admin-type-select">
                    <select id="widget_cat" class="form-select">
                        <option value="">{{ __('messages.select') ?? 'Select' }}</option>
                        <option value="widget_html">{{ __('messages.html_code') ?? 'Html code' }}</option>
                        <option value="widget_members">{{ __('messages.suggest_members') ?? 'Suggest Members' }}</option>
                        <option value="widget_stats_box">{{ __('messages.stats_box') ?? 'Stats Box' }}</option>
                        <option value="widget_forum_latest">{{ __('messages.latest_topic') ?? 'Latest Topics' }}</option>
                        <option value="widget_news_latest">{{ __('messages.latest_news') ?? 'Latest News' }}</option>
                        <option value="widget_points_leaderboard">{{ __('messages.points') ?? 'Points' }}</option>
                        <option value="widget_store_latest">{{ __('messages.latest_products') ?? 'Latest Products' }}</option>
                        <option value="widget_directory_latest">{{ __('messages.latest_sites') ?? 'Latest Sites' }}</option>
                        <option value="widget_orders_latest">{{ __('messages.latest_orders') ?? 'Latest Orders' }}</option>
                        <option value="widget_badges_showcase">{{ __('messages.badges') ?? 'Badges' }}</option>
                        <option value="widget_quests_daily">{{ __('messages.daily_quests') ?? 'Daily Quests' }}</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="admin-workspace-grid">
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.add') }} {{ __('messages.widgets') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.manage_widgets') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <div id="widget_block" data-form-url="{{ route('admin.widgets.form') }}">
                    <div class="admin-dropzone-empty">
                        <div class="admin-modal-icon is-primary mb-3">
                            <i class="feather-grid"></i>
                        </div>
                        <h4 class="mb-2">{{ __('messages.manage_widgets') }}</h4>
                        <p class="admin-muted mb-0">{{ __('messages.select') ?? 'Select' }} {{ __('messages.type') }}...</p>
                    </div>
                </div>
            </div>
        </section>

        <aside class="admin-section-stack">
            <section class="admin-note-card">
                <span class="admin-note-label">{{ __('messages.drag_rows_to_reorder') }}</span>
                <span class="admin-note-copy">{{ __('messages.widgets') }}</span>
                <ul class="admin-compact-list mt-3">
                    <li><i class="feather-move"></i><span>{{ __('messages.drag_rows_to_reorder') }}</span></li>
                    <li><i class="feather-layout"></i><span>{{ __('messages.place') }}</span></li>
                    <li><i class="feather-box"></i><span>{{ __('messages.type') }}</span></li>
                </ul>
            </section>
        </aside>
    </div>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.widgets') }}</span>
                <h2 class="admin-panel__title">{{ __('messages.widgets') }}</h2>
            </div>
            <div class="admin-chip-list">
                <span class="admin-chip"><i class="feather-layers"></i>{{ $widgets->count() }}</span>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify" id="widgetsTable">
                    <thead>
                        <tr>
                            <th style="width: 140px;">ID</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.place') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th style="width: 140px;">{{ __('messages.order') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="widgetsTableBody" data-reorder-url="{{ route('admin.widgets.reorder') }}">
                        @foreach($widgets as $widget)
                            <tr data-id="{{ $widget->id }}">
                                <td data-label="ID">
                                    <span class="admin-reorder-handle me-2"><i class="feather-move"></i></span>
                                    <strong>#{{ $widget->id }}</strong>
                                </td>
                                <td data-label="{{ __('messages.name') }}">{{ $widget->name }}</td>
                                <td data-label="{{ __('messages.place') }}">{{ $places[$widget->o_parent] ?? $widget->o_parent }}</td>
                                <td data-label="{{ __('messages.type') }}">
                                    <span class="badge bg-soft-primary text-primary">{{ $widget->o_mode }}</span>
                                </td>
                                <td data-label="{{ __('messages.order') }}"><span class="widget-order-value">{{ $widget->o_order }}</span></td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <div class="admin-action-cluster">
                                        <button type="button" class="btn btn-sm btn-primary widget-edit-btn" data-url="{{ route('admin.widgets.form.edit', $widget->id) }}">
                                            {{ __('messages.edit') }}
                                        </button>
                                        <form action="{{ route('admin.widgets.delete', $widget->id) }}" method="POST" data-widget-delete="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ theme_asset('admin-duralux/vendors/js/jquery-ui.min.js') }}"></script>
<script>
    (function () {
        var widgetBlock = document.getElementById('widget_block');
        var widgetSelect = document.getElementById('widget_cat');
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function emptyWidgetBlock() {
            if (!widgetBlock) {
                return;
            }

            widgetBlock.innerHTML = `
                <div class="admin-dropzone-empty">
                    <div class="admin-modal-icon is-primary mb-3">
                        <i class="feather-grid"></i>
                    </div>
                    <h4 class="mb-2">{{ __('messages.manage_widgets') }}</h4>
                    <p class="admin-muted mb-0">{{ __('messages.select') ?? 'Select' }} {{ __('messages.type') }}...</p>
                </div>
            `;
        }

        function loadWidgetForm(url) {
            if (!widgetBlock) {
                return;
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
            }).catch(function () {
                widgetBlock.innerHTML = '<div class="alert alert-danger">{{ __('messages.unable_to_load_widget_form') ?? 'Unable to load widget form.' }}</div>';
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
