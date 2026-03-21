@extends('theme::layouts.master')

@section('content')
@php
    $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
    $owner = $product->user;
    $ownerAvatar = $owner && $owner->img ? (\Illuminate\Support\Str::startsWith($owner->img, ['http://', 'https://']) ? $owner->img : asset($owner->img)) : theme_asset('img/avatar/default.png');
    $pendingCounts = $pendingCounts ?? collect();
    $articleAuthors = $articleAuthors ?? collect();
    $currentArticle = $article ?? null;
    $currentTopicPendingCount = $currentArticle
        ? \App\Models\Option::where('o_type', 'knowledgebase')->where('o_mode', $product->name)->where('name', $currentArticle->name)->where('o_order', 1)->count()
        : 0;
    $shellTitle = $currentArticle ? $currentArticle->name : $product->name;
    $shellSummary = $currentArticle
        ? \Illuminate\Support\Str::limit(strip_tags($currentArticle->o_valuer), 240)
        : \Illuminate\Support\Str::limit($product->o_valuer, 240);
    $newTopicUrl = route('kb.index', $product->name) . '#kb-new-topic';
@endphp

@include('theme::store.partials.page-shell-styles')

<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title">{{ __('messages.knowledgebase') }}</p>
    <p class="section-banner-text"></p>
</div>

<div class="section-header">
    <div class="section-header-info">
        <p class="section-pretitle">{{ __('messages.store') }}</p>
        <h2 class="section-title">{{ $product->name }}</h2>
    </div>
    <div class="section-header-actions">
        <a class="section-header-subsection" href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a>
        <p class="section-header-subsection">{{ __('messages.knowledgebase') }}</p>
    </div>
</div>

