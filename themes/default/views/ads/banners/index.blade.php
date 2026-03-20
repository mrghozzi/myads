@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/03.jpg') }}) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title">{{ __('messages.list') }}&nbsp;{{ __('messages.bannads') }}</p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>{{ __('messages.yhtierbpyaci') }}</b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="{{ route('legacy.b_code') }}">
          <i class="fa fa-code" aria-hidden="true"></i>&nbsp;{{ __('messages.codes') }}&nbsp;{{ __('messages.bannads') }}
          </a>
          <!-- /BUTTON -->
       </div>
</div>

<div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions" >
      @if(isset($site_settings->e_links) && $site_settings->e_links == 1)
      <a href="https://github.com/mrghozzi/myads/wiki/{{ __('messages.list') }}&nbsp;{{ __('messages.bannads') }}" class="button primary " target="_blank">&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
      &nbsp;
      @endif
      <a class="button tertiary " href="{{ route('legacy.state', ['ty' => 'banner', 'st' => 'vu']) }}">&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
      </div>
      <p class="text-sticker">
          <!-- TEXT STICKER ICON -->
          <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
          </svg>
          <!-- TEXT STICKER ICON -->
          {{ __('messages.you_have') }}&nbsp;{{ $user->nvu }}&nbsp;{{ __('messages.ptvyba') }}
      </p>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="{{ route('ads.promote', ['p' => 'banners']) }}" class="button secondary" style="color: #fff;" >
        <i class="fa fa-plus nav_icon"></i>&nbsp;
        {{ __('messages.add') }}
        </a>
        <!-- /BUTTON -->
      </div>
      <!-- /SECTION FILTERS BAR ACTIONS -->
</div>

<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
        <table id="tablepagination" class="table table table-hover">
            <thead>
                <tr>
                  <th>#ID</th>
                  <th>{{ __('messages.name') ?? 'Name' }}</th>
                  <th>{{ __('messages.Vu') ?? 'Vu' }}</th>
                  <th>{{ __('messages.Clik') ?? 'Clik' }}</th>
                  <th>{{ __('messages.size') ?? 'Size' }}</th>
                  <th>{{ __('messages.Statu') ?? 'Statu' }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($banners as $banner)
            @php
                $bnname = mb_strlen($banner->name, 'utf8') > 25 ? mb_substr($banner->name, 0, 25) . "&nbsp;..." : $banner->name;
                $fgft = $banner->statu == 1 ? "ON" : "OFF";
            @endphp
            <tr>
              <td>{{ $banner->id }}</td>
              <td>{!! $bnname !!}<hr />
               <div style="display: flex; align-items: center; gap: 8px;">
                   <a href="{{ route('ads.banners.edit', $banner->id) }}" class="btn btn-success"><i class="fa fa-edit "></i></a>
                   <form action="{{ route('ads.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_banner') }}');" style="margin: 0;">
                       @csrf
                       @method('DELETE')
                       <button type="submit" class="btn btn-danger" aria-label="{{ __('messages.delete_banner') }}" style="display: inline-flex; align-items: center; justify-content: center;">
                           <i class="fa fa-ban "></i>
                       </button>
                   </form>
               </div>
              </td>
              <td><a href="{{ route('legacy.state', ['ty' => 'banner', 'id' => $banner->id]) }}" class="btn btn-warning" >{{ $banner->vu }}</a></td>
              <td><a href="{{ route('legacy.state', ['ty' => 'vu', 'id' => $banner->id]) }}" class="btn btn-primary" >{{ $banner->clik }}</a></td>
              <td>{{ $banner->px }}</td>
              <td>{{ $fgft }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
  </div>
</div>
@endsection
