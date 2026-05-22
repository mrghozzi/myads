@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title"><span><i class="fa fa-tag" aria-hidden="true"></i></span>&nbsp;{{ $discount->exists ? (__('messages.edit_discount') ?? 'Edit Discount Code') : (__('messages.create_discount') ?? 'Create Discount Code') }}</p>
    <p class="section-banner-text">{{ __('messages.manage_store_discounts_desc') ?? 'Create and manage promo codes for your products.' }}</p>
</div>

<div class="store-editor-page container" style="margin-top: 30px; max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('store.discounts.index') }}" style="color: #9aa4bf; text-decoration: none; font-weight: 600;">
            <i class="fa fa-arrow-left"></i>&nbsp;{{ __('messages.back_to_list') ?? 'Back to list' }}
        </a>
    </div>

    <div class="widget-box" style="background: #1d2333; border: 1px solid #2f3749; border-radius: 12px; padding: 30px;">
        <h3 style="color: #fff; font-weight: 700; margin-bottom: 25px; border-bottom: 1px solid #2f3749; padding-bottom: 15px;">
            {{ $discount->exists ? (__('messages.edit_discount_details') ?? 'Edit Coupon Details') : (__('messages.create_new_discount') ?? 'Create New Coupon') }}
        </h3>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 8px; margin-bottom: 20px; background: rgba(231, 76, 60, 0.15); border-color: #e74c3c; color: #e74c3c;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ $discount->exists ? route('store.discounts.update', $discount->id) : route('store.discounts.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.coupon_name') ?? 'Coupon Name / Description' }} <span style="color: #e74c3c;">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $discount->name) }}" placeholder="e.g. 20% Winter Sale" required style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%;">
            </div>

            <!-- Code -->
            <div class="mb-4">
                <label for="code" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.coupon_code') ?? 'Promo Code' }} <span style="color: #e74c3c;">*</span></label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $discount->code) }}" placeholder="e.g. WINTER20" required style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%; text-transform: uppercase;">
                <small style="color: #9aa4bf; display: block; margin-top: 5px;">{{ __('messages.coupon_code_help') ?? 'The code customers enter at checkout (e.g. SAVE50). Only alphanumeric characters.' }}</small>
            </div>

            <div class="row">
                <!-- Type -->
                <div class="col-md-6 mb-4">
                    <label for="discount_type" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.coupon_type') ?? 'Discount Type' }} <span style="color: #e74c3c;">*</span></label>
                    <select id="discount_type" name="discount_type" class="form-select" required style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%; height: auto;">
                        <option value="percent" {{ old('discount_type', $discount->discount_type) === 'percent' ? 'selected' : '' }}>{{ __('messages.percentage') ?? 'Percentage (%)' }}</option>
                        <option value="fixed" {{ old('discount_type', $discount->discount_type) === 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed_points') ?? 'Fixed Points (PTS)' }}</option>
                    </select>
                </div>

                <!-- Value -->
                <div class="col-md-6 mb-4">
                    <label for="discount_value" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.coupon_value') ?? 'Discount Value' }} <span style="color: #e74c3c;">*</span></label>
                    <input type="number" id="discount_value" name="discount_value" class="form-control" value="{{ old('discount_value', $discount->discount_value) }}" min="1" required style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%;">
                </div>
            </div>

            <div class="row">
                <!-- Scope -->
                <div class="col-md-6 mb-4">
                    <label for="scope" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.applies_to') ?? 'Applies To' }} <span style="color: #e74c3c;">*</span></label>
                    <select id="scope" name="scope" class="form-select" required style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%; height: auto;">
                        <option value="all_my_products" {{ old('scope', $discount->applies_to === 'all' ? 'all_my_products' : 'all_my_products') === 'all_my_products' ? 'selected' : '' }}>{{ __('messages.all_my_products') ?? 'All My Products' }}</option>
                        <option value="one_of_my_products" {{ old('scope', $discount->applies_to === 'product' ? 'one_of_my_products' : '') === 'one_of_my_products' ? 'selected' : '' }}>{{ __('messages.one_of_my_products') ?? 'Specific Product' }}</option>
                    </select>
                </div>

                <!-- Product selection -->
                <div class="col-md-6 mb-4" id="product-select-wrapper" style="display: none;">
                    <label for="product_id" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.select_product') ?? 'Select Product' }} <span style="color: #e74c3c;">*</span></label>
                    <select id="product_id" name="product_id" class="form-select" style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%; height: auto;">
                        <option value="">-- {{ __('messages.choose_product') ?? 'Choose Product' }} --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $discount->target_value) == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->o_order }} PTS)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Start Date -->
                <div class="col-md-6 mb-4">
                    <label for="start_date" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.start_date') ?? 'Start Date' }}</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d\TH:i') : '') }}" style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%;">
                </div>

                <!-- End Date -->
                <div class="col-md-6 mb-4">
                    <label for="end_date" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.end_date') ?? 'End Date' }}</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d\TH:i') : '') }}" style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%;">
                </div>
            </div>

            <!-- Max Uses -->
            <div class="mb-4">
                <label for="max_uses" class="form-label" style="color: #9aa4bf; font-weight: 600; margin-bottom: 8px; display: block;">{{ __('messages.max_uses') ?? 'Max Uses' }}</label>
                <input type="number" id="max_uses" name="max_uses" class="form-control" value="{{ old('max_uses', $discount->max_uses) }}" min="1" placeholder="e.g. 100 (Leave blank for unlimited)" style="background: #181f29; border: 1px solid #2f3749; color: #fff; border-radius: 6px; padding: 12px 16px; width: 100%;">
            </div>

            <!-- Is Active (Only on Edit) -->
            @if($discount->exists)
                <div class="mb-4">
                    <div class="form-check" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }} style="background: #181f29; border: 1px solid #2f3749; border-radius: 4px; width: 20px; height: 20px; cursor: pointer;">
                        <label class="form-check-label" for="is_active" style="color: #fff; font-weight: 600; cursor: pointer;">{{ __('messages.is_active') ?? 'Active and available for checkout' }}</label>
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; border-top: 1px solid #2f3749; padding-top: 20px;">
                <a href="{{ route('store.discounts.index') }}" class="button secondary" style="background: #2f3749; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; text-decoration: none;">
                    {{ __('messages.cancel') ?? 'Cancel' }}
                </a>
                <button type="submit" class="button primary" style="padding: 12px 30px; border-radius: 6px; cursor: pointer;">
                    {{ $discount->exists ? (__('messages.save_changes') ?? 'Save Changes') : (__('messages.create_discount') ?? 'Create Coupon') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeSelect = document.getElementById('scope');
    const productWrapper = document.getElementById('product-select-wrapper');
    const productIdInput = document.getElementById('product_id');
    const codeInput = document.getElementById('code');

    function toggleProductSelect() {
        if (scopeSelect.value === 'one_of_my_products') {
            productWrapper.style.display = 'block';
            productIdInput.required = true;
        } else {
            productWrapper.style.display = 'none';
            productIdInput.required = false;
            productIdInput.value = '';
        }
    }

    // Run on change
    scopeSelect.addEventListener('change', toggleProductSelect);

    // Run once on load
    toggleProductSelect();

    // Uppercase code input as typed
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
});
</script>
@endsection
