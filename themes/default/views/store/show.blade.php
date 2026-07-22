@extends('theme::layouts.master')

@section('content')
@php
    $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
    $commentCount = \App\Models\Option::where('o_type', 's_coment')->where('o_parent', $product->id)->count();
    $categoryName = $type ? $type->name : '';
    $categoryLabel = $categoryName ? (__('messages.' . $categoryName) ?? $categoryName) : '';
    $subCategoryLabel = '';
    if ($type && $type->o_mode) {
        if (is_numeric($type->o_mode)) {
            $subCategory = \App\Models\Option::find($type->o_mode);
            $subCategoryLabel = $subCategory ? $subCategory->name : '';
        } else {
            $subCategoryLabel = \Illuminate\Support\Facades\Lang::has('messages.' . $type->o_mode) ? __('messages.' . $type->o_mode) : $type->o_mode;
        }
    }
    $owner = $product->user;
    $ownerAvatar = $owner ? $owner->avatarUrl() : asset('upload/_avatar.png');
    $latestVersionLabel = $latestFile ? $latestFile->name : 'v1.0';
    $fileCount = $files->count();
    $reportKey = 'product' . $product->id;
    $pageSummary = \Illuminate\Support\Str::limit($product->o_valuer, 240);
@endphp

@include('theme::store.partials.page-shell-styles')



