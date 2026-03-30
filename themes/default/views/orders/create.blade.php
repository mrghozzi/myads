@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="create-order-icon">
    <p class="section-banner-title">{{ __('messages.post_new_order') }}</p>
    <p class="section-banner-text">{{ __('messages.fill_order_details') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column"></div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.order_details') }}</p>
            <div class="widget-box-content">
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 20px; border-radius: 12px; padding: 15px; background: rgba(233, 75, 95, 0.1); border: 1px solid rgba(233, 75, 95, 0.2); color: #e94b5f;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('errMSG'))
                    <div class="alert alert-danger" style="margin-bottom: 20px; border-radius: 12px; padding: 15px; background: rgba(233, 75, 95, 0.1); border: 1px solid rgba(233, 75, 95, 0.2); color: #e94b5f;">
                        {{ session('errMSG') }}
                    </div>
                @endif

                <form class="form" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="order-title">{{ __('messages.title') }}</label>
                                    <input type="text" id="order-title" name="title" value="{{ old('title') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="order-category">{{ __('messages.category') }}</label>
                                    <input type="text" id="order-category" name="category" placeholder="e.g. Programming, SEO, Design" value="{{ old('category') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="order-budget">{{ __('messages.budget') }}</label>
                                    <input type="text" id="order-budget" name="budget" placeholder="e.g. $50 or 1000 PTS" value="{{ old('budget') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="order-description">{{ __('messages.description') }}</label>
                                    <textarea id="order-description" name="description" style="height: 200px;" required>{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 24px; text-align: center;">
                        <button type="submit" class="button primary">{{ __('messages.publish_order') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>
@endsection
