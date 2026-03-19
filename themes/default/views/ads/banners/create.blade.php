@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.add_banner') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ route('ads.index') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('ads.banners.store') }}" method="POST">
                    @csrf
                    @php($bannerSizes = \App\Support\BannerSizeCatalog::ordered())
                    <div class="form-group">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.url') }}</label>
                        <input type="url" name="url" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.size') }}</label>
                        <select name="px" class="form-control">
                            @foreach($bannerSizes as $size)
                                <option value="{{ $size['value'] }}">{{ $size['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.img') }}</label>
                        <input type="text" name="img" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
