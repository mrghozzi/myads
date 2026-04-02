@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/03.jpg') }}) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/link_ads.png') }}" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title">{{ __('messages.list') }}&nbsp;{{ __('messages.textads') }}</p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>{{ __('messages.yhtierbpyaci') }}</b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="{{ route('legacy.l_code') }}">
          <i class="fa fa-code" aria-hidden="true"></i>&nbsp;{{ __('messages.codes') }}&nbsp;{{ __('messages.textads') }}
          </a>
          <!-- /BUTTON -->
       </div>
</div>

<div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions" >
      <a class="button tertiary " href="{{ route('legacy.state', ['ty' => 'link', 'st' => 'vu']) }}">&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
      </div>
      <p class="text-sticker">
          <!-- TEXT STICKER ICON -->
          <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
          </svg>
          <!-- TEXT STICKER ICON -->
          {{ __('messages.you_have') }}&nbsp;{{ $user->nlink }}&nbsp;{{ __('messages.ptcyta') }}
      </p>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="{{ route('ads.promote', ['p' => 'link']) }}" class="button secondary" style="color: #fff;" >
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
               <th>{{ __('messages.Statu') ?? 'Statu' }}</th>
              </tr>
             </thead>
             <tbody>
              @foreach($links as $link)
              @php
                  $bnname = mb_strlen($link->name, 'utf8') > 25 ? mb_substr($link->name, 0, 25) . "&nbsp;..." : $link->name;
                  $fgft = $link->statu == 1 ? "ON" : "OFF";
                  // Calculate Vu count (visits where pid=link_id and t_name='link')
                  $vuCount = \App\Models\State::where('pid', $link->id)->where('t_name', 'link')->count();
              @endphp
              <tr>
                <td>{{ $link->id }}</td>
                <td>{!! $bnname !!}<hr />
                  <div style="display: flex; align-items: center; gap: 8px;">
                      <a href="{{ route('ads.links.edit', $link->id) }}" class="btn btn-success"><i class="fa fa-edit "></i></a>
                      <form action="{{ route('ads.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_link') }}');" style="margin: 0;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger" aria-label="{{ __('messages.delete_link') }}" style="display: inline-flex; align-items: center; justify-content: center;">
                              <i class="fa fa-ban "></i>
                          </button>
                      </form>
                  </div></td>
                <td><a href="{{ route('legacy.state', ['ty' => 'link', 'id' => $link->id]) }}" class="btn btn-warning" >{{ $vuCount }}</a></td>
                <td><a href="{{ route('legacy.state', ['ty' => 'clik', 'id' => $link->id]) }}" class="btn btn-primary" >{{ $link->clik }}</a></td>
                <td>{{ $fgft }}</td>
              </tr>
              @endforeach
             </tbody>
        </table>
    </div>
  </div>
</div>
@endsection
