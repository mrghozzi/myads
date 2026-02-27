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
              <p class="achievement-box-title">{{ __('messages.codes') }}&nbsp;{{ __('messages.bannads') }}</p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>{{ __('messages.yhtierbpyaci') }}</b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="{{ route('legacy.b_list') }}">
          {{ __('messages.list') }}&nbsp;{{ __('messages.bannads') }}
          </a>
          <!-- /BUTTON -->
       </div>
</div>

<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
         <!-- WIDGET BOX TITLE -->
         <p class="widget-box-title">Your referral link</p>
         <br />
         <blockquote class="widget-box" >
         <center><kbd>{{ route('register', ['ref' => $user->id]) }}</kbd></center>
         </blockquote>
         <br />
         <p class="widget-box-title"><i class="fa fa-share"></i>&nbsp;Share your referral link</p>
         <div class="widget-box-content">
            <!-- SOCIAL LINKS -->
            <div class="social-links multiline align-left">
              <!-- SOCIAL LINK -->
              <a class="social-link small facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('register', ['ref' => $user->id])) }}" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-facebook-f" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small" href="https://twitter.com/intent/tweet?text={{ urlencode(config('app.name')) }}&url={{ urlencode(route('register', ['ref' => $user->id])) }}" style="background-color: #011a24;" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-x-twitter" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small youtube" href="https://telegram.me/share/url?url={{ urlencode(route('register', ['ref' => $user->id])) }}&text={{ urlencode(config('app.name')) }}" style="background-color: #0088cc;" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-telegram" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

           </div>
         <!-- WIDGET BOX TITLE -->
    </div>
  </div>
  </div>
  <div class="tab-box">
          <!-- TAB BOX OPTIONS -->
          <div class="tab-box-options">
            <!-- TAB BOX OPTION -->
            <div class="tab-box-option active">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">728x90</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">300x250</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">160x600</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">468x60</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">{{ __('messages.responsive') }}&nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->
          </div>
          <!-- /TAB BOX OPTIONS -->

          <!-- TAB BOX ITEMS -->
          <div class="tab-box-items">
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: block;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags 728x90  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');"><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=728x90"></script>{{ $extensions_code }}</textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=728x90"></script></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->

            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags 300x250  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');"><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=300x250"></script>{{ $extensions_code }}</textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=300x250"></script></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags 160x600 (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');"><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=160x600"></script>{{ $extensions_code }}</textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=160x600"></script></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags 468x60  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');"><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=468x60"></script>{{ $extensions_code }}</textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=468x60"></script></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags Responsive  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');"><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=responsive"></script>{{ $extensions_code }}</textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><script type="text/javascript" src="{{ route('ads.script') }}?ID={{ $user->id }}&px=responsive"></script></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->

          </div>
          <!-- /TAB BOX ITEMS -->
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabOptions = document.querySelectorAll('.tab-box-option');
    const tabItems = document.querySelectorAll('.tab-box-item');

    tabOptions.forEach((option, index) => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            tabOptions.forEach(opt => opt.classList.remove('active'));
            // Add active class to clicked option
            this.classList.add('active');

            // Hide all items
            tabItems.forEach(item => item.style.display = 'none');
            // Show corresponding item
            if (tabItems[index]) {
                tabItems[index].style.display = 'block';
            }
        });
    });
});
</script>
@endsection
