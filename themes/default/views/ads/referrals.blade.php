@extends('theme::layouts.master')

@section('content')
@php
    $refUrl = url('/') . '?ref=' . $user->id;
    $siteTitle = $site_settings->titer ?? config('app.name');
    $banner728 = theme_asset('img/banner/728x90.gif');
    $banner300 = theme_asset('img/banner/300x250.gif');
    $banner160 = theme_asset('img/banner/160x600.gif');
    $banner468 = theme_asset('img/banner/468x60.gif');
    $code728 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner728}\" width=\"728\" height=\"90\" ></a><!-- ADStn code begin -->";
    $code300 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner300}\" width=\"300\" height=\"250\" ></a><!-- ADStn code begin -->";
    $code160 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner160}\" width=\"160\" height=\"600\" ></a><!-- ADStn code begin -->";
    $code468 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner468}\" width=\"468\" height=\"60\" ></a><!-- ADStn code begin -->";
@endphp

<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/03.jpg') }}) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/referral.png') }}" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title">{{ __('messages.codes') }}&nbsp;{{ __('messages.referal') }}</p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>{{ __('messages.ryffyrly') }}</b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="{{ route('legacy.referral') }}">
          <i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ __('messages.list') }}&nbsp;{{ __('messages.referal') }}
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
         <center><kbd>{{ $refUrl }}</kbd></center>
         </blockquote>
         <br />
         <p class="widget-box-title"><i class="fa fa-share"></i>&nbsp;Share your referral link</p>
         <div class="widget-box-content">
            <!-- SOCIAL LINKS -->
            <div class="social-links multiline align-left">
              <!-- SOCIAL LINK -->
              <a class="social-link small facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($refUrl) }}" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-facebook-f" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small" href="https://twitter.com/intent/tweet?text={{ urlencode($siteTitle) }}&url={{ urlencode($refUrl) }}" style="background-color: #011a24;" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-x-twitter" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small youtube" href="https://telegram.me/share/url?url={{ urlencode($refUrl) }}&text={{ urlencode($siteTitle) }}" style="background-color: #0088cc;" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-telegram" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

           </div>
         <!-- WIDGET BOX TITLE -->
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
          </div>
          <!-- /TAB BOX OPTIONS -->

          <!-- TAB BOX ITEMS -->
          <div class="tab-box-items">
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: block;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your sponsorship tag 728x90</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">
{{ $code728 }}
{{ $extensions_code }}
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><a href="{{ $refUrl }}"><img src="{{ $banner728 }}" width="728" height="90" ></a></center>
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
                <p class="tab-box-item-title">Your sponsorship tag 300x250</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">
{{ $code300 }}
{{ $extensions_code }}
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><a href="{{ $refUrl }}"><img src="{{ $banner300 }}" width="300" height="250" ></a></center>
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
                <p class="tab-box-item-title">Your sponsorship tag 160x600</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">
{{ $code160 }}
{{ $extensions_code }}
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><a href="{{ $refUrl }}"><img src="{{ $banner160 }}" width="160" height="600" ></a></center>
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
                <p class="tab-box-item-title">Your sponsorship tag 468x60</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">
{{ $code468 }}
{{ $extensions_code }}
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><a href="{{ $refUrl }}"><img src="{{ $banner468 }}" width="468" height="60" ></a></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->

          </div>
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
