@if(Auth::check())
    @include('theme::seo_checker.member_results')
@else
    @include('theme::seo_checker.guest_results')
@endif
