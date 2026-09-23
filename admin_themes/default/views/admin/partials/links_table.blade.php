<div class="table-responsive">
    <table class="table table-hover align-middle mb-0 admin-table admin-table-cardify">
        <thead>
            <tr>
                <th class="wd-40 text-center">
                    <div class="form-check form-check-md d-inline-block">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th>ID</th>
                <th>{{ __('messages.user') }}</th>
                <th>{{ __('messages.textads') }}</th>
                <th>{{ __('messages.clicks') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th class="text-end">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="linksTableBody">
            @forelse($links as $link)
                <tr id="linkRow{{ $link->id }}">
                    <td data-label="#" class="text-center">
                        <div class="form-check form-check-md d-inline-block">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $link->id }}">
                        </div>
                    </td>
                    <td data-label="ID">
                        <span class="badge bg-soft-secondary text-dark fw-bold">#{{ $link->id }}</span>
                    </td>
                    <td data-label="{{ __('messages.user') }}">
                        @if($link->user)
                            <a href="{{ route('profile.show', $link->user->username) }}" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                                <div class="avatar-image avatar-sm rounded-circle overflow-hidden" style="width: 28px; height: 28px;">
                                    <img src="{{ $link->user->img ? asset($link->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                </div>
                                <span class="fw-semibold text-dark small">{{ $link->user->username }}</span>
                            </a>
                        @else
                            <span class="badge bg-soft-secondary text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.textads') }}" style="max-width: 320px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold text-dark text-truncate link-title-{{ $link->id }}" title="{{ $link->name }}">
                                {{ $link->name }}
                            </span>
                            @if($link->name_b || $link->txt_b)
                                <span class="badge bg-info text-white" style="font-size: 9px; padding: 2px 4px;">A/B</span>
                            @endif
                        </div>
                        <div class="small text-muted text-truncate mb-1 link-txt-{{ $link->id }}" title="{{ $link->txt }}">
                            {{ $link->txt }}
                        </div>
                        <div class="small text-truncate link-url-{{ $link->id }}">
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none">
                                <i class="feather-external-link me-1" style="font-size: 11px;"></i>{{ $link->url }}
                            </a>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.clicks') }}">
                        <a href="{{ route('admin.stats', ['ty' => 'clik']) }}" class="badge bg-soft-primary text-primary text-decoration-none" title="{{ __('messages.Stats') }}">
                            <i class="feather-mouse-pointer me-1"></i>{{ number_format($link->clik) }}
                        </a>
                        @if($link->name_b || $link->txt_b)
                            <div class="mt-1 small text-muted" style="font-size: 11px;">
                                <span class="text-success">A: {{ number_format($link->clik_a) }}</span> | 
                                <span class="text-info">B: {{ number_format($link->clik_b) }}</span>
                            </div>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.status') }}">
                        <div class="form-check form-switch d-inline-block m-0">
                            <input 
                                class="form-check-input status-toggle-btn" 
                                type="checkbox" 
                                role="switch"
                                id="linkStatusSwitch{{ $link->id }}" 
                                data-id="{{ $link->id }}"
                                {{ $link->statu == 1 ? 'checked' : '' }}
                            >
                            <label class="form-check-label ms-1 small fw-semibold link-status-label-{{ $link->id }} {{ $link->statu == 1 ? 'text-success' : 'text-muted' }}" for="linkStatusSwitch{{ $link->id }}">
                                {{ $link->statu == 1 ? __('messages.active') : __('messages.inactive') }}
                            </label>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <button 
                                type="button" 
                                class="btn btn-sm btn-icon btn-light-brand edit-link-btn" 
                                data-id="{{ $link->id }}"
                                data-name="{{ $link->name }}"
                                data-name_b="{{ $link->name_b }}"
                                data-url="{{ $link->url }}"
                                data-txt="{{ $link->txt }}"
                                data-txt_b="{{ $link->txt_b }}"
                                data-statu="{{ $link->statu }}"
                                data-countries="{{ implode(', ', $link->targetCountries()) }}"
                                data-devices="{{ json_encode($link->targetDevices()) }}"
                                title="{{ __('messages.edit') }}"
                            >
                                <i class="feather-edit-3"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger delete-link-btn" data-id="{{ $link->id }}" data-name="{{ $link->name }}" title="{{ __('messages.delete') }}">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3">
                            <i class="feather-file-text"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('messages.no_results_found') ?? 'No text ads found' }}</h6>
                        <p class="text-muted small mb-0">{{ __('messages.try_adjusting_filters') ?? 'Try changing search criteria or filters.' }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($links->hasPages())
    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            {{ __('messages.showing') ?? 'Showing' }} {{ $links->firstItem() ?? 0 }} - {{ $links->lastItem() ?? 0 }} {{ __('messages.of') ?? 'of' }} {{ $links->total() }}
        </div>
        <div class="pagination-wrapper">
            {{ $links->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
