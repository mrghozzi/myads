@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $limit = (int) ($config['limit'] ?? 3);
    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
    $buttonText = !empty($config['button_text']) ? $config['button_text'] : __('messages.see_all');

    $newsItems = \App\Models\News::where('statu', 1)->latest('id')->limit($limit)->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @foreach($newsItems as $news)
                <div class="user-status request-small">
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('news.show', $news->id) }}">{{ Str::limit($news?->name, 35) }}</a>
                    </p>
                    @if($showDate)
                        <p class="user-status-text small">
                            {{ \Carbon\Carbon::createFromTimestamp($news?->date ?? time())->diffForHumans() }}
                        </p>
                    @endif
                </div>
            @endforeach

            @if($newsItems->isEmpty())
                <p class="text-center small text-muted my-2">{{ __('messages.no_post') }}</p>
            @endif
        </div>
        @if($showButton)
            <a href="{{ route('news.index') }}" class="button primary full" style="margin-top: 20px;">{{ $buttonText }}</a>
        @endif
    </div>
</div>
