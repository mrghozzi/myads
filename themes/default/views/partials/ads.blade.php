@php
    $ad = \App\Models\Ad::find($id);
@endphp

@if($ad && $ad->code_ads)
    <div class="ads-container" style="text-align: center; margin: 20px 0;">
        {!! $ad->code_ads !!}
    </div>
@endif
