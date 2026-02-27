@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop">
    <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/state_banner.png') }}) no-repeat 50%; background-size: cover;">
        <!-- ACHIEVEMENT BOX INFO WRAP -->
        <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/statistics.png') }}" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
                <!-- ACHIEVEMENT BOX TITLE -->
                <p class="achievement-box-title">{!! $title !!}</p>
                <!-- /ACHIEVEMENT BOX TITLE -->

                <!-- ACHIEVEMENT BOX TEXT -->
                <p class="achievement-box-text"><b>{{ $subtitle }}</b></p>
                <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
        </div>
        <!-- /ACHIEVEMENT BOX INFO WRAP -->

        <!-- BUTTON -->
        <a class="button white-solid" href="{{ $backUrl }}">
            <i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;{{ __('messages.go_back') }}
        </a>
        <!-- /BUTTON -->
    </div>
</div>

<div class="grid grid">
    <div class="grid-column">
        <div class="widget-box">
             <table id="tablepagination" class="table table-borderless table-hover">
                 <thead>
                  <tr>
                   <th>#ID</th>
                   <th>{{ __('messages.url_link') ?? 'Url' }}</th>
                   <th>{{ __('messages.time') ?? 'Time' }}</th>
                   <th>{{ __('messages.browser') ?? 'Browser' }}</th>
                   <th>{{ __('messages.platform') ?? 'Platform' }}</th>
                   <th>{{ __('messages.ip') ?? 'Ip' }}</th>
                  </tr>
                 </thead>
                 <tbody>
                    @forelse($states as $state)
                        <tr>
                            <td>{{ $state->id }}</td>
                            <td>
                                @if($state->r_link == 'N')
                                    <span class="btn btn-danger disabled"><i class="fa-solid fa-link-slash"></i></span>
                                @else
                                    <a class="btn btn-success" href="{{ $state->r_link }}" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>
                                @endif
                            </td>
                            <td>
                                {{ date('d, M Y', $state->r_date) }}<br>
                                <i class="fa-solid fa-clock"></i> {{ date('H:i:s', $state->r_date) }}
                            </td>
                            <td>
                                {{ $state->browser['name'] }}<br>
                                {{ $state->browser['version'] }}
                            </td>
                            <td>{{ $state->browser['platform'] }}</td>
                            <td>
                                <a class="btn btn-primary" href="http://ip.is-best.net/?ip={{ $state->v_ip }}" target="_blank"><i class="fa-solid fa-file-invoice fa-bounce"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                 </tbody>
             </table>
        </div>
    </div>
</div>
@endsection