<div class="section-header">
    <div class="section-header-info">
        <p class="section-pretitle">{{ $categoryLabel }}</p>
        <h2 class="section-title">{{ $product->name }}</h2>
    </div>
    <div class="section-header-actions">
        <a class="section-header-subsection" href="{{ route('store.index') }}">{{ __('messages.store') }}</a>
        @if($categoryLabel)
            <a class="section-header-subsection" href="#">{{ $categoryLabel }}</a>
        @endif
        @if($subCategoryLabel)
            <a class="section-header-subsection" href="#">{{ $subCategoryLabel }}</a>
        @endif
        <p class="section-header-subsection">{{ $product->name }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="alert">
        <strong><i class="fa fa-check-circle" aria-hidden="true"></i></strong>&nbsp; {{ session('success') }}
    </div>
@endif

@include('theme::partials.ads', ['id' => 5])

@if($isSuspended)
    <div class="alert alert-danger" role="alert">
        <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong>&nbsp; {{ __('messages.product_suspended_notice') }}
    </div>
@endif

<div class="store-detail-page post{{ $status ? $status->id : $product->id }}">
    <div class="widget-box store-shell-card no-padding">
        <div class="store-hero">
            <div class="store-hero__main">
                <div class="store-hero__media">
                    <img src="{{ $productImage }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ theme_asset('img/error_plug.png') }}';">
                </div>
                <div class="store-hero__content">
                    <div class="store-badge-row">
                        @if($product->has_active_sale)
                            <span class="store-pill store-pill-sale" style="background: #e74c3c; color: white;">
                                <span class="old-price" style="text-decoration: line-through; opacity: 0.7; margin-right: 5px;">{{ $product->o_order }}</span>
                                <strong>{{ $product->sale_price }}</strong> {{ __('messages.points') }}
                            </span>
                        @elseif($product->o_order > 0)
                            <span class="store-pill"><strong>{{ $product->o_order }}</strong> {{ __('messages.points') }}</span>
                        @else
                            <span class="store-pill">{{ __('messages.free') }}</span>
                        @endif
                        @if($categoryLabel)
                            <span class="store-pill">{{ $categoryLabel }}</span>
                        @endif
                        @if($subCategoryLabel)
                            <span class="store-pill">{{ $subCategoryLabel }}</span>
                        @endif
                    </div>
                    <h3 class="store-title">{{ $product->name }}</h3>
                    <p class="store-subtitle">{{ $pageSummary }}</p>
                    <div class="store-stat-grid">
                        <div class="store-stat-card">
                            <span>{{ __('messages.version') }}</span>
                            <strong>{{ $latestVersionLabel }}</strong>
                        </div>
                        <div class="store-stat-card">
                            <span>{{ __('messages.download') }}</span>
                            <strong>{{ $downloadCount }}</strong>
                        </div>
                        <div class="store-stat-card">
                            <span>{{ __('messages.comments') }}</span>
                            <strong>{{ $commentCount }}</strong>
                        </div>
                        <div class="store-stat-card">
                            <span>{{ __('messages.version') }}</span>
                            <strong>{{ $fileCount }}</strong>
                        </div>
                    </div>
                    <div class="store-inline-actions">
                        @if(auth()->check())
                            @if($license || $product->o_order == 0 || auth()->id() == $product->o_parent)
                                @if($downloadHash)
                                    <a href="{{ route('store.download.hash', $downloadHash) }}" class="button secondary" style="color: #fff;">
                                        <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                    </a>
                                @endif
                            @else
                                <button type="button" class="button secondary" style="color: #fff;" onclick="document.getElementById('inline-purchase-panel').style.display = document.getElementById('inline-purchase-panel').style.display === 'none' ? 'block' : 'none';">
                                    <i class="fa fa-shopping-cart"></i>&nbsp;{{ __('messages.purchase') }}
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="button secondary" style="color: #fff;">
                                <i class="fa fa-shopping-cart"></i>&nbsp;{{ __('messages.purchase') }}
                            </a>
                        @endif
                        <a class="button tertiary" href="{{ route('kb.index', $product->name) }}">
                            <i class="fa fa-database" aria-hidden="true"></i>&nbsp;{{ __('messages.knowledgebase') }}
                        </a>
                    </div>
                    @if(auth()->check() && !$license && $product->o_order > 0)
                        <div id="inline-purchase-panel" style="display: none; margin-top: 20px; background: #1d2333; border: 1px solid #2f3749; border-radius: 12px; padding: 20px; text-align: left;">
                            <h4 style="margin-top: 0; color: #fff;">{{ __('messages.confirm_purchase') ?? 'Confirm Purchase' }}</h4>
                            <div class="price-breakdown-box" style="background: #21283b; border-radius: 8px; padding: 16px; margin-bottom: 20px; margin-top: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span style="color: #9aa4bf;">{{ __('messages.price') ?? 'Price' }}</span>
                                    <strong style="color: #fff;" id="base-price-display">{{ $product->current_price }} PTS</strong>
                                </div>
                                <div id="discount-row" style="display: none; justify-content: space-between; margin-bottom: 10px; color: #2ecc71;">
                                    <span>{{ __('messages.discount') ?? 'Discount' }}</span>
                                    <strong id="discount-amount-display">-0 PTS</strong>
                                </div>
                                <hr style="border-top: 1px solid #2f3749; margin: 12px 0; background: none;">
                                <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 700;">
                                    <span style="color: #fff;">{{ __('messages.total') ?? 'Total' }}</span>
                                    <strong style="color: #4f46e5;" id="final-price-display">{{ $product->current_price }} PTS</strong>
                                </div>
                            </div>
                            <div class="promo-code-section" style="margin-bottom: 20px;">
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #9aa4bf; margin-bottom: 8px;">{{ __('messages.discount_code') ?? 'Promo Code' }}</label>
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" id="coupon-code-input" class="form-control" placeholder="{{ __('messages.enter_promo_code') ?? 'Enter code...' }}" style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 10px 14px; flex-grow: 1;">
                                    <button type="button" id="apply-coupon-btn" class="button primary" style="padding: 10px 20px; border-radius: 6px; white-space: nowrap;">{{ __('messages.apply') ?? 'Apply' }}</button>
                                </div>
                                <div id="coupon-feedback" style="margin-top: 8px; font-size: 13px; font-weight: 600; display: none;"></div>
                            </div>
                            <div id="purchase-error" class="alert alert-danger" style="display: none; font-size: 14px; padding: 12px; margin-bottom: 20px;"></div>
                            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                <button type="button" class="button secondary" onclick="document.getElementById('inline-purchase-panel').style.display='none'" style="padding: 10px 20px; border-radius: 6px; background: #2f3749; color: #fff; border: none; cursor: pointer;">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                                <button type="button" id="confirm-purchase-btn" class="button primary" style="padding: 10px 24px; border-radius: 6px; background: #4f46e5; border: none; cursor: pointer;">{{ __('messages.confirm_purchase') ?? 'Confirm Purchase' }}</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="store-aside">
                <div class="store-aside-card">
                    <div class="store-aside-card__header">
                        <div>
                            <p class="store-aside-card__label">{{ __('messages.publisher') }}</p>
                            <p class="store-aside-card__title">
                                @if($owner)
                                    <a href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>
                                @else
                                    {{ __('messages.unknown') }}
                                @endif
                            </p>
                        </div>
                        <div class="store-action-menu" data-activity-menu-wrap data-store-actions-menu>
                            <button type="button" class="store-action-menu__trigger" data-activity-menu-trigger data-activity-menu-type="actions" aria-expanded="false">
                                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                            </button>
                            <div class="simple-dropdown store-action-menu__panel" data-activity-menu-panel>
                                <a class="simple-dropdown-link" href="{{ route('kb.index', $product->name) }}">
                                    <i class="fa fa-database" aria-hidden="true"></i>&nbsp;{{ __('messages.knowledgebase') }}
                                </a>
                                <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="navigator.clipboard.writeText('{{ route('store.show', $product->name) }}'); alert('{{ __('messages.link_copied') }}');">
                                    <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
                                </button>
                                @if($status)
                                    @include('theme::partials.activity.promotion_link', ['activity' => $status])
                                @endif
                                @if($canManageProduct)
                                    <a class="simple-dropdown-link" href="{{ route('store.update', $product->name) }}">
                                        <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit_product') }}
                                    </a>
                                    <a class="simple-dropdown-link" href="{{ route('store.downloads', $product->name) }}">
                                        <i class="fa fa-users" aria-hidden="true"></i>&nbsp;{{ __('messages.downloads') ?? 'Downloads' }}
                                    </a>
                                    <a class="simple-dropdown-link" href="{{ route('store.updates', $product->name) }}">
                                        <i class="fa fa-history" aria-hidden="true"></i>&nbsp;{{ __('messages.manage_updates') ?? 'Manage Updates' }}
                                    </a>
                                    @if($topic)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" id="trigger-topic-edit-from-menu">
                                        <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp;{{ __('messages.edit_topic') }}
                                    </button>
                                    @endif
                                    <p class="simple-dropdown-link store-dropdown-button" onclick="deletePost({{ $product->id }}, 7867, '.store-detail-page')">
                                        <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                                    </p>
                                @elseif(auth()->check())
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportPost({{ $product->id }}, 7867, '{{ $reportKey }}')">
                                        <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_product') }}
                                    </button>
                                    @if($owner)
                                        <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportUser({{ $owner->id }}, '{{ $reportKey }}')">
                                            <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="user-status" style="margin-top: 18px;">
                        @if($owner)
                            <a class="user-status-avatar" href="{{ route('profile.show', $owner->username) }}">
                                <div class="user-avatar small no-outline {{ $owner->isOnline() ? 'online' : 'offline' }}">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ $ownerAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                        </div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-40-44" data-line-color="{{ $owner ? $owner->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                        </div>
                                    </div>
                                    @if($owner->hasVerifiedBadge())
                                    <div class="user-avatar-badge">
                                            <div class="user-avatar-badge-border">
                                                <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;">
                                                    <canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas>
                                                </div>
                                            </div>
                                            <div class="user-avatar-badge-content">
                                                <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;">
                                                    <canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas>
                                                </div>
                                            </div>
                                            <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endif
                        <p class="user-status-title medium">
                            @if($owner)
                                <a class="bold" href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>
                            @else
                                {{ __('messages.unknown') }}
                            @endif
                        </p>
                        <p class="user-status-text small">
                            @if($status)
                                <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp;{{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    <div class="store-aside-card__meta">
                        <div class="store-meta-row">
                            <span>{{ __('messages.Version_nbr') }}</span>
                            <strong>{{ $latestVersionLabel }}</strong>
                        </div>
                        <div class="store-meta-row">
                            <span>{{ __('messages.comments') }}</span>
                            <strong>{{ $commentCount }}</strong>
                        </div>
                        <div class="store-meta-row">
                            <span>{{ __('messages.download') }}</span>
                            <strong>{{ $downloadCount }}</strong>
                        </div>
                    </div>
                    <div id="report{{ $reportKey }}" class="store-inline-report"></div>
                </div>
                @if($license)
                <div class="store-aside-card" style="margin-top: 15px; border: 2px solid #615dfa; background: #fafafd; border-radius: 12px; padding: 16px;">
                    <p class="store-aside-card__label" style="color: #615dfa; font-weight: bold; margin-bottom: 8px;">
                        <i class="fa fa-key"></i>&nbsp;{{ __('messages.license_key_label') }}
                    </p>
                    <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid #e1e1f0; border-radius: 6px; padding: 8px 12px; font-family: monospace; font-size: 13px; font-weight: bold; color: #3e3f5e; letter-spacing: 0.5px;">
                        <span id="license-key-val">{{ $license->license_key }}</span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $license->license_key }}'); alert('{{ __('messages.license_key_copied') }}');" style="background: none; border: none; cursor: pointer; color: #8f91ac; padding: 0 4px;" title="Copy">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <p style="font-size: 11px; color: #8f91ac; margin-top: 8px; line-height: 1.4; margin-bottom: 0;">
                        {{ __('messages.license_key_hint') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="widget-box store-content-card store-tabs">
        <div class="tab-box">
            <div class="tab-box-options">
                <div class="tab-box-option active" data-tab="desc-tab">
                    <p class="tab-box-option-title">{{ __('messages.details') }}</p>
                </div>
                @if($topic)
                <div class="tab-box-option" data-tab="topic-tab">
                    <p class="tab-box-option-title">{{ __('messages.topic') }}</p>
                </div>
                @endif
                <div class="tab-box-option" data-tab="comments-tab">
                    <p class="tab-box-option-title">{{ __('messages.comments') }} <span class="highlighted">{{ $commentCount }}</span></p>
                </div>
                <div class="tab-box-option" data-tab="versions-tab">
                    <p class="tab-box-option-title">{{ __('messages.version') }} <span class="highlighted">{{ $latestVersionLabel }}</span></p>
                </div>
            </div>
            <div class="tab-box-items">
                <div class="tab-box-item" id="desc-tab" style="display: block; transition: none 0s ease 0s;">
                    <div class="tab-box-item-content store-rich-text">
                        @if($canManageProduct)
                        <div class="store-details-toolbar">
                            <button type="button" class="button secondary small" id="store-edit-details-btn">
                                <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp; {{ __('messages.edit_product') }}
                            </button>
                            <button type="button" class="button primary small" id="store-save-details-btn" style="display:none;">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                            <button type="button" class="button white small" id="store-cancel-details-btn" style="display:none;">
                                <i class="fa fa-times" aria-hidden="true"></i>&nbsp; {{ __('messages.cancel') }}
                            </button>
                            <span id="store-details-saving" style="display:none;color:#8f91ac;font-size:.85rem;margin-inline-start:10px;">
                                <i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.saving') }}
                            </span>
                            <span id="store-details-saved" style="display:none;color:#4ff461;font-size:.85rem;margin-inline-start:10px;">
                                <i class="fa fa-check-circle"></i>&nbsp; {{ __('messages.saved') }}
                            </span>
                        </div>
                        @endif

                        {{-- Read-only view --}}
                        <div id="store-details-display" class="markdown-content">
                            {!! $product->o_valuer !!}
                        </div>

                        {{-- Editor (hidden by default) --}}
                        @if($canManageProduct)
                        <div id="store-details-editor" style="display:none;">
                            <div class="stackedit-tools mb-2">
                                <button type="button" class="button secondary small open-stackedit-details">
                                    <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp; {{ __('messages.edit_with_stackedit') }}
                                </button>
                            </div>
                            <textarea id="store-details-textarea" rows="15" class="form-control" style="width:100%;padding:10px;">{{ $product->o_valuer }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Topic Tab --}}
                @if($topic)
                <div class="tab-box-item" id="topic-tab" style="display: none; transition: none 0s ease 0s;">
                    <div class="tab-box-item-content">
                        @if($canManageProduct)
                        <div class="store-topic-toolbar">
                            <button type="button" class="button secondary small" id="store-edit-topic-btn">
                                <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp; {{ __('messages.edit_topic') }}
                            </button>
                            <button type="button" class="button primary small" id="store-save-topic-btn" style="display:none;">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                            <button type="button" class="button white small" id="store-cancel-topic-btn" style="display:none;">
                                <i class="fa fa-times" aria-hidden="true"></i>&nbsp; {{ __('messages.cancel') }}
                            </button>
                            <span id="store-topic-saving" style="display:none;color:#8f91ac;font-size:.85rem;margin-inline-start:10px;">
                                <i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.saving') ?? 'Saving...' }}
                            </span>
                            <span id="store-topic-saved" style="display:none;color:#4ff461;font-size:.85rem;margin-inline-start:10px;">
                                <i class="fa fa-check-circle"></i>&nbsp; {{ __('messages.saved') ?? 'Saved!' }}
                            </span>
                        </div>
                        @endif

                        {{-- Read-only view --}}
                        <div id="store-topic-display" class="store-rich-text markdown-content">
                            {!! $topic->txt !!}
                        </div>

                        {{-- Editor (hidden by default) --}}
                        @if($canManageProduct)
                        <div id="store-topic-editor" style="display:none;">
                            <div class="stackedit-tools mb-2">
                                <button type="button" class="button secondary small open-stackedit-topic">
                                    <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp; {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                                </button>
                            </div>
                            <textarea id="store-topic-textarea" rows="15" class="form-control" style="width:100%;padding:10px;">{{ $topic->txt }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="tab-box-item" id="comments-tab" style="display: none; transition: none 0s ease 0s;">
                    <div class="tab-box-item-content">
                        <div class="post-comment-list post-comment-list-{{ $product->id }}"></div>
                    </div>
                </div>
                <div class="tab-box-item" id="versions-tab" style="display: none; transition: none 0s ease 0s;">
                    <div class="tab-box-item-content">
                        <div class="table-responsive">
                            <table id="tablepagination" class="table table-borderless table-hover store-version-table">
                                <thead>
                                    <tr>
                                        <th><center>{{ __('messages.id') ?? 'ID' }}</center></th>
                                        <th><center>{{ __('messages.version') }}</center></th>
                                        <th><center>{{ __('messages.download') }}</center></th>
                                        @if($canManageProduct)
                                            <th><center>{{ __('messages.desc') }}</center></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $file)
                                        @php
                                            $fileHash = hash('crc32', $file->o_mode . $file->id);
                                            $fileDownloads = \App\Models\Short::where('sh_type', 7867)->where('tp_id', $file->id)->value('clik') ?? 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $file->id }}</td>
                                            <td><center><b>{{ $file->name }}</b></center></td>
                                            <td>
                                                <center>
                                                    @if(auth()->check() && auth()->user()->pts < $product->o_order)
                                                        <a href="javascript:void(0);" class="button secondary not-enough-points" style="color: #fff;">
                                                            <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                                            <span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>
                                                        </a>
                                                    @elseif(auth()->check())
                                                        <a href="{{ route('store.download.hash', $fileHash) }}" class="button secondary" style="color: #fff;">
                                                            <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                                            <span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('login') }}" class="button secondary" style="color: #fff;">
                                                            <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                                            <span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>
                                                        </a>
                                                    @endif
                                                </center>
                                            </td>
                                            @if($canManageProduct)
                                                <td class="markdown-content">{!! $file->o_valuer !!}</td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $canManageProduct ? 4 : 3 }}" class="store-empty-table">{{ __('messages.no_post') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.tab-box-option').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.tab-box-option').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.tab-box-item').forEach(function(item) { item.style.display = 'none'; });
            tab.classList.add('active');
            var target = document.getElementById(tab.dataset.tab);
            if (target) {
                target.style.display = 'block';
            }
            if (tab.dataset.tab === 'comments-tab') {
                loadComments({{ $product->id }}, 'store');
            }
        });
    });

    document.querySelectorAll('.not-enough-points').forEach(function(btn) {
        btn.addEventListener('click', function() {
            alert("{{ __('messages.insufficient_points') }}");
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Markdown rendering
        function renderAllMarkdown() {
            document.querySelectorAll('.markdown-content').forEach(el => {
                if (!el.getAttribute('data-rendered')) {
                    try {
                        const rawContent = el.innerHTML;
                        el.innerHTML = DOMPurify.sanitize(marked.parse(el.innerText || rawContent));
                        el.setAttribute('data-rendered', 'true');
                        el.style.display = 'block';
                    } catch (e) {
                        console.error('Error rendering markdown:', e);
                    }
                }
            });
        }
        renderAllMarkdown();

        // ── Topic inline editing ──
        const editBtn   = document.getElementById('store-edit-topic-btn');
        const saveBtn   = document.getElementById('store-save-topic-btn');
        const cancelBtn = document.getElementById('store-cancel-topic-btn');
        const savingEl  = document.getElementById('store-topic-saving');
        const savedEl   = document.getElementById('store-topic-saved');
        const display   = document.getElementById('store-topic-display');
        const editor    = document.getElementById('store-topic-editor');
        const textarea  = document.getElementById('store-topic-textarea');

        if (editBtn && textarea) {
            let originalValue = textarea.value;

            // Trigger from menu
            const menuTrigger = document.getElementById('trigger-topic-edit-from-menu');
            if (menuTrigger) {
                menuTrigger.addEventListener('click', function() {
                    const topicTab = document.querySelector('[data-tab="topic-tab"]');
                    if (topicTab) {
                        topicTab.click(); // Switch to tab
                        editBtn.click(); // Open editor
                    }
                });
            }

            // Enter edit mode
            editBtn.addEventListener('click', function() {
                originalValue = textarea.value;
                display.style.display = 'none';
                editor.style.display = 'block';
                editBtn.style.display = 'none';
                saveBtn.style.display = '';
                cancelBtn.style.display = '';
                savedEl.style.display = 'none';
            });

            // Cancel edit
            cancelBtn.addEventListener('click', function() {
                textarea.value = originalValue;
                editor.style.display = 'none';
                display.style.display = '';
                display.removeAttribute('data-rendered');
                display.innerHTML = originalValue;
                renderAllMarkdown();
                editBtn.style.display = '';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            });

            // Save via AJAX
            saveBtn.addEventListener('click', function() {
                saveBtn.disabled = true;
                savingEl.style.display = '';
                savedEl.style.display = 'none';

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                fetch("{{ route('store.update.topic', $product->name) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ txt: textarea.value })
                })
                .then(r => r.json())
                .then(data => {
                    savingEl.style.display = 'none';
                    saveBtn.disabled = false;
                    if (data.success) {
                        originalValue = textarea.value;
                        // Update display
                        display.removeAttribute('data-rendered');
                        display.innerHTML = textarea.value;
                        renderAllMarkdown();
                        // Switch back to view mode
                        editor.style.display = 'none';
                        display.style.display = '';
                        editBtn.style.display = '';
                        saveBtn.style.display = 'none';
                        cancelBtn.style.display = 'none';
                        savedEl.style.display = '';
                        setTimeout(() => { savedEl.style.display = 'none'; }, 3000);
                    } else {
                        alert(data.message || 'Error');
                    }
                })
                .catch(() => {
                    savingEl.style.display = 'none';
                    saveBtn.disabled = false;
                    alert('Network error');
                });
            });

            // StackEdit integration for topic editor
            const stackeditBtn = document.querySelector('.open-stackedit-topic');
            if (stackeditBtn) {
                stackeditBtn.addEventListener('click', function() {
                    const stackedit = new Stackedit();
                    stackedit.openFile({
                        name: '{{ $product->name }}',
                        content: { text: textarea.value }
                    });
                    const adjustIframe = () => {
                        const iframe = document.querySelector('iframe[src*="stackedit.io"]');
                        if (iframe) {
                            const header = document.querySelector('.header, .nxl-header');
                            if (header) {
                                const hh = header.offsetHeight;
                                iframe.style.top = hh + 'px';
                                iframe.style.height = `calc(100% - ${hh}px)`;
                            } else {
                                iframe.style.top = '80px';
                                iframe.style.height = 'calc(100% - 80px)';
                            }
                        } else {
                            setTimeout(adjustIframe, 50);
                        }
                    };
                    adjustIframe();
                    stackedit.on('fileChange', (file) => {
                        textarea.value = file.content.text;
                    });
                });
            }
        }

        // ── Details inline editing ──
        const editDetailsBtn   = document.getElementById('store-edit-details-btn');
        const saveDetailsBtn   = document.getElementById('store-save-details-btn');
        const cancelDetailsBtn = document.getElementById('store-cancel-details-btn');
        const savingDetailsEl  = document.getElementById('store-details-saving');
        const savedDetailsEl   = document.getElementById('store-details-saved');
        const displayDetails   = document.getElementById('store-details-display');
        const editorDetails    = document.getElementById('store-details-editor');
        const textareaDetails  = document.getElementById('store-details-textarea');

        if (editDetailsBtn && textareaDetails) {
            let originalDetailsValue = textareaDetails.value;

            // Enter edit mode
            editDetailsBtn.addEventListener('click', function() {
                originalDetailsValue = textareaDetails.value;
                displayDetails.style.display = 'none';
                editorDetails.style.display = 'block';
                editDetailsBtn.style.display = 'none';
                saveDetailsBtn.style.display = '';
                cancelDetailsBtn.style.display = '';
                savedDetailsEl.style.display = 'none';
            });

            // Cancel edit
            cancelDetailsBtn.addEventListener('click', function() {
                textareaDetails.value = originalDetailsValue;
                editorDetails.style.display = 'none';
                displayDetails.style.display = '';
                displayDetails.removeAttribute('data-rendered');
                displayDetails.innerHTML = originalDetailsValue;
                renderAllMarkdown();
                editDetailsBtn.style.display = '';
                saveDetailsBtn.style.display = 'none';
                cancelDetailsBtn.style.display = 'none';
            });

            // Save via AJAX
            saveDetailsBtn.addEventListener('click', function() {
                saveDetailsBtn.disabled = true;
                savingDetailsEl.style.display = '';
                savedDetailsEl.style.display = 'none';

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                fetch("{{ route('store.update.details', $product->name) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ txt: textareaDetails.value })
                })
                .then(r => r.json())
                .then(data => {
                    savingDetailsEl.style.display = 'none';
                    saveDetailsBtn.disabled = false;
                    if (data.success) {
                        originalDetailsValue = textareaDetails.value;
                        // Update display
                        displayDetails.removeAttribute('data-rendered');
                        displayDetails.innerHTML = textareaDetails.value;
                        renderAllMarkdown();
                        // Switch back to view mode
                        editorDetails.style.display = 'none';
                        displayDetails.style.display = '';
                        editDetailsBtn.style.display = '';
                        saveDetailsBtn.style.display = 'none';
                        cancelDetailsBtn.style.display = 'none';
                        savedDetailsEl.style.display = '';
                        setTimeout(() => { savedDetailsEl.style.display = 'none'; }, 3000);
                    } else {
                        alert(data.message || 'Error');
                    }
                })
                .catch(() => {
                    savingDetailsEl.style.display = 'none';
                    saveDetailsBtn.disabled = false;
                    alert('Network error');
                });
            });

            // StackEdit integration for details editor
            const stackeditDetailsBtn = document.querySelector('.open-stackedit-details');
            if (stackeditDetailsBtn) {
                stackeditDetailsBtn.addEventListener('click', function() {
                    const stackedit = new Stackedit();
                    stackedit.openFile({
                        name: '{{ $product->name }}',
                        content: { text: textareaDetails.value }
                    });
                    const adjustIframe = () => {
                        const iframe = document.querySelector('iframe[src*="stackedit.io"]');
                        if (iframe) {
                            const header = document.querySelector('.header, .nxl-header');
                            if (header) {
                                const hh = header.offsetHeight;
                                iframe.style.top = hh + 'px';
                                iframe.style.height = `calc(100% - ${hh}px)`;
                            } else {
                                iframe.style.top = '80px';
                                iframe.style.height = 'calc(100% - 80px)';
                            }
                        } else {
                            setTimeout(adjustIframe, 50);
                        }
                    };
                    adjustIframe();
                    stackedit.on('fileChange', (file) => {
                        textareaDetails.value = file.content.text;
                    });
                });
            }
        }
    });
