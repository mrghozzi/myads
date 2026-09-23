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
                <th>{{ __('messages.smart_ad') }}</th>
                <th>{{ __('messages.targeting') ?? 'Targeting' }}</th>
                <th>{{ __('messages.impressions') }}</th>
                <th>{{ __('messages.clicks') }}</th>
                <th>{{ __('messages.ctr') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th class="text-end">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="smartAdsTableBody">
            @forelse($smartAds as $smartAd)
                @php
                    $ctr = $smartAd->impressions > 0 ? round(($smartAd->clicks / $smartAd->impressions) * 100, 2) : 0;
                @endphp
                <tr id="smartAdRow{{ $smartAd->id }}">
                    <td data-label="#" class="text-center">
                        <div class="form-check form-check-md d-inline-block">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $smartAd->id }}">
                        </div>
                    </td>
                    <td data-label="ID">
                        <span class="badge bg-soft-secondary text-dark fw-bold">#{{ $smartAd->id }}</span>
                    </td>
                    <td data-label="{{ __('messages.user') }}">
                        @if($smartAd->user)
                            <a href="{{ route('profile.show', $smartAd->user->username) }}" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                                <div class="avatar-image avatar-sm rounded-circle overflow-hidden" style="width: 28px; height: 28px;">
                                    <img src="{{ $smartAd->user->img ? asset($smartAd->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                </div>
                                <span class="fw-semibold text-dark small">{{ $smartAd->user->username }}</span>
                            </a>
                        @else
                            <span class="badge bg-soft-secondary text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.smart_ad') }}" style="max-width: 280px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            @if($smartAd->image)
                                <img src="{{ $smartAd->image }}" alt="" style="width: 28px; height: 28px; object-fit: cover; border-radius: 6px;" onerror="this.style.display='none';">
                            @endif
                            <span class="fw-bold text-dark text-truncate" title="{{ $smartAd->displayTitle() }}">
                                {{ $smartAd->displayTitle() }}
                            </span>
                        </div>
                        <div class="small text-truncate">
                            <a href="{{ $smartAd->landing_url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none">
                                <i class="feather-external-link me-1" style="font-size: 11px;"></i>{{ $smartAd->landing_url }}
                            </a>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.targeting') ?? 'Targeting' }}" style="max-width: 200px;">
                        <div class="small text-truncate text-muted mb-1" title="{{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetCountries()) }}">
                            <i class="feather-globe me-1 text-primary"></i>{{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetCountries()) }}
                        </div>
                        <div class="small text-truncate text-muted" title="{{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetDevices()) }}">
                            <i class="feather-smartphone me-1 text-info"></i>{{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetDevices()) }}
                        </div>
                    </td>
                    <td data-label="{{ __('messages.impressions') }}">
                        <span class="badge bg-soft-warning text-warning">
                            <i class="feather-eye me-1"></i>{{ number_format($smartAd->impressions) }}
                        </span>
                    </td>
                    <td data-label="{{ __('messages.clicks') }}">
                        <span class="badge bg-soft-primary text-primary">
                            <i class="feather-mouse-pointer me-1"></i>{{ number_format($smartAd->clicks) }}
                        </span>
                    </td>
                    <td data-label="{{ __('messages.ctr') }}">
                        <span class="badge bg-soft-success text-success fw-bold">{{ $ctr }}%</span>
                    </td>
                    <td data-label="{{ __('messages.status') }}">
                        <div class="form-check form-switch d-inline-block m-0">
                            <input 
                                class="form-check-input status-toggle-btn" 
                                type="checkbox" 
                                role="switch"
                                id="smartAdStatusSwitch{{ $smartAd->id }}" 
                                data-id="{{ $smartAd->id }}"
                                {{ (int) $smartAd->statu === 1 ? 'checked' : '' }}
                            >
                            <label class="form-check-label ms-1 small fw-semibold smartad-status-label-{{ $smartAd->id }} {{ (int) $smartAd->statu === 1 ? 'text-success' : ((int) $smartAd->statu === 2 ? 'text-danger' : 'text-muted') }}" for="smartAdStatusSwitch{{ $smartAd->id }}">
                                @if((int) $smartAd->statu === 1)
                                    {{ __('messages.active') }}
                                @elseif((int) $smartAd->statu === 2)
                                    {{ __('messages.blocked') }}
                                @else
                                    {{ __('messages.paused') }}
                                @endif
                            </label>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <a href="{{ route('admin.smart_ads.edit', $smartAd->id) }}" class="btn btn-sm btn-icon btn-light-brand" title="{{ __('messages.edit') }}">
                                <i class="feather-edit-3"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger delete-smartad-btn" data-id="{{ $smartAd->id }}" data-name="{{ $smartAd->displayTitle() }}" title="{{ __('messages.delete') }}">
                                <i class="feather-trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3">
                            <i class="feather-cpu"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('messages.no_results_found') ?? 'No smart ads found' }}</h6>
                        <p class="text-muted small mb-0">{{ __('messages.try_adjusting_filters') ?? 'Try changing search criteria or filters.' }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($smartAds->hasPages())
    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            {{ __('messages.showing') ?? 'Showing' }} {{ $smartAds->firstItem() ?? 0 }} - {{ $smartAds->lastItem() ?? 0 }} {{ __('messages.of') ?? 'of' }} {{ $smartAds->total() }}
        </div>
        <div class="pagination-wrapper">
            {{ $smartAds->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
