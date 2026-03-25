@extends('theme::layouts.admin')

@section('title', __('messages.forum_categories'))

@section('content')
<!-- Header -->
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.forum_categories') }}</h2>
        <p class="text-muted">{{ __('messages.forum_categories_desc') ?? 'Manage your forum categories here.' }}</p>
        <div class="mt-4">
             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="feather-plus me-2"></i> {{ __('messages.add_category') }}
            </button>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.icon') }}</th>
                            <th>{{ __('messages.order') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $category->name }}</div>
                                @if($category->txt)
                                <small class="text-muted">{{ Str::limit($category->txt, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <i class="fa {{ $category->icons }} fs-4"></i>
                                <span class="ms-2 text-muted small">{{ $category->icons }}</span>
                            </td>
                            <td>{{ $category->ordercat }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                    <i class="feather-edit-3"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.new_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.forum_categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 icon-picker-field">
                            @include('theme::admin.partials.icon_picker')
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.order') }}</label>
                            <input type="number" name="ordercat" class="form-control" value="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($categories as $category)
<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.forum_categories.update', $category->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control" rows="3">{{ $category->txt }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 icon-picker-field">
                            @include('theme::admin.partials.icon_picker', ['selectedIcon' => $category->icons])
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.order') }}</label>
                            <input type="number" name="ordercat" class="form-control" value="{{ $category->ordercat }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.confirm_delete_category') }}</h4>
                <p class="text-muted">{{ $category->name }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.forum_categories.delete', $category->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var pickers = Array.prototype.slice.call(document.querySelectorAll('[data-icon-picker]'));

    if (!pickers.length) {
        return;
    }

    function slugFromValue(value) {
        if (!value) {
            return '';
        }

        var parts = value.trim().split(/\s+/);
        var lastPart = parts[parts.length - 1] || '';

        return lastPart.replace(/^fa-/, '');
    }

    function optionPayload(option) {
        return {
            value: option.value,
            label: (option.textContent || '').trim(),
            iconClass: option.dataset.icon || option.value,
        };
    }

    function collectGroups(select, fallbackLabel) {
        var groups = [];
        var groupedValues = new Set();
        var fallbackOptions = [];

        Array.prototype.forEach.call(select.children, function (child) {
            if (child.tagName === 'OPTGROUP') {
                var options = Array.prototype.slice.call(child.querySelectorAll('option')).filter(function (option) {
                    return option.value;
                }).map(function (option) {
                    groupedValues.add(option.value);
                    return optionPayload(option);
                });

                if (options.length) {
                    groups.push({
                        label: child.label,
                        options: options,
                    });
                }
            } else if (child.tagName === 'OPTION' && child.value) {
                fallbackOptions.push(optionPayload(child));
            }
        });

        fallbackOptions.forEach(function (option) {
            if (!groupedValues.has(option.value)) {
                groups.unshift({
                    label: fallbackLabel,
                    options: [option],
                });
            }
        });

        return groups;
    }

    function syncPreview(picker) {
        var select = picker.querySelector('.icon-picker__native');
        var trigger = picker.querySelector('.icon-picker__trigger');
        var badgeIcon = picker.querySelector('.icon-picker__trigger-badge i');
        var valueNode = picker.querySelector('.icon-picker__trigger-value');
        var placeholder = picker.dataset.placeholder || 'Select Icon';
        var selectedOption = select.options[select.selectedIndex];
        var value = selectedOption && selectedOption.value ? selectedOption.value : '';
        var label = value ? ((selectedOption.textContent || '').trim() || slugFromValue(value)) : placeholder;
        var iconClass = value ? (selectedOption.dataset.icon || value) : 'fa-solid fa-icons';

        badgeIcon.className = iconClass;
        valueNode.textContent = label;
        trigger.classList.toggle('is-empty', !value);
    }

    function closePicker(picker) {
        var trigger = picker.querySelector('.icon-picker__trigger');
        var panel = picker.querySelector('.icon-picker__panel');
        var search = picker.querySelector('.icon-picker__search');

        picker.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.hidden = true;
        search.value = '';
        renderOptions(picker, '');
    }

    function closeOtherPickers(currentPicker) {
        pickers.forEach(function (picker) {
            if (picker !== currentPicker) {
                closePicker(picker);
            }
        });
    }

    function openPicker(picker) {
        closeOtherPickers(picker);

        var trigger = picker.querySelector('.icon-picker__trigger');
        var panel = picker.querySelector('.icon-picker__panel');
        var search = picker.querySelector('.icon-picker__search');

        picker.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        panel.hidden = false;
        renderOptions(picker, search.value);

        window.requestAnimationFrame(function () {
            search.focus();
        });
    }

    function renderOptions(picker, query) {
        var results = picker.querySelector('[data-icon-picker-results]');
        var empty = picker.querySelector('.icon-picker__empty');
        var select = picker.querySelector('.icon-picker__native');
        var normalizedQuery = (query || '').trim().toLowerCase();
        var visibleCount = 0;

        results.innerHTML = '';

        picker._iconGroups.forEach(function (group) {
            var filteredOptions = group.options.filter(function (option) {
                if (!normalizedQuery) {
                    return true;
                }

                return option.label.toLowerCase().indexOf(normalizedQuery) !== -1 ||
                    option.value.toLowerCase().indexOf(normalizedQuery) !== -1;
            });

            if (!filteredOptions.length) {
                return;
            }

            visibleCount += filteredOptions.length;

            var section = document.createElement('section');
            section.className = 'icon-picker__group';

            if (group.label) {
                var title = document.createElement('p');
                title.className = 'icon-picker__group-title';
                title.textContent = group.label;
                section.appendChild(title);
            }

            var list = document.createElement('div');
            list.className = 'icon-picker__group-options';

            filteredOptions.forEach(function (option) {
                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'icon-picker__option';
                button.dataset.value = option.value;

                if (select.value === option.value) {
                    button.classList.add('is-selected');
                }

                button.innerHTML =
                    '<span class="icon-picker__option-badge"><i class="' + option.iconClass + '"></i></span>' +
                    '<span class="icon-picker__option-copy">' +
                        '<span class="icon-picker__option-label">' + option.label + '</span>' +
                        '<span class="icon-picker__option-meta">' + option.value + '</span>' +
                    '</span>' +
                    '<span class="icon-picker__option-check" aria-hidden="true"><i class="fa-solid fa-check"></i></span>';

                list.appendChild(button);
            });

            section.appendChild(list);
            results.appendChild(section);
        });

        empty.hidden = visibleCount !== 0;
        results.hidden = visibleCount === 0;
    }

    pickers.forEach(function (picker) {
        var select = picker.querySelector('.icon-picker__native');
        var trigger = picker.querySelector('.icon-picker__trigger');
        var search = picker.querySelector('.icon-picker__search');
        var empty = picker.querySelector('.icon-picker__empty');

        picker._iconGroups = collectGroups(select, picker.dataset.currentGroup || 'Current Selection');
        search.placeholder = picker.dataset.searchPlaceholder || 'Search icons...';
        empty.textContent = picker.dataset.noResults || 'No icons found';

        syncPreview(picker);
        renderOptions(picker, '');
        picker.classList.add('is-ready');

        trigger.addEventListener('click', function () {
            if (picker.classList.contains('is-open')) {
                closePicker(picker);
                return;
            }

            openPicker(picker);
        });

        search.addEventListener('input', function () {
            renderOptions(picker, search.value);
        });

        select.addEventListener('change', function () {
            syncPreview(picker);
            renderOptions(picker, search.value);
        });

        picker.querySelector('[data-icon-picker-results]').addEventListener('click', function (event) {
            var optionButton = event.target.closest('.icon-picker__option');

            if (!optionButton) {
                return;
            }

            select.value = optionButton.dataset.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            closePicker(picker);
        });
    });

    document.addEventListener('click', function (event) {
        pickers.forEach(function (picker) {
            if (!picker.contains(event.target)) {
                closePicker(picker);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            pickers.forEach(closePicker);
        }
    });

    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            modal.querySelectorAll('[data-icon-picker]').forEach(function (picker) {
                closePicker(picker);
            });
        });
    });
});
</script>
<style>
    #addCategoryModal .modal-body,
    [id^="editCategoryModal"] .modal-body {
        overflow: visible;
    }
    .icon-picker-field {
        position: relative;
    }
    .icon-picker__helper {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .02em;
        color: #91a1b6;
    }
    .icon-picker__native {
        width: 100%;
    }
    .icon-picker__trigger,
    .icon-picker__panel {
        display: none;
    }
    .icon-picker.is-ready .icon-picker__native {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        clip-path: inset(50%);
        border: 0;
        white-space: nowrap;
    }
    .icon-picker.is-ready .icon-picker__trigger {
        width: 100%;
        min-height: 74px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        border: 1px solid #d9e2ef;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 12px 24px rgba(40, 60, 80, 0.06);
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        appearance: none;
        cursor: pointer;
        font: inherit;
        line-height: 1.3;
        text-align: start;
    }
    .icon-picker.is-ready .icon-picker__trigger:hover,
    .icon-picker.is-open .icon-picker__trigger {
        border-color: #3454d1;
        box-shadow: 0 0 0 4px rgba(52, 84, 209, 0.12);
    }
    .icon-picker__trigger-badge {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        border: 1px solid #dbe4ff;
        color: #3454d1;
        flex-shrink: 0;
        font-size: 18px;
    }
    .icon-picker__trigger.is-empty .icon-picker__trigger-badge,
    .icon-picker__trigger.is-empty .icon-picker__trigger-badge i,
    .icon-picker__trigger.is-empty .icon-picker__trigger-value {
        color: #91a1b6;
    }
    .icon-picker__trigger-content {
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
        text-align: start;
        flex: 1 1 auto;
    }
    .icon-picker__trigger-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #91a1b6;
        font-weight: 700;
    }
    .icon-picker__trigger-value {
        color: #283c50;
        font-size: 18px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .icon-picker__trigger-arrow {
        color: #91a1b6;
        font-size: 14px;
        flex-shrink: 0;
        transition: transform .2s ease;
    }
    .icon-picker.is-open .icon-picker__trigger-arrow {
        transform: rotate(180deg);
    }
    .icon-picker.is-ready .icon-picker__panel {
        margin-top: 12px;
        display: block;
        border: 1px solid #d9e2ef;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 22px 48px rgba(40, 60, 80, 0.16);
    }
    .icon-picker__search-wrap {
        position: relative;
        padding: 14px 14px 10px;
        border-bottom: 1px solid #eef1f6;
        background: #fff;
        border-radius: 18px 18px 0 0;
    }
    .icon-picker__search-icon {
        position: absolute;
        top: 50%;
        left: 30px;
        transform: translateY(-50%);
        color: #91a1b6;
        font-size: 13px;
    }
    .icon-picker__search {
        width: 100% !important;
        max-width: 100%;
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid #d9e2ef;
        background: #f8fafc;
        padding: 10px 14px 10px 40px;
        color: #283c50;
        font-size: 13px;
        box-sizing: border-box;
    }
    .icon-picker__search:focus {
        outline: none;
        border-color: #3454d1;
        box-shadow: 0 0 0 4px rgba(52, 84, 209, 0.12);
    }
    .icon-picker__results {
        max-height: 320px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 8px 10px 12px;
        background: #fff;
        border-radius: 0 0 18px 18px;
    }
    .icon-picker__empty {
        padding: 18px;
        color: #91a1b6;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
    }
    .icon-picker__group + .icon-picker__group {
        margin-top: 8px;
    }
    .icon-picker__group-title {
        margin: 0;
        padding: 8px 8px 6px;
        color: #91a1b6;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .icon-picker__group-options {
        display: grid;
        gap: 6px;
    }
    .icon-picker__option {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid transparent;
        border-radius: 14px;
        background: #fff;
        transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
        text-align: start;
        appearance: none;
        cursor: pointer;
        font: inherit;
        line-height: 1.3;
    }
    .icon-picker__option:hover {
        background: #f8faff;
        border-color: #dbe4ff;
    }
    .icon-picker__option.is-selected {
        background: #eef3ff;
        border-color: #c8d6ff;
        box-shadow: inset 0 0 0 1px rgba(52, 84, 209, 0.08);
    }
    .icon-picker__option-badge {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #f8fafc;
        border: 1px solid #e7edf5;
        color: #283c50;
        font-size: 17px;
    }
    .icon-picker__option-copy {
        min-width: 0;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .icon-picker__option-label {
        color: #283c50;
        font-size: 14px;
        font-weight: 700;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .icon-picker__option-meta {
        color: #91a1b6;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .icon-picker__option-check {
        color: #3454d1;
        font-size: 14px;
        opacity: 0;
        transform: scale(.9);
        transition: opacity .2s ease, transform .2s ease;
    }
    .icon-picker__option.is-selected .icon-picker__option-check {
        opacity: 1;
        transform: scale(1);
    }
    @media (min-width: 768px) {
        .icon-picker {
            position: relative;
        }
        .icon-picker.is-ready .icon-picker__panel {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 12px);
            z-index: 1055;
        }
    }
    @media (max-width: 767.98px) {
        .icon-picker__option-meta {
            display: none;
        }
        .icon-picker.is-ready .icon-picker__panel {
            position: static;
        }
        .icon-picker__results {
            max-height: 260px;
        }
    }
    html[dir="rtl"] .icon-picker__search-icon {
        left: auto;
        right: 30px;
    }
    html[dir="rtl"] .icon-picker__search {
        padding: 10px 40px 10px 14px;
    }
</style>
@endpush
