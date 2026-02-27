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
                <form action="{{ route('ads.store.banner') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
                            <option value="468x60">468x60</option>
                            <option value="728x90">728x90</option>
                            <option value="300x250">300x250</option>
                            <option value="160x600">160x600</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.img') }}</label>
                        <input type="file" name="img" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
