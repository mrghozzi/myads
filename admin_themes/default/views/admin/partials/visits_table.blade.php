@php
    $durationLabels = [
        1 => '10s (1 ' . __('messages.point') . ')',
        2 => '20s (2 ' . __('messages.points') . ')',
        3 => '30s (5 ' . __('messages.points') . ')',
        4 => '60s (10 ' . __('messages.points') . ')',
    ];
@endphp

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
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.views') }}</th>
                <th>{{ __('messages.duration') ?? 'Duration' }}</th>
                <th>{{ __('messages.date') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th class="text-end">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="visitsTableBody">
            @forelse($visits as $visit)
                <tr id="visitRow{{ $visit->id }}">
                    <td data-label="#" class="text-center">
                        <div class="form-check form-check-md d-inline-block">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $visit->id }}">
                        </div>
                    </td>
                    <td data-label="ID">
                        <span class="badge bg-soft-secondary text-dark fw-bold">#{{ $visit->id }}</span>
                    </td>
                    <td data-label="{{ __('messages.user') }}">
                        @if($visit->user)
                            <a href="{{ route('profile.show', $visit->user->username) }}" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                                <div class="avatar-image avatar-sm rounded-circle overflow-hidden" style="width: 28px; height: 28px;">
                                    <img src="{{ $visit->user->img ? asset($visit->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                </div>
                                <span class="fw-semibold text-dark small">{{ $visit->user->username }}</span>
                            </a>
                        @else
                            <span class="badge bg-soft-secondary text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.name') }}" style="max-width: 280px;">
                        <div class="fw-bold text-dark text-truncate visit-name-{{ $visit->id }}" title="{{ $visit->name }}">
                            {{ $visit->name }}
                        </div>
                        <div class="small text-truncate visit-url-{{ $visit->id }}">
                            <a href="{{ $visit->url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none">
                                <i class="feather-external-link me-1" style="font-size: 11px;"></i>{{ $visit->url }}
                            </a>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.views') }}">
                        <span class="badge bg-soft-primary text-primary fs-7">
                            <i class="feather-eye me-1"></i>{{ number_format($visit->vu) }}
                        </span>
                    </td>
                    <td data-label="{{ __('messages.duration') ?? 'Duration' }}">
                        <span class="badge bg-light text-dark border visit-duration-{{ $visit->id }}">
                            <i class="feather-clock me-1 text-muted"></i>{{ $durationLabels[$visit->tims] ?? ($visit->tims . 's') }}
                        </span>
                    </td>
                    <td data-label="{{ __('messages.date') }}">
                        <span class="small text-muted">
                            {{ date('Y-m-d H:i', $visit->tims > 1000000000 ? $visit->tims : ($visit->created_at ? $visit->created_at->timestamp : time())) }}
                        </span>
                    </td>
                    <td data-label="{{ __('messages.status') }}">
                        <div class="form-check form-switch d-inline-block m-0">
                            <input 
                                class="form-check-input status-toggle-btn" 
                                type="checkbox" 
                                role="switch"
                                id="visitStatusSwitch{{ $visit->id }}" 
                                data-id="{{ $visit->id }}"
                                {{ $visit->statu == 1 ? 'checked' : '' }}
                            >
                            <label class="form-check-label ms-1 small fw-semibold visit-status-label-{{ $visit->id }} {{ $visit->statu == 1 ? 'text-success' : 'text-muted' }}" for="visitStatusSwitch{{ $visit->id }}">
                                {{ $visit->statu == 1 ? __('messages.active') : __('messages.inactive') }}
                            </label>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <button 
                                type="button" 
                                class="btn btn-sm btn-icon btn-light-brand edit-visit-btn" 
                                data-id="{{ $visit->id }}"
                                data-name="{{ $visit->name }}"
                                data-url="{{ $visit->url }}"
                                data-tims="{{ $visit->tims }}"
                                data-statu="{{ $visit->statu }}"
                                title="{{ __('messages.edit') }}"
                            >
                                <i class="feather-edit-3"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger delete-visit-btn" data-id="{{ $visit->id }}" data-name="{{ $visit->name }}" title="{{ __('messages.delete') }}">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3">
                            <i class="feather-activity"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('messages.no_results_found') ?? 'No traffic campaigns found' }}</h6>
                        <p class="text-muted small mb-0">{{ __('messages.try_adjusting_filters') ?? 'Try changing search criteria or filters.' }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($visits->hasPages())
    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            {{ __('messages.showing') ?? 'Showing' }} {{ $visits->firstItem() ?? 0 }} - {{ $visits->lastItem() ?? 0 }} {{ __('messages.of') ?? 'of' }} {{ $visits->total() }}
        </div>
        <div class="pagination-wrapper">
            {{ $visits->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
