@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/03.jpg') }}) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/exchange.png') }}" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title">{{ __('messages.list') }}&nbsp;{{ __('messages.exvisit') }}</p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>{{ __('messages.ctevbtexp') }}</b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" onClick="window.open('{{ route('visits.surf') }}', 'SurfWindow', 'width=1024,height=768');" href="javascript:void(0);" >
          <i class="fa fa-exchange nav_icon"></i>&nbsp;{{ __('messages.exvisit') }}
          </a>
          <!-- /BUTTON -->
       </div>
</div>

<div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions" >
      @if(isset($site_settings->e_links) && $site_settings->e_links == 1)
      <a href="https://github.com/mrghozzi/myads/wiki/{{ __('messages.list') }}&nbsp;{{ __('messages.exvisit') }}" class="button primary " target="_blank">&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
      @endif
      </div>
      <p class="text-sticker">
          <!-- TEXT STICKER ICON -->
          <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
          </svg>
          <!-- TEXT STICKER ICON -->
          {{ __('messages.you_have') }}&nbsp;{{ $user->vu }}&nbsp;{{ __('messages.ptvysa') }}&nbsp;|&nbsp;
          {{ __('messages.yshbv') }}&nbsp;:&nbsp;{{ $visits }}
      </p>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="{{ route('ads.promote', ['p' => 'exchange']) }}" class="button secondary" style="color: #fff;" >
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
        <table id="tablepagination" class="table table-borderless table-hover">
            <thead>
             <tr>
              <th>{{ __('messages.id') ?? '#ID' }}</th>
              <th>{{ __('messages.name') ?? 'Name' }}</th>
              <th>{{ __('messages.vu') ?? 'Vu' }}</th>
              <th>{{ __('messages.tims') ?? 'Tims' }}</th>
              <th>{{ __('messages.statu') ?? 'Statu' }}</th>
              <th></th>
             </tr>
            </thead>
            <tbody>
             @foreach($sites as $site)
             @php
                 $fgft = $site->statu == 1 ? "ON" : "OFF";
                 $repvu = array("1","2","3","4");
                 $repvu_to = array("10s","20s","30s","60s");
                 $tims_vu = str_replace($repvu, $repvu_to, $site->tims);
                 $bnname = mb_strlen($site->name, 'utf8') > 25 ? mb_substr($site->name, 0, 25) . "&nbsp;..." : $site->name;
             @endphp
             <tr>
               <td>{{ $site->id }}</td>
               <td>{!! $bnname !!}</td>
               <td>{{ $site->vu }}</td>
               <td>{{ $tims_vu }}</td>
               <td>{{ $fgft }}</td>
               <td>
                   <div style="display: flex; align-items: center; gap: 8px;">
                       <a href="{{ route('visits.edit', $site->id) }}" class='btn btn-success'><i class="fa fa-edit "></i></a>
                       <form action="{{ route('visits.destroy', $site->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_visit') }}');" style="margin: 0;">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="btn btn-danger" aria-label="{{ __('messages.delete_visit') }}" style="display: inline-flex; align-items: center; justify-content: center;">
                               <i class="fa fa-ban "></i>
                           </button>
                       </form>
                   </div>
               </td>
             </tr>
             @endforeach
            </tbody>
        </table>
    </div>
  </div>
</div>
@endsection
