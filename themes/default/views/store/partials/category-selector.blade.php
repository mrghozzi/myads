@php
    $selectedStoreCategory = $selectedStoreCategory ?? null;
    $selectedStoreSubcategory = $selectedStoreSubcategory ?? null;
    $scriptProductOptions = $scriptProductOptions ?? collect();
    $scriptCategoryOptions = $scriptCategoryOptions ?? collect();
@endphp

<div class="form-select">
    <label for="cat_s"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;{{ __('messages.cat') }}</label>
    <select id="cat_s" name="cat_s" required onchange="window.triggerCategoryUpdate(this)">
        <option value="">-- {{ __('messages.select') }} --</option>
        @foreach($storeCategories as $category)
            <option value="{{ $category->name }}" @selected($selectedStoreCategory === $category->name)>
                {{ __('messages.' . $category->name) }}
            </option>
        @endforeach
    </select>
    <svg class="form-select-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg>
</div>

@if($selectedStoreCategory === \App\Support\StoreCategoryCatalog::PLUGINS || $selectedStoreCategory === \App\Support\StoreCategoryCatalog::THEMES)
    <div class="form-select" style="margin-top: 16px;">
        <label for="sc_cat"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp;{{ \Illuminate\Support\Facades\Lang::has('messages.script') ? __('messages.script') : 'Choose Script' }}</label>
        <select id="sc_cat" name="sc_cat" required>
            <option value="">-- {{ __('messages.select') }} --</option>
            @foreach($scriptProductOptions as $scriptProduct)
                <option value="{{ $scriptProduct['value'] }}" @selected((string) $selectedStoreSubcategory === (string) $scriptProduct['value'])>
                    {{ $scriptProduct['label'] }}
                </option>
            @endforeach
            <option value="others" @selected($selectedStoreSubcategory === 'others')>{{ __('messages.others') }}</option>
        </select>
        <svg class="form-select-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg>
    </div>
@elseif($selectedStoreCategory === \App\Support\StoreCategoryCatalog::SCRIPT)
    <div class="form-select" style="margin-top: 16px;">
        <label for="sc_cat"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp;{{ \Illuminate\Support\Facades\Lang::has('messages.script_type') ? __('messages.script_type') : __('messages.subcategories') }}</label>
        <select id="sc_cat" name="sc_cat" required>
            <option value="">-- {{ __('messages.select') }} --</option>
            @foreach($scriptCategoryOptions as $scriptCategory)
                <option value="{{ $scriptCategory['value'] }}" @selected((string) $selectedStoreSubcategory === (string) $scriptCategory['value'])>
                    {{ \Illuminate\Support\Facades\Lang::has('messages.' . $scriptCategory['label']) ? __('messages.' . $scriptCategory['label']) : $scriptCategory['label'] }}
                </option>
            @endforeach
        </select>
        <svg class="form-select-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg>
    </div>
@endif

