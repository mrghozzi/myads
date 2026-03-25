@extends('theme::layouts.admin')

@section('title', __('messages.widgets'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.widgets') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.widgets') }}</li>
        </ul>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.widgets') }}</h5>
    </div>
    <div class="card-body">
        <div class="row g-4 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">{{ __('messages.add') }} {{ __('messages.widgets') }}</label>
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
                </select>
            </div>
            <div class="col-lg-8">
                <div class="text-muted">{{ __('messages.drag_rows_to_reorder') ?? 'Drag rows to reorder' }}</div>
            </div>
        </div>
        <div id="widget_block" class="mt-4" data-form-url="{{ route('admin.widgets.form') }}"></div>
    </div>
</div>

<div class="card stretch stretch-full mt-4">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.widgets') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="widgetsTable">
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
                            <td>
                                <span class="widget-drag-handle me-2"><i class="feather-move"></i></span>
                                <span class="fw-semibold">#{{ $widget->id }}</span>
                            </td>
                            <td>{{ $widget->name }}</td>
                            <td>{{ $places[$widget->o_parent] ?? $widget->o_parent }}</td>
                            <td><span class="badge bg-soft-primary text-primary">{{ $widget->o_mode }}</span></td>
                            <td><span class="widget-order-value">{{ $widget->o_order }}</span></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-primary widget-edit-btn" data-url="{{ route('admin.widgets.form.edit', $widget->id) }}">
                                    {{ __('messages.edit') }}
                                </button>
                                <form action="{{ route('admin.widgets.delete', $widget->id) }}" method="POST" class="d-inline-block ms-2" data-widget-delete="true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ theme_asset('admin-duralux/vendors/js/jquery-ui.min.js') }}"></script>
<script>
    (function() {
        var widgetBlock = document.getElementById('widget_block');
        var widgetSelect = document.getElementById('widget_cat');
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        function loadWidgetForm(url) {
            if (!widgetBlock) {
                return;
            }
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(response) {
                if (!response.ok) {
                    throw new Error('failed');
                }
                return response.text();
            }).then(function(html) {
                widgetBlock.innerHTML = html;
            }).catch(function() {
                widgetBlock.innerHTML = '<div class="alert alert-danger">{{ __('messages.unable_to_load_widget_form') ?? 'Unable to load widget form.' }}</div>';
            });
        }
        if (widgetSelect) {
            widgetSelect.addEventListener('change', function() {
                var value = widgetSelect.value;
                if (!value) {
                    widgetBlock.innerHTML = '';
                    return;
                }
                var baseUrl = widgetBlock.getAttribute('data-form-url');
                loadWidgetForm(baseUrl + '?type=' + encodeURIComponent(value));
            });
        }
        document.addEventListener('click', function(event) {
            var editButton = event.target.closest('.widget-edit-btn');
            if (editButton) {
                event.preventDefault();
                loadWidgetForm(editButton.getAttribute('data-url'));
            }
            var closeButton = event.target.closest('[data-widget-close]');
            if (closeButton) {
                event.preventDefault();
                widgetBlock.innerHTML = '';
                if (widgetSelect) {
                    widgetSelect.value = '';
                }
            }
        });
        document.addEventListener('submit', function(event) {
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
                handle: '.widget-drag-handle',
                update: function() {
                    var order = [];
                    window.jQuery(tableBody).find('tr').each(function(index) {
                        var id = window.jQuery(this).data('id');
                        if (id) {
                            order.push(id);
                        }
                        window.jQuery(this).find('.widget-order-value').text(index);
                    });
                    window.jQuery.ajax({
                        url: tableBody.getAttribute('data-reorder-url'),
                        method: 'POST',
                        data: { order: order },
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                }
            });
        }
    })();
</script>
@endsection
