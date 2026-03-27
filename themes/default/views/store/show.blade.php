@extends('theme::layouts.master')

@section('content')
@php
    $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
    $commentCount = \App\Models\Option::where('o_type', 's_coment')->where('o_parent', $product->id)->count();
    $categoryName = $type ? $type->name : '';
    $categoryLabel = $categoryName ? __($categoryName) : '';
    $subCategoryLabel = '';
    if ($type && $type->o_mode) {
        if (is_numeric($type->o_mode)) {
            $subCategory = \App\Models\Option::find($type->o_mode);
            $subCategoryLabel = $subCategory ? $subCategory->name : '';
        } else {
            $subCategoryLabel = __($type->o_mode);
        }
    }
    $owner = $product->user;
    $ownerAvatar = $owner && $owner->img ? (\Illuminate\Support\Str::startsWith($owner->img, ['http://', 'https://']) ? $owner->img : asset($owner->img)) : theme_asset('img/avatar/default.png');
    $latestVersionLabel = $latestFile ? $latestFile->name : 'v1.0';
    $fileCount = $files->count();
    $reportKey = 'product' . $product->id;
    $pageSummary = \Illuminate\Support\Str::limit($product->o_valuer, 240);
@endphp

@include('theme::store.partials.page-shell-styles')

<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title">{{ __('messages.store') }}</p>
    <p class="section-banner-text"></p>
</div>

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
                        @if($product->o_order > 0)
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
                        @if($downloadHash)
                            @if(auth()->check() && auth()->user()->pts < $product->o_order)
                                <a href="javascript:void(0);" class="button secondary not-enough-points" style="color: #fff;">
                                    <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                </a>
                            @elseif(auth()->check())
                                <a href="{{ route('store.download.hash', $downloadHash) }}" class="button secondary" style="color: #fff;">
                                    <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="button secondary" style="color: #fff;">
                                    <i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}
                                </a>
                            @endif
                        @endif
                        <a class="button tertiary" href="{{ route('kb.index', $product->name) }}">
                            <i class="fa fa-database" aria-hidden="true"></i>&nbsp;{{ __('messages.knowledgebase') }}
                        </a>
                    </div>
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
                                        <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                        </div>
                                    </div>
                                    @if($owner->isAdmin())
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
            </div>
        </div>
    </div>

    <div class="widget-box store-content-card store-tabs">
        <div class="tab-box">
            <div class="tab-box-options">
                <div class="tab-box-option active" data-tab="desc-tab">
                    <p class="tab-box-option-title">{{ __('messages.details') }}</p>
                </div>
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
                        <p class="tab-box-item-paragraph">{!! nl2br(e($product->o_valuer)) !!}</p>
                    </div>
                </div>
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
                                                <td>{!! $file->o_valuer !!}</td>
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
@endsection
