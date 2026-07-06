@if(Auth::check())
    @include('theme::seo_checker.member_index')
@else
    @include('theme::seo_checker.guest_index')
@endif
