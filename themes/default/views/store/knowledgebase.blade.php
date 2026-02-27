@extends('theme::layouts.master')

@section('content')
@php
    $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
    $owner = $product->user;
    $ownerAvatar = $owner && $owner->img ? ( \Illuminate\Support\Str::startsWith($owner->img, ['http://', 'https://']) ? $owner->img : asset($owner->img) ) : theme_asset('img/avatar/default.png');
    $pendingCounts = $pendingCounts ?? collect();
@endphp
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
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
<div class="grid">
    <div class="widget-box no-padding">
        <div class="widget-box-status">
            <div class="widget-box-status-content">
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
                            @if($owner)
                                <a href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>
                            @else
                                {{ __('messages.unknown') }}
                            @endif
                        </p>
                    </div>
                </div>
                <hr />
            </div>
        </div>
    </div>
</div>

@if($mode === 'list')
<div class="section-filters-bar v6">
    <div class="section-filters-bar-actions">
        <form method="GET" action="{{ route('kb.index', $product->name) }}" class="form">
            <div class="form-row split">
                <div class="form-item">
                    <div class="form-input">
                        <input type="text" name="st" value="{{ request('st') }}" placeholder="{{ __('messages.name') }}" required>
                    </div>
                </div>
                <div class="form-item">
                    <button type="submit" class="button secondary">{{ __('messages.add') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="grid">
    <div class="widget-box">
        <div class="table-responsive">
            <table id="tablepagination" class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>{{ __('messages.topics') }}</th>
                        <th>{{ __('messages.pending') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $item)
                        @php
                            $pending = $pendingCounts[$item->name] ?? 0;
                        @endphp
                        <tr>
                            <td>#{{ $item->id }}</td>
                            <td>
                                <a href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}">{{ $item->name }}</a>
                                <div class="text-sticker">{{ \Illuminate\Support\Str::limit(strip_tags($item->o_valuer), 120) }}</div>
                            </td>
                            <td><span class="badge badge-info"><b>{{ $pending }}</b></span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">{{ __('messages.no_post') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if($mode === 'create' || $mode === 'edit')
<div class="grid">
    <div class="widget-box">
        <h3 class="widget-box-title">{{ $mode === 'edit' ? __('messages.edit') : __('messages.add') }}</h3>
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
                <div class="form-item">
                    <img src="{{ route('kb.captcha') }}" id="kb-captcha" alt="captcha" style="height: 30px; cursor: pointer;">
                </div>
                <div class="form-item">
                    <div class="form-input">
                        <input type="text" name="capt" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-item">
                    <button class="button primary" type="submit">{{ __('messages.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@if($mode === 'show')
<div class="section-filters-bar v6">
    <div class="section-filters-bar-actions">
        <a class="button secondary" href="{{ route('kb.pending', ['name' => $product->name, 'article' => $article->name]) }}">
            {{ __('messages.pending') }}
            <span class="badge badge-light"><b>{{ $pendingCount }}</b></span>
        </a>
    </div>
    <div class="section-filters-bar-actions">
        <a class="button primary" href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a>
        <a class="button secondary" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $article->name]) }}">
            <i class="fa fa-pencil-square-o"></i> {{ __('messages.edit') }}
        </a>
        <a class="button secondary" href="{{ route('kb.history', ['name' => $product->name, 'article' => $article->name]) }}">
            <i class="fa fa-history"></i> {{ __('messages.history') }}
        </a>
    </div>
</div>
<div class="grid">
    <div class="widget-box">
        <h3 class="widget-box-title">{{ $article->name }}</h3>
        <div class="widget-box-content">
            {!! $article->o_valuer !!}
        </div>
    </div>
</div>
@endif

@if($mode === 'pending' || $mode === 'history')
<div class="grid">
    <div class="widget-box">
        <h3 class="widget-box-title">{{ $article->name }}</h3>
        <div class="widget-box-content">
            {!! $article->o_valuer !!}
        </div>
    </div>
    <div class="widget-box">
        <h3 class="widget-box-title">{{ $mode === 'pending' ? __('messages.pending') : __('messages.history') }}</h3>
        @if($entries->isEmpty())
            <p>{{ __('messages.no_post') }}</p>
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
                                    <td>
                                        <input type="radio" name="entry" value="{{ $entry->id }}" required>
                                        #{{ $entry->id }}
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($entry->o_valuer), 220) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($isAuthorized)
                    <button type="submit" class="button primary" onclick="return confirm('{{ __('messages.aystqwbc') }}')">
                        {{ $mode === 'pending' ? __('messages.replacing') : __('messages.recovery') }}
                    </button>
                @endif
            </form>
        @endif
    </div>
</div>
@endif

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
