@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.edit_banner') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ route('ads.banners.index') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('ads.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $banner->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.url') }}</label>
                        <input type="url" name="url" class="form-control" value="{{ $banner->url }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.size') }}</label>
                        <select name="px" class="form-control">
                            <option value="468x60" {{ $banner->px == '468x60' ? 'selected' : '' }}>468x60</option>
                            <option value="728x90" {{ $banner->px == '728x90' ? 'selected' : '' }}>728x90</option>
                            <option value="300x250" {{ $banner->px == '300x250' ? 'selected' : '' }}>300x250</option>
                            <option value="160x600" {{ $banner->px == '160x600' ? 'selected' : '' }}>160x600</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.img') }}</label>
                        <input type="text" name="img" class="form-control" value="{{ $banner->img }}" required>
                        <small>Enter image URL</small>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