</script>
<style>
    .markdown-content { display: none; }
    .store-topic-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 0 16px;
        border-bottom: 1px solid rgba(140, 146, 182, 0.16);
        margin-bottom: 16px;
    }
    #store-topic-editor, #store-details-editor { margin-top: 4px; }
    #store-topic-editor textarea, #store-details-editor textarea {
        font-family: Consolas, Monaco, 'Courier New', monospace;
        font-size: 0.92rem;
        line-height: 1.7;
        border-radius: 12px;
        border: 1px solid rgba(140, 146, 182, 0.24);
        background: rgba(97, 93, 250, 0.03);
    }
    body[data-theme="css_d"] #store-topic-editor textarea,
    body[data-theme="css_d"] #store-details-editor textarea {
        background: rgba(97, 93, 250, 0.08);
        border-color: rgba(140, 146, 182, 0.18);
        color: #e8e8e8;
    }
    .store-details-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 0 16px;
        border-bottom: 1px solid rgba(140, 146, 182, 0.16);
        margin-bottom: 16px;
    }
</style>

@if(auth()->check() && !$license && $product->o_order > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponCodeInput = document.getElementById('coupon-code-input');
    const couponFeedback = document.getElementById('coupon-feedback');
    const discountRow = document.getElementById('discount-row');
    const discountAmountDisplay = document.getElementById('discount-amount-display');
    const finalPriceDisplay = document.getElementById('final-price-display');
    const confirmPurchaseBtn = document.getElementById('confirm-purchase-btn');
    const purchaseError = document.getElementById('purchase-error');

    let currentCoupon = null;

    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const code = couponCodeInput.value.trim();
            if (!code) {
                couponFeedback.textContent = "{{ __('messages.enter_coupon_code') ?? 'Please enter a coupon code.' }}";
                couponFeedback.style.color = '#e74c3c';
                couponFeedback.style.display = 'block';
                return;
            }

            applyCouponBtn.disabled = true;
            couponFeedback.textContent = "{{ __('messages.validating') ?? 'Validating...' }}";
            couponFeedback.style.color = '#9aa4bf';
            couponFeedback.style.display = 'block';

            fetch("{{ route('store.discounts.validate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    code: code,
                    product_id: "{{ $product->id }}"
                })
            })
            .then(res => res.json())
            .then(data => {
                applyCouponBtn.disabled = false;
                if (data.success) {
                    currentCoupon = code;
                    couponFeedback.textContent = "{{ __('messages.coupon_applied') ?? 'Coupon applied successfully!' }} (" + data.discount_text + ")";
                    couponFeedback.style.color = '#2ecc71';
                    
                    discountAmountDisplay.textContent = "-" + data.discount_amount + " PTS";
                    discountRow.style.display = 'flex';
                    finalPriceDisplay.textContent = data.final_price + " PTS";
                } else {
                    currentCoupon = null;
                    couponFeedback.textContent = data.message;
                    couponFeedback.style.color = '#e74c3c';
                    
                    discountRow.style.display = 'none';
                    finalPriceDisplay.textContent = "{{ $product->current_price }} PTS";
                }
            })
            .catch(err => {
                applyCouponBtn.disabled = false;
                currentCoupon = null;
                couponFeedback.textContent = "{{ __('messages.network_error') ?? 'An error occurred. Please try again.' }}";
                couponFeedback.style.color = '#e74c3c';
                
                discountRow.style.display = 'none';
                finalPriceDisplay.textContent = "{{ $product->current_price }} PTS";
            });
        });
    }

    if (confirmPurchaseBtn) {
        confirmPurchaseBtn.addEventListener('click', function() {
            confirmPurchaseBtn.disabled = true;
            confirmPurchaseBtn.textContent = "{{ __('messages.processing') ?? 'Processing...' }}";
            purchaseError.style.display = 'none';

            fetch("{{ route('store.purchase', $product->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    code: currentCoupon
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.download_url;
                } else {
                    confirmPurchaseBtn.disabled = false;
                    confirmPurchaseBtn.textContent = "{{ __('messages.confirm_purchase') ?? 'Confirm Purchase' }}";
                    purchaseError.textContent = data.message;
                    purchaseError.style.display = 'block';
                }
            })
            .catch(err => {
                confirmPurchaseBtn.disabled = false;
                confirmPurchaseBtn.textContent = "{{ __('messages.confirm_purchase') ?? 'Confirm Purchase' }}";
                purchaseError.textContent = "{{ __('messages.network_error') ?? 'An error occurred. Please try again.' }}";
                purchaseError.style.display = 'block';
            });
        });
    }
});
</script>
@endif

@endsection
