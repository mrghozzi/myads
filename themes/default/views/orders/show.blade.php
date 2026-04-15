@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="order-detail-icon">
    <p class="section-banner-title">{{ __('messages.order_details') }}</p>
    <p class="section-banner-text">{{ __('messages.viewing_order_request') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <div class="widget-box">
             <p class="widget-box-title">{{ __('messages.client_info') }}</p>
             <div class="widget-box-content">
                <div class="user-short-description">
                    <a class="user-short-description-avatar user-avatar medium {{ $order->user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $order->user->username) }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-68-74" data-src="{{ $order->user ? $order->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                        </div>
                    </a>
                    <p class="user-short-description-title"><a href="{{ route('profile.show', $order->user->username) }}">{{ $order->user->username }}</a></p>
                    <p class="user-short-description-text">{{ $order->user->isOnline() ? __('messages.online') : __('messages.offline') }}</p>
                </div>
                 @auth
                    <div class="widget-box-actions" style="margin-top: 20px;">
                        @if(auth()->id() != $order->uid)
                            <a href="{{ url('/messages/' . \App\Models\Message::encodeConversationRouteKey(auth()->user(), $order->uid)) }}" class="button primary full">
                                <i class="fa fa-envelope"></i>&nbsp;{{ __('messages.send_message') }}
                            </a>
                        @endif

                        @if(($order->uid == auth()->id() || auth()->user()->isAdmin()) && $order->statu == 1)
                            <form action="{{ route('orders.close', $order->id) }}" method="POST" style="margin-top: 10px;">
                                @csrf
                                <button type="submit" class="button secondary full" onclick="return confirm('{{ __('messages.confirm_close_order') }}')">
                                    <i class="fa fa-lock"></i>&nbsp;{{ __('messages.close_order') }}
                                </button>
                            </form>
                        @endif

                        <div style="margin-top: 20px; border-top: 1px solid #eaeaf5; padding-top: 15px;">
                            <a href="{{ route('report.index', ['order' => $order->id]) }}" class="button white full" style="margin-bottom: 10px;">
                                <i class="fa fa-flag"></i>&nbsp;{{ __('messages.report_topic') }}
                            </a>
                            @if($order->uid != auth()->id())
                                <a href="{{ route('report.index', ['user' => $order->uid]) }}" class="button white full">
                                    <i class="fa fa-user"></i>&nbsp;{{ __('messages.report_publisher') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endauth
             </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        @php
            $activity = $order->statusRecord;
        @endphp
        
        @if($activity)
            @include('theme::partials.activity.render', ['activity' => $activity, 'detailView' => true])
            
            <!-- COMMENTS SECTION -->
            <div class="widget-box" style="margin-top: 16px;">
                <p class="widget-box-title">{{ __('messages.replies_and_offers') }}</p>
                <div class="widget-box-content">
                    {{-- Here we can integrate the standard comment system for status with type 6 --}}
                    @include('theme::partials.activity.comments', [
                        'comments' => \App\Models\Option::where('o_parent', $order->id)->where('o_type', 'o_order')->orderBy('id', 'desc')->get(),
                        'id' => $order->id,
                        'type' => 'order',
                        'limit' => 100,
                        'order' => $order,
                        'hide_form' => $order->statu == 0
                    ])
                </div>
            </div>
        @else
            <div class="widget-box">
                <h1 class="widget-box-title">{{ $order->title }}</h1>
                <div class="widget-box-content">
                    <p>{!! nl2br(e($order->description)) !!}</p>
                    <hr>
                    <p><strong>{{ __('messages.budget') }}:</strong> {{ $order->budget }}</p>
                    <p><strong>{{ __('messages.category') }}:</strong> {{ $order->category }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