<div class="knowledgebase-page">
    <div class="widget-box kb-shell-card no-padding">
        <div class="kb-hero">
            <div class="kb-hero__main">
                <div class="kb-hero__media">
                    <img src="{{ $productImage }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ theme_asset('img/error_plug.png') }}';">
                </div>
                <div class="kb-hero__content">
                    <div class="kb-badge-row">
                        @if($product->o_order > 0)
                            <span class="kb-pill"><strong>{{ $product->o_order }}</strong> {{ __('messages.points') }}</span>
                        @else
                            <span class="kb-pill">{{ __('messages.free') }}</span>
                        @endif
                        <span class="kb-pill"><strong>{{ $articleTotal ?? 0 }}</strong> {{ __('messages.topics') }}</span>
                        <span class="kb-pill"><strong>{{ $pendingTotal ?? 0 }}</strong> {{ __('messages.pending') }}</span>
                    </div>
                    <h3 class="kb-title">{{ $shellTitle }}</h3>
                    <p class="kb-subtitle">{{ $shellSummary }}</p>
                    <div class="kb-stat-grid">
                        <div class="kb-stat-card"><span>{{ __('messages.topics') }}</span><strong>{{ $articleTotal ?? 0 }}</strong></div>
                        <div class="kb-stat-card"><span>{{ __('messages.pending') }}</span><strong>{{ $pendingTotal ?? 0 }}</strong></div>
                        <div class="kb-stat-card"><span>{{ __('messages.current') }}</span><strong>{{ $currentArticle ? ('#' . $currentArticle->id) : __('messages.preview') }}</strong></div>
                        <div class="kb-stat-card"><span>{{ __('messages.seller') }}</span><strong>{{ $owner ? $owner->username : __('messages.unknown') }}</strong></div>
                    </div>
                    <div class="kb-shell-nav">
                        <a class="kb-shell-nav__link {{ $mode === 'list' ? 'active' : '' }}" href="{{ route('kb.index', $product->name) }}"><i class="fa fa-list-ul" aria-hidden="true"></i>{{ __('messages.topics') }}</a>
                        <a class="kb-shell-nav__link {{ $mode === 'create' ? 'active' : '' }}" href="{{ $newTopicUrl }}"><i class="fa fa-plus" aria-hidden="true"></i>{{ __('messages.add') }}</a>
                        @if($currentArticle)
                            <a class="kb-shell-nav__link {{ $mode === 'show' ? 'active' : '' }}" href="{{ route('kb.show', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>{{ __('messages.topic') }}</a>
                            <a class="kb-shell-nav__link {{ $mode === 'edit' ? 'active' : '' }}" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>{{ $canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit') }}</a>
                            <a class="kb-shell-nav__link {{ $mode === 'pending' ? 'active' : '' }}" href="{{ route('kb.pending', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-hourglass-half" aria-hidden="true"></i>{{ __('messages.pending') }} <strong>{{ $currentTopicPendingCount }}</strong></a>
                            <a class="kb-shell-nav__link {{ $mode === 'history' ? 'active' : '' }}" href="{{ route('kb.history', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-history" aria-hidden="true"></i>{{ __('messages.history') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="kb-aside">
                <div class="kb-aside-card">
                    <div class="kb-aside-card__header">
                        <div>
                            <p class="kb-aside-card__label">{{ __('messages.details') }}</p>
                            <p class="kb-aside-card__title">{{ __('messages.knowledgebase') }}</p>
                        </div>
                        <a class="button small tertiary" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                    </div>
                    <div class="user-status" style="margin-top: 18px;">
                        @if($owner)
                            <a class="user-status-avatar" href="{{ route('profile.show', $owner->username) }}">
                                <div class="user-avatar small no-outline {{ $owner->isOnline() ? 'online' : 'offline' }}">
                                    <div class="user-avatar-content"><div class="hexagon-image-30-32" data-src="{{ $ownerAvatar }}"></div></div>
                                    <div class="user-avatar-progress-border"><div class="hexagon-border-40-44"></div></div>
                                </div>
                            </a>
                        @endif
                        <p class="user-status-title medium">@if($owner)<a class="bold" href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>@else{{ __('messages.unknown') }}@endif</p>
                        <p class="user-status-text small">{{ __('messages.seller') }}</p>
                    </div>
                    <div class="kb-aside-card__meta">
                        <div class="kb-meta-row"><span>{{ __('messages.seller') }}</span><strong>{{ $owner ? $owner->username : __('messages.unknown') }}</strong></div>
                        @if($currentArticle)
                            <div class="kb-meta-row"><span>{{ __('messages.publisher') }}</span><strong>{{ $articleAuthor ? $articleAuthor->username : __('messages.guest') }}</strong></div>
                        @endif
                        <div class="kb-meta-row"><span>{{ __('messages.topics') }}</span><strong>{{ $articleTotal ?? 0 }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.pending') }}</span><strong>{{ $pendingTotal ?? 0 }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($mode === 'list')
        <div class="widget-box kb-helper-card" id="kb-new-topic">
            <p class="widget-box-title">{{ __('messages.add') }} {{ __('messages.topic') }}</p>
            <div class="widget-box-content">
                <form method="GET" action="{{ route('kb.index', $product->name) }}" class="form">
                    <div class="form-row split">
                        <div class="form-item"><div class="form-input"><input type="text" name="st" value="{{ request('st') }}" placeholder="{{ __('messages.name') }}" required></div></div>
                        <div class="form-item"><button type="submit" class="button secondary">{{ __('messages.add') }}</button></div>
                    </div>
                </form>
            </div>
        </div>

        @if($articles->isEmpty())
            <div class="kb-empty-state">{{ __('messages.no_post') }}</div>
        @else
            <div class="kb-topic-grid">
                @foreach($articles as $item)
                    @php
                        $pending = $pendingCounts[$item->name] ?? 0;
                        $cardAuthor = ((int) $item->o_parent > 0) ? ($articleAuthors[$item->o_parent] ?? null) : null;
                        $canManageTopic = auth()->check() && (auth()->id() == $product->o_parent || auth()->user()->isAdmin() || auth()->id() == $item->o_parent);
                        $reportKey = 'kbtopic' . $item->id;
                    @endphp
                    <article class="widget-box kb-topic-card">
                        <div class="kb-topic-card__header">
                            <div>
                                <p class="kb-topic-card__label">#{{ $item->id }}</p>
                                <h3 class="kb-topic-card__title"><a href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}">{{ $item->name }}</a></h3>
                            </div>
                            <div class="kb-action-menu" data-activity-menu-wrap>
                                <button type="button" class="kb-action-menu__trigger" data-activity-menu-trigger data-activity-menu-type="actions" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                                <div class="simple-dropdown kb-action-menu__panel" data-activity-menu-panel>
                                    <a class="simple-dropdown-link" href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="navigator.clipboard.writeText('{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}'); alert('{{ __('messages.link_copied') }}');"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</button>
                                    @if(auth()->check() && !$canManageTopic)
                                        <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportPost({{ $item->id }}, 205, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_topic') }}</button>
                                        @if($cardAuthor && auth()->id() != $cardAuthor->id)
                                            <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportUser({{ $cardAuthor->id }}, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <p class="kb-topic-card__summary">{{ \Illuminate\Support\Str::limit(strip_tags($item->o_valuer), 180) }}</p>
                        <div class="kb-topic-card__meta">
                            <span class="kb-pill">{{ __('messages.author') }}: {{ $cardAuthor ? $cardAuthor->username : __('messages.guest') }}</span>
                            <span class="kb-pill">{{ __('messages.pending') }}: <strong>{{ $pending }}</strong></span>
                        </div>
                        <div id="report{{ $reportKey }}" class="store-inline-report"></div>
                        <div class="kb-topic-card__footer">
                            <a class="button secondary" href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    @endif

    @if($mode === 'create' || $mode === 'edit')
        <div class="kb-editor-layout">
            <div class="widget-box kb-form-card">
                <p class="widget-box-title">{{ $mode === 'edit' ? ($canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit')) : __('messages.add') . ' ' . __('messages.topic') }}</p>
                <div class="widget-box-content">
                    @if(session('kb_error'))
                        <div class="alert alert-danger">{{ session('kb_error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('kb.store') }}">
                        @csrf
                        <input type="hidden" name="store" value="{{ $product->name }}">
                        @if($mode === 'create')
                            <div class="form-row">
                                <div class="form-item">
                                    <div class="form-input">
                                        <input type="text" name="name" value="{{ $articleName ?? '' }}" placeholder="{{ __('messages.name') }}" required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="name" value="{{ $articleName }}">
                            <div class="form-row">
                                <div class="form-item">
                                    <div class="form-input">
                                        <input type="text" value="{{ $articleName }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input">
                                    <textarea id="kb-editor" name="txt" rows="15" required>{{ old('txt', $editorText ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-row split">
                            <div class="form-item"><img src="{{ route('kb.captcha') }}" id="kb-captcha" alt="captcha" style="height: 30px; cursor: pointer;"></div>
                            <div class="form-item"><div class="form-input"><input type="text" name="capt" required></div></div>
                        </div>
                        <div class="form-row">
                            <div class="form-item"><button class="button primary" type="submit">{{ __('messages.save') }}</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="widget-box kb-helper-card">
                <p class="widget-box-title">{{ __('messages.details') }}</p>
                <div class="widget-box-content">
                    <div class="kb-aside-card__meta">
                        <div class="kb-meta-row"><span>{{ __('messages.seller') }}</span><strong>{{ $owner ? $owner->username : __('messages.unknown') }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.topics') }}</span><strong>{{ $articleTotal ?? 0 }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.pending') }}</span><strong>{{ $pendingTotal ?? 0 }}</strong></div>
                    </div>
                    <div class="kb-side-card__actions">
                        <a class="button secondary" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                        <a class="button tertiary" href="{{ route('kb.index', $product->name) }}"><i class="fa fa-list-ul" aria-hidden="true"></i>&nbsp;{{ __('messages.topics') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($mode === 'show')
        @php
            $reportKey = 'kbcurrent' . $article->id;
            $canReportTopic = auth()->check() && !$canManageCurrentArticle;
            $canReportPublisher = $canReportTopic && $articleAuthor && auth()->id() != $articleAuthor->id;
        @endphp
        <div class="kb-topic-layout">
            <div class="widget-box kb-main-card">
                <div class="widget-box-content">
                    <div class="kb-main-card__header">
                        <div>
                            <p class="kb-topic-card__label">{{ __('messages.topic') }}</p>
                            <h3 class="widget-box-title">{{ $article->name }}</h3>
                            <p class="kb-main-card__subtitle">
                                {{ __('messages.current') }} #{{ $article->id }}
                                @if($articleAuthor)
                                    &nbsp;|&nbsp;{{ __('messages.publisher') }}: {{ $articleAuthor->username }}
                                @else
                                    &nbsp;|&nbsp;{{ __('messages.publisher') }}: {{ __('messages.guest') }}
                                @endif
                            </p>
                        </div>
                        <div class="kb-action-menu" data-activity-menu-wrap>
                            <button type="button" class="kb-action-menu__trigger" data-activity-menu-trigger data-activity-menu-type="actions" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="simple-dropdown kb-action-menu__panel" data-activity-menu-panel>
                                <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="navigator.clipboard.writeText('{{ route('kb.show', ['name' => $product->name, 'article' => $article->name]) }}'); alert('{{ __('messages.link_copied') }}');"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</button>
                                <a class="simple-dropdown-link" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                                @if($canReportTopic)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportPost({{ $article->id }}, 205, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_topic') }}</button>
                                @endif
                                @if($canReportPublisher)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportUser({{ $articleAuthor->id }}, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="kb-article-body">{!! $article->o_valuer !!}</div>
                    <div id="report{{ $reportKey }}" class="store-inline-report"></div>
                </div>
            </div>
            <div class="widget-box kb-side-card">
                <p class="widget-box-title">{{ __('messages.actions') }}</p>
                <div class="widget-box-content">
                    <div class="kb-aside-card__meta">
                        <div class="kb-meta-row"><span>{{ __('messages.pending') }}</span><strong>{{ $currentTopicPendingCount }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.publisher') }}</span><strong>{{ $articleAuthor ? $articleAuthor->username : __('messages.guest') }}</strong></div>
                    </div>
                    <div class="kb-side-card__actions">
                        <a class="button secondary" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ $canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit') }}</a>
                        <a class="button tertiary" href="{{ route('kb.pending', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-hourglass-half" aria-hidden="true"></i>&nbsp;{{ __('messages.pending') }}</a>
                        <a class="button tertiary" href="{{ route('kb.history', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-history" aria-hidden="true"></i>&nbsp;{{ __('messages.history') }}</a>
                        <a class="button white" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ $product->name }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($mode === 'pending' || $mode === 'history')
        @php
            $reportKey = 'kbcurrent' . $article->id;
            $canReportTopic = auth()->check() && !$canManageCurrentArticle;
            $canReportPublisher = $canReportTopic && $articleAuthor && auth()->id() != $articleAuthor->id;
        @endphp
        <div class="kb-review-layout">
            <div class="widget-box kb-main-card">
                <div class="widget-box-content">
                    <div class="kb-main-card__header">
                        <div>
                            <p class="kb-topic-card__label">{{ __('messages.current') }}</p>
                            <h3 class="widget-box-title">{{ $article->name }}</h3>
                            <p class="kb-main-card__subtitle">
                                #{{ $article->id }}
                                @if($articleAuthor)
                                    &nbsp;|&nbsp;{{ __('messages.publisher') }}: {{ $articleAuthor->username }}
                                @endif
                            </p>
                        </div>
                        <div class="kb-action-menu" data-activity-menu-wrap>
                            <button type="button" class="kb-action-menu__trigger" data-activity-menu-trigger data-activity-menu-type="actions" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="simple-dropdown kb-action-menu__panel" data-activity-menu-panel>
                                <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="navigator.clipboard.writeText('{{ route('kb.show', ['name' => $product->name, 'article' => $article->name]) }}'); alert('{{ __('messages.link_copied') }}');"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</button>
                                @if($canReportTopic)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportPost({{ $article->id }}, 205, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_topic') }}</button>
                                @endif
                                @if($canReportPublisher)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportUser({{ $articleAuthor->id }}, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="kb-article-body">{!! $article->o_valuer !!}</div>
                    <div id="report{{ $reportKey }}" class="store-inline-report"></div>
                </div>
            </div>
            <div class="widget-box kb-side-card kb-review-card__table">
                <p class="widget-box-title">{{ $mode === 'pending' ? __('messages.pending') : __('messages.history') }}</p>
                <div class="widget-box-content">
                    @if($entries->isEmpty())
                        <div class="kb-empty-state">{{ __('messages.no_post') }}</div>
                    @else
                        <form method="POST" action="{{ route('kb.approve') }}">
                            @csrf
                            <input type="hidden" name="store" value="{{ $product->name }}">
                            <input type="hidden" name="article" value="{{ $article->name }}">
                            <div class="table-responsive">
                                <table id="tablepagination" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>{{ __('messages.topic') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($entries as $entry)
                                            <tr>
                                                <td><input type="radio" name="entry" value="{{ $entry->id }}" required> #{{ $entry->id }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($entry->o_valuer), 220) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($isAuthorized)
                                <button type="submit" class="button primary" onclick="return confirm('{{ __('messages.aystqwbc') }}')">{{ $mode === 'pending' ? __('messages.replacing') : __('messages.recovery') }}</button>
                            @endif
                        </form>
                    @endif
                    <div class="kb-side-card__actions">
                        <a class="button tertiary" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ $canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit') }}</a>
                        <a class="button white" href="{{ route('kb.show', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ __('messages.topic') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($mode === 'create' || $mode === 'edit')
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/{{ app()->getLocale() }}.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var textarea = document.getElementById('kb-editor');
        if (textarea) {
            sceditor.create(textarea, {
                format: 'xhtml',
                locale: '{{ app()->getLocale() }}',
                emoticons: {
                    dropdown: {
                        @foreach(\App\Models\Emoji::limit(10)->get() as $emoji)
                        '{{ $emoji->name }}': '{{ asset($emoji->img) }}',
                        @endforeach
                    },
                    more: {
                        @foreach(\App\Models\Emoji::skip(10)->limit(10)->get() as $emoji)
                        '{{ $emoji->name }}': '{{ asset($emoji->img) }}',
                        @endforeach
                    }
                },
                style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
            });
        }
        var captcha = document.getElementById('kb-captcha');
        if (captcha) {
            captcha.addEventListener('click', function () {
                this.src = '{{ route('kb.captcha') }}?t=' + Date.now();
            });
        }
    });
</script>
@endpush
@endif
@endsection
