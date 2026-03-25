<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                $listings = \App\Models\Directory::where('statu', 1)->latest('id')->limit(5)->get();
            @endphp

            @foreach($listings as $listing)
                <div class="user-status request-small">
                    <!-- SITE ICON -->
                    <div class="user-status-avatar">
                        <div class="user-avatar small no-outline">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/directory/category/all-01.png') }}"></div>
                            </div>
                        </div>
                    </div>
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('directory.show', $listing->id) }}">{{ Str::limit($listing->name, 25) }}</a>
                    </p>
                    <p class="user-status-text small">
                        {{ $listing->category?->name ?? __('messages.directory') }} | {{ \Carbon\Carbon::createFromTimestamp($listing->date)->diffForHumans() }}
                    </p>
                </div>
            @endforeach

            @if($listings->isEmpty())
                <p class="text-center small">{{ __('messages.no_listings_found') }}</p>
            @endif
        </div>
        <a href="{{ route('directory.index') }}" class="button primary full" style="margin-top: 20px;">{{ __('messages.see_all') }}</a>
    </div>
</div>
