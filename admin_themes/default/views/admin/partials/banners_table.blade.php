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
                <th>{{ __('messages.bannads') }}</th>
                <th>{{ __('messages.user') }}</th>
                <th>{{ __('messages.size') }}</th>
                <th>{{ __('messages.views') }}</th>
                <th>{{ __('messages.clicks') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th class="text-end">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="bannersTableBody">
            @forelse($banners as $banner)
                <tr id="bannerRow{{ $banner->id }}">
                    <td data-label="#" class="text-center">
                        <div class="form-check form-check-md d-inline-block">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $banner->id }}">
                        </div>
                    </td>
                    <td data-label="ID">
                        <span class="badge bg-soft-secondary text-dark fw-bold">#{{ $banner->id }}</span>
                    </td>
                    <td data-label="{{ __('messages.bannads') }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="admin-banner-thumb position-relative" style="width: 70px; height: 42px; border-radius: 8px; overflow: hidden; background: rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.08); flex-shrink: 0; cursor: pointer;" onclick="previewBannerImage('{{ $banner->img }}', '{{ addslashes($banner->name) }}', '{{ $banner->img_b }}')">
                                <img src="{{ $banner->img }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('themes/default/assets/images/placeholder.jpg') }}';">
                                @if($banner->img_b)
                                    <span class="badge bg-info text-white position-absolute top-0 end-0 m-1" style="font-size: 8px; padding: 2px 4px;">A/B</span>
                                @endif
                            </div>
                            <div style="min-width: 0;">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 240px;" title="{{ $banner->name }}">
                                    {{ $banner->name }}
                                </div>
                                <div class="small text-muted text-truncate" style="max-width: 240px;">
                                    <a href="{{ $banner->url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none">
                                        <i class="feather-external-link me-1" style="font-size: 11px;"></i>{{ $banner->url }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.user') }}">
                        @if($banner->user)
                            <a href="{{ route('profile.show', $banner->user->username) }}" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                                <div class="avatar-image avatar-sm rounded-circle overflow-hidden" style="width: 28px; height: 28px;">
                                    <img src="{{ $banner->user->img ? asset($banner->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                </div>
                                <span class="fw-semibold text-dark small">{{ $banner->user->username }}</span>
                            </a>
                        @else
                            <span class="badge bg-soft-secondary text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.size') }}">
                        <span class="badge bg-light text-dark border">{{ $banner->px }}</span>
                    </td>
                    <td data-label="{{ __('messages.views') }}">
                        <a href="{{ route('admin.stats', ['ty' => 'banner', 'id' => $banner->id]) }}" class="badge bg-soft-warning text-warning text-decoration-none" title="{{ __('messages.Stats') }}">
                            <i class="feather-eye me-1"></i>{{ number_format($banner->vu) }}
                        </a>
                        @if($banner->img_b)
                            <div class="mt-1 small text-muted" style="font-size: 11px;">
                                <span class="text-success">A: {{ number_format($banner->vu_a) }}</span> | 
                                <span class="text-info">B: {{ number_format($banner->vu_b) }}</span>
                            </div>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.clicks') }}">
                        <a href="{{ route('admin.stats', ['ty' => 'vu', 'id' => $banner->id]) }}" class="badge bg-soft-primary text-primary text-decoration-none" title="{{ __('messages.Stats') }}">
                            <i class="feather-mouse-pointer me-1"></i>{{ number_format($banner->clik) }}
                        </a>
                        @if($banner->img_b)
                            <div class="mt-1 small text-muted" style="font-size: 11px;">
                                <span class="text-success">A: {{ number_format($banner->clik_a) }}</span> | 
                                <span class="text-info">B: {{ number_format($banner->clik_b) }}</span>
                            </div>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.status') }}">
                        <div class="form-check form-switch d-inline-block m-0">
                            <input 
                                class="form-check-input status-toggle-btn" 
                                type="checkbox" 
                                role="switch"
                                id="statusSwitch{{ $banner->id }}" 
                                data-id="{{ $banner->id }}"
                                {{ $banner->statu == 1 ? 'checked' : '' }}
                            >
                            <label class="form-check-label ms-1 small fw-semibold status-label-{{ $banner->id }} {{ $banner->statu == 1 ? 'text-success' : 'text-muted' }}" for="statusSwitch{{ $banner->id }}">
                                {{ $banner->statu == 1 ? __('messages.active') : __('messages.paused') }}
                            </label>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-sm btn-icon btn-light" onclick="previewBannerImage('{{ $banner->img }}', '{{ addslashes($banner->name) }}', '{{ $banner->img_b }}')" title="{{ __('messages.preview') }}">
                                <i class="feather-eye"></i>
                            </button>
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-icon btn-light-brand" title="{{ __('messages.edit') }}">
                                <i class="feather-edit-3"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger delete-banner-btn" data-id="{{ $banner->id }}" data-name="{{ $banner->name }}" title="{{ __('messages.delete') }}">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3">
                            <i class="feather-image"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('messages.no_results_found') ?? 'No banners found' }}</h6>
                        <p class="text-muted small mb-0">{{ __('messages.try_adjusting_filters') ?? 'Try changing search criteria or filters.' }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($banners->hasPages())
    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            {{ __('messages.showing') ?? 'Showing' }} {{ $banners->firstItem() ?? 0 }} - {{ $banners->lastItem() ?? 0 }} {{ __('messages.of') ?? 'of' }} {{ $banners->total() }}
        </div>
        <div class="pagination-wrapper">
            {{ $banners->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
