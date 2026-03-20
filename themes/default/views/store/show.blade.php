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
    $ownerAvatar = $owner && $owner->img ? ( \Illuminate\Support\Str::startsWith($owner->img, ['http://', 'https://']) ? $owner->img : asset($owner->img) ) : theme_asset('img/avatar/default.png');
@endphp
<style>
    .paragraph_producet img { margin-top: 24px; width: 75%; height: auto; border-radius: 12px; }
</style>
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
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

@include('theme::partials.ads', ['id' => 5])

<div class="grid grid post{{ $status ? $status->id : $product->id }}">
    <div class="widget-box no-padding">
        <div class="widget-box-status">
            <div class="widget-box-status-content">
                <div class="user-status">
                    @if($owner)
                        <a class="user-status-avatar" href="{{ route('profile.show', $owner->username) }}">
                            <div class="user-avatar small no-outline {{ $owner->isOnline() ? 'online' : 'offline' }}">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $ownerAvatar }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44"></div>
                                </div>
                                @if($owner->isAdmin())
                                    <div class="user-avatar-badge">
                                        <div class="user-avatar-badge-border">
                                            <div class="hexagon-22-24"></div>
                                        </div>
                                        <div class="user-avatar-badge-content">
                                            <div class="hexagon-dark-16-18"></div>
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
                <hr />
                <p class="widget-box-status-text">
                    <div class="product-preview">
                        <a href="{{ route('store.show', $product->name) }}">
                            <figure class="product-preview-image liquid" style="background: rgba(0, 0, 0, 0) url({{ theme_asset('img/error_plug.png') }}) no-repeat scroll center center / cover;">
                                <img src="{{ $productImage }}" alt="{{ $product->name }}" style="display: none;">
                            </figure>
                        </a>
                        <div class="product-preview-info">
                            @if($product->o_order > 0)
                                <p class="text-sticker"><span class="highlighted">{{ $product->o_order }}</span> {{ __('messages.points') }}</p>
                            @else
                                <p class="text-sticker">{{ __('messages.free') }}</p>
                            @endif
                            <p class="product-preview-title"><a href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a></p>
                            <p class="product-preview-category digital">
                                @if($categoryLabel)
                                    <a href="#">{{ $categoryLabel }}</a>
                                @endif
                            </p>
                        </div>
                    </div>
                </p>
                <hr />
            </div>
        </div>
    </div>
    <div class="section-filters-bar v6">
        <div class="section-filters-bar-actions">
            <a class="button tertiary" href="{{ url('/kb/'.$product->name) }}"><i class="fa fa-database" aria-hidden="true"></i>&nbsp;{{ __('messages.knowledgebase') }}</a>
        </div>
        <p class="text-sticker">
            <svg class="text-sticker-icon icon-info">
                <use xlink:href="#svg-info"></use>
            </svg>
            {{ __('messages.Version_nbr') }}&nbsp;{{ $latestFile ? $latestFile->name : 'v1.0' }}
        </p>
        <div class="section-filters-bar-actions">
            @if($downloadHash)
                @if(auth()->check() && auth()->user()->pts < $product->o_order)
                    <a href="javascript:void(0);" class="button secondary not-enough-points" style="color: #fff;"><i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }} <span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $downloadCount }}</b></font></span></a>
                @elseif(auth()->check())
                    <a href="{{ route('store.download.hash', $downloadHash) }}" class="button secondary" style="color: #fff;"><i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }} <span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $downloadCount }}</b></font></span></a>
                @else
                    <a href="{{ route('login') }}" class="button secondary" style="color: #fff;"><i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }} <span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $downloadCount }}</b></font></span></a>
                @endif
            @endif
            @if(auth()->check() && (auth()->id() == $product->o_parent || auth()->user()->isAdmin()))
                <a href="{{ route('store.update', $product->name) }}" class="button primary">{{ __('messages.update') }}</a>
            @endif
        </div>
    </div>
    <div class="tab-box">
        <div class="tab-box-options">
            <div class="tab-box-option active" data-tab="desc-tab">
                <p class="tab-box-option-title">{{ __('messages.desc') }}</p>
            </div>
            <div class="tab-box-option" data-tab="comments-tab">
                <p class="tab-box-option-title">{{ __('messages.comments') }} <span class="highlighted">{{ $commentCount }}</span></p>
            </div>
            <div class="tab-box-option" data-tab="versions-tab">
                <p class="tab-box-option-title">{{ __('messages.version') }} <span class="highlighted">{{ $latestFile ? $latestFile->name : 'v1.0' }}</span></p>
            </div>
        </div>
        <div class="tab-box-items">
            <div class="tab-box-item" id="desc-tab" style="display: block; transition: none 0s ease 0s;">
                <div class="tab-box-item-content paragraph_producet">
                    <p class="tab-box-item-paragraph">{!! nl2br(e($product->o_valuer)) !!}</p>
                </div>
            </div>
            <div class="tab-box-item" id="comments-tab" style="display: none; transition: none 0s ease 0s;">
                <div class="tab-box-item-content">
                    <div class="product-preview">
                        <div class="post-comment-list post-comment-list-{{ $product->id }}"></div>
                    </div>
                </div>
            </div>
            <div class="tab-box-item" id="versions-tab" style="display: none; transition: none 0s ease 0s;">
                <div class="tab-box-item-content">
                    <table id="tablepagination" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th><center>{{ __('messages.id') ?? 'ID' }}</center></th>
                                <th><center>{{ __('messages.version') }}</center></th>
                                <th><center>{{ __('messages.download') }}</center></th>
                                @if(auth()->check() && (auth()->id() == $product->o_parent || auth()->user()->isAdmin()))
                                    <th><center>{{ __('messages.desc') }}</center></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
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
                                                <a href="javascript:void(0);" class="button secondary not-enough-points" style="color: #fff;">&nbsp;<i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}&nbsp;<span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>&nbsp;</a>
                                            @elseif(auth()->check())
                                                <a href="{{ route('store.download.hash', $fileHash) }}" class="button secondary" style="color: #fff;">&nbsp;<i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}&nbsp;<span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>&nbsp;</a>
                                            @else
                                                <a href="{{ route('login') }}" class="button secondary" style="color: #fff;">&nbsp;<i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}&nbsp;<span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>&nbsp;</a>
                                            @endif
                                        </center>
                                    </td>
                                    @if(auth()->check() && (auth()->id() == $product->o_parent || auth()->user()->isAdmin()))
                                        <td>{!! $file->o_valuer !!}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.tab-box-option').forEach(function(tab){
        tab.addEventListener('click', function(){
            document.querySelectorAll('.tab-box-option').forEach(function(t){ t.classList.remove('active'); });
            document.querySelectorAll('.tab-box-item').forEach(function(item){ item.style.display = 'none'; });
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
    document.querySelectorAll('.not-enough-points').forEach(function(btn){
        btn.addEventListener('click', function(){ alert("{{ __('messages.insufficient_points') }}"); });
    });
    document.querySelectorAll('.product-preview-image').forEach(function (figure) {
        var img = figure.querySelector('img');
        if (!img) return;
        var src = img.getAttribute('src');
        if (!src) {
            img.style.display = 'none';
            return;
        }
        var showImage = function () {
            img.style.display = '';
            figure.style.backgroundImage = 'none';
        };
        var hideImage = function () {
            img.style.display = 'none';
        };
        img.addEventListener('load', showImage);
        img.addEventListener('error', hideImage);
        if (img.complete) {
            if (img.naturalWidth > 0) {
                showImage();
            } else {
                hideImage();
            }
        }
    });
</script>
@endsection
