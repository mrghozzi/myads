<div class="admin-surface-soft p-4" style="border-radius: 16px; background: var(--admin-card-bg, #ffffff); border: 1px solid rgba(97, 93, 250, 0.15); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom border-light-subtle">
        <div class="d-flex align-items-center gap-3">
            <div class="widget-icon-pill" style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.15) 100%); display: flex; align-items: center; justify-content: center; color: #615dfa;">
                <i class="{{ $mode === 'edit' ? 'feather-edit-3' : 'feather-plus-circle' }}" style="font-size: 20px;"></i>
            </div>
            <div>
                <span class="admin-panel__eyebrow text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px; color: #615dfa;">
                    {{ $mode === 'edit' ? __('messages.edit') : __('messages.add') }} {{ __('messages.widgets') }}
                </span>
                <h3 class="admin-panel__title mb-0" style="font-size: 18px; font-weight: 700;">
                    {{ $mode === 'edit' ? ($widget->name ?? __('messages.widgets')) : (__('messages.add') . ' ' . $type) }}
                </h3>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-light border" data-widget-close="true" style="border-radius: 10px;">
            <i class="feather-x me-1"></i> {{ __('messages.close') }}
        </button>
    </div>

    <form action="{{ $mode === 'edit' ? route('admin.widgets.update', $widget->id) : route('admin.widgets.store') }}" method="POST" class="admin-section-stack">
        @csrf
        @if($mode === 'create')
            <input type="hidden" name="o_mode" value="{{ $type }}">
        @endif

        @if(isset($selectedPlace) && $selectedPlace && $mode === 'create')
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3" style="border-radius: 12px; background: rgba(35, 210, 226, 0.08); border: 1px solid rgba(35, 210, 226, 0.25); color: #0284c7;">
                <i class="feather-map-pin" style="font-size: 16px;"></i>
                <span style="font-size: 13px; font-weight: 600;">
                    {{ __('messages.target_location') ?? 'المكان الموجه من الموقع' }}: <strong>{{ $places[$selectedPlace] ?? ('#' . $selectedPlace) }}</strong>
                </span>
            </div>
        @endif

        <div class="admin-form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div>
                <label class="admin-form-label fw-bold mb-1" style="font-size: 13px;">{{ __('messages.name') }}</label>
                <input type="text" name="name" class="form-control admin-form-control" value="{{ $widget?->name ?? '' }}" placeholder="{{ __('messages.name') }}..." required style="border-radius: 10px;">
            </div>
            <div>
                <label class="admin-form-label fw-bold mb-1" style="font-size: 13px;">{{ __('messages.place') }}</label>
                <select name="o_parent" class="form-select admin-form-control" required style="border-radius: 10px;">
                    @foreach($places as $id => $name)
                        @if(in_array((string) $id, $allowedPlaceIds, true))
                            @php
                                $isSelected = ($widget?->o_parent == $id) || ($mode === 'create' && isset($selectedPlace) && (string)$selectedPlace === (string)$id);
                            @endphp
                            <option value="{{ $id }}" {{ $isSelected ? 'selected' : '' }}>{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-form-label fw-bold mb-1" style="font-size: 13px;">{{ __('messages.order') }}</label>
                <input type="number" name="o_order" class="form-control admin-form-control" value="{{ $widget?->o_order ?? 0 }}" required style="border-radius: 10px;">
            </div>
            <div>
                <label class="admin-form-label fw-bold mb-1" style="font-size: 13px;">{{ __('messages.type') }}</label>
                <input type="text" class="form-control admin-form-control bg-light" value="{{ $type }}" readonly style="border-radius: 10px; opacity: 0.85;">
            </div>
            @if($type === 'widget_html')
                <div class="admin-form-grid__full" style="grid-column: 1 / -1;">
                    <label class="admin-form-label fw-bold mb-1 d-flex justify-content-between align-items-center" style="font-size: 13px;">
                        <span>{{ __('messages.content') }} (HTML Code)</span>
                        <span class="badge bg-soft-primary text-primary" style="font-size: 10px;">HTML / JS / CSS</span>
                    </label>
                    <textarea name="o_valuer" rows="7" class="form-control admin-form-control font-monospace" placeholder="<div class=&quot;custom-widget&quot;>...</div>" style="border-radius: 12px; font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 13px; background: var(--admin-input-bg, #f8fafc);">{{ $widget?->o_valuer ?? '' }}</textarea>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-3 border-top border-light-subtle">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4" style="background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%); border: none; border-radius: 10px; font-weight: 700; box-shadow: 0 4px 12px rgba(97, 93, 250, 0.3);">
                    <i class="feather-check me-1"></i> {{ $mode === 'edit' ? __('messages.save') : __('messages.add') }}
                </button>
                <button type="button" class="btn btn-secondary border px-3" data-widget-close="true" style="border-radius: 10px;">
                    {{ __('messages.close') }}
                </button>
            </div>
            
            @if($mode === 'edit')
                <button type="button" class="btn btn-outline-danger px-3" onclick="document.getElementById('delete-widget-form-{{ $widget->id }}').submit();" style="border-radius: 10px;">
                    <i class="feather-trash-2 me-1"></i> {{ __('messages.delete') }}
                </button>
            @endif
        </div>
    </form>

    @if($mode === 'edit')
        <form id="delete-widget-form-{{ $widget->id }}" action="{{ route('admin.widgets.delete', $widget->id) }}" method="POST" class="d-none" data-widget-delete="true">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

