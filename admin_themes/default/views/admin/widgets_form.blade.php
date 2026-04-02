<div class="admin-surface-soft">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <span class="admin-panel__eyebrow">{{ $mode === 'edit' ? __('messages.edit') : __('messages.add') }}</span>
            <h3 class="admin-panel__title mb-0">{{ __('messages.widgets') }}</h3>
        </div>
        <button type="button" class="btn btn-light" data-widget-close="true">{{ __('messages.close') }}</button>
    </div>

    <form action="{{ $mode === 'edit' ? route('admin.widgets.update', $widget->id) : route('admin.widgets.store') }}" method="POST" class="admin-section-stack">
        @csrf
        @if($mode === 'create')
            <input type="hidden" name="o_mode" value="{{ $type }}">
        @endif

        <div class="admin-form-grid">
            <div>
                <label class="admin-form-label">{{ __('messages.name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ $widget?->name ?? '' }}" required>
            </div>
            <div>
                <label class="admin-form-label">{{ __('messages.place') }}</label>
                <select name="o_parent" class="form-select" required>
                    @foreach($places as $id => $name)
                        @if(in_array((string) $id, $allowedPlaceIds, true))
                            <option value="{{ $id }}" {{ ($widget?->o_parent == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-form-label">{{ __('messages.order') }}</label>
                <input type="number" name="o_order" class="form-control" value="{{ $widget?->o_order ?? 0 }}" required>
            </div>
            <div>
                <label class="admin-form-label">{{ __('messages.type') }}</label>
                <input type="text" class="form-control" value="{{ $type }}" readonly>
            </div>
            @if($type === 'widget_html')
                <div class="admin-form-grid__full">
                    <label class="admin-form-label">{{ __('messages.content') }}</label>
                    <textarea name="o_valuer" rows="6" class="form-control">{{ $widget?->o_valuer ?? '' }}</textarea>
                </div>
            @endif
        </div>

        <div class="d-flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">
                {{ $mode === 'edit' ? __('messages.save') : __('messages.add') }}
            </button>
            <button type="button" class="btn btn-secondary" data-widget-close="true">
                {{ __('messages.close') }}
            </button>
        </div>
    </form>

    @if($mode === 'edit')
        <form action="{{ route('admin.widgets.delete', $widget->id) }}" method="POST" class="mt-3" data-widget-delete="true">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
        </form>
    @endif
</div>
