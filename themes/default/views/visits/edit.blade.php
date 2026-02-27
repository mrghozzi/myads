@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.edit_website') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ route('visits.index') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('visits.update', $site->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $site->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.url') }}</label>
                        <input type="url" name="url" class="form-control" value="{{ $site->url }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.duration') }}</label>
                        <select name="tims" class="form-control">
                            <option value="1" {{ $site->tims == 1 ? 'selected' : '' }}>10s (1 Point)</option>
                            <option value="2" {{ $site->tims == 2 ? 'selected' : '' }}>20s (2 Points)</option>
                            <option value="3" {{ $site->tims == 3 ? 'selected' : '' }}>30s (5 Points)</option>
                            <option value="4" {{ $site->tims == 4 ? 'selected' : '' }}>60s (10 Points)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
