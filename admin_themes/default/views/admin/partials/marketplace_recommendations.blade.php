@if(isset($uninstalledMarketplaceItems) && $uninstalledMarketplaceItems->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header border-0 bg-transparent pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-shopping-bag me-2" style="color: #8b5cf6;"></i> {{ __('messages.marketplace') ?? 'Marketplace' }} - {{ __('messages.recommended') ?? 'Recommended' }}</h6>
                    <a href="{{ route('admin.plugins') }}" class="btn btn-sm btn-light rounded-pill px-3">{{ __('messages.view_all') ?? 'View All' }}</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="marketplaceCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" style="border-radius: 12px;">
                            @foreach($uninstalledMarketplaceItems->chunk(2) as $index => $chunk)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <div class="row g-3">
                                        @foreach($chunk as $item)
                                            @php
                                                $marketImage = trim((string) ($item['image_url'] ?? ''));
                                                $marketName = trim((string) ($item['name'] ?? ''));
                                                $marketSlug = trim((string) ($item['slug'] ?? ''));
                                                $marketDescription = trim((string) ($item['description'] ?? ''));
                                                $marketInitial = strtoupper(substr($marketName !== '' ? $marketName : $marketSlug, 0, 1));
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 border rounded-3 h-100" style="background: #f8fafc;">
                                                    <div style="width: 60px; height: 60px; flex-shrink: 0; border-radius: 10px; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; color: #64748b; margin-inline-end: 15px;">
                                                        @if($marketImage !== '')
                                                            <img src="{{ $marketImage }}" alt="{{ $marketName }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                        @else
                                                            {{ $marketInitial }}
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $marketName !== '' ? $marketName : $marketSlug }}</h6>
                                                            @if(!empty($item['category']))
                                                                <span class="badge bg-light text-secondary border ms-2" style="font-size: 0.65rem;">{{ __('messages.' . ($item['category'] === 'templates' ? 'themes' : $item['category'])) }}</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">{{ $marketDescription }}</p>
                                                        <a href="{{ $item['product_url'] ?? 'https://www.adstn.ovh/store' }}" target="_blank" class="btn btn-sm btn-primary py-1 px-3" style="font-size: 0.75rem; border-radius: 6px;">{{ __('messages.open_in_store') ?? 'Store' }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($uninstalledMarketplaceItems->count() > 2)
                        <div class="d-flex justify-content-center mt-3 gap-2">
                            <button class="btn btn-sm btn-light border rounded-circle" type="button" data-bs-target="#marketplaceCarousel" data-bs-slide="prev" style="width: 32px; height: 32px; padding: 0;">
                                <i class="feather-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-light border rounded-circle" type="button" data-bs-target="#marketplaceCarousel" data-bs-slide="next" style="width: 32px; height: 32px; padding: 0;">
                                <i class="feather-chevron-right"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
