<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                $newsItems = \App\Models\News::where('statu', 1)->latest('id')->limit(3)->get();
            @endphp

            @foreach($newsItems as $news)
                <div class="user-status request-small">
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('news.show', $news->id) }}">{{ Str::limit($news?->name, 35) }}</a>
                    </p>
                    <p class="user-status-text small">
                        {{ \Carbon\Carbon::createFromTimestamp($news?->date ?? time())->diffForHumans() }}
                    </p>
                </div>
            @endforeach

            @if($newsItems->isEmpty())
                <p class="text-center small">{{ __('messages.no_post') }}</p>
            @endif
        </div>
        <a href="{{ route('news.index') }}" class="button primary full" style="margin-top: 20px;">{{ __('messages.see_all') }}</a>
    </div>
</div>
