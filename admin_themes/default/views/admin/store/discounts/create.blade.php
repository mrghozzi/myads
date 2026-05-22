@extends('admin::layouts.admin')

@section('title', __('messages.create_discount') ?? 'Create Discount Code')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="pages-editor-shell">
    <style>
        .pages-editor-shell {
            --pe-bg: #f4f6fb;
            --pe-card-bg: #ffffff;
            --pe-card-alt: #f8faff;
            --pe-border: #dfe5f2;
            --pe-border-strong: #cad4ea;
            --pe-title: #1d2a44;
            --pe-muted: #64708b;
            --pe-primary: #3f66ff;
            --pe-primary-hover: #2f53e2;
            --pe-shadow: 0 10px 30px rgba(26, 42, 84, 0.08);
            --pe-focus: rgba(63, 102, 255, 0.22);
        }

        .app-skin-dark .pages-editor-shell {
            --pe-bg: #111827;
            --pe-card-bg: #1f2937;
            --pe-card-alt: #263246;
            --pe-border: #374151;
            --pe-border-strong: #4b5563;
            --pe-title: #f9fafb;
            --pe-muted: #9ca3af;
            --pe-primary: #5b7cff;
            --pe-primary-hover: #4a69ec;
            --pe-shadow: 0 10px 30px rgba(0, 0, 0, 0.28);
            --pe-focus: rgba(91, 124, 255, 0.28);
        }

        .pages-editor-shell {
            background: var(--pe-bg);
            border: 1px solid var(--pe-border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--pe-shadow);
        }

        .pages-editor-hero {
            background: linear-gradient(120deg, var(--pe-card-bg) 0%, var(--pe-card-alt) 100%);
            border: 1px solid var(--pe-border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 18px;
            display: flex;
            gap: 16px;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .pages-editor-hero h2 {
            color: var(--pe-title);
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .pages-editor-hero p {
            margin: 8px 0 0;
            color: var(--pe-muted);
            font-size: 13px;
        }

        .pe-breadcrumb {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            gap: 8px;
            align-items: center;
            color: var(--pe-muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pe-breadcrumb a {
            color: var(--pe-muted);
            text-decoration: none;
        }

        .pe-breadcrumb a:hover {
            color: var(--pe-primary);
        }

        .pe-card {
            border: 1px solid var(--pe-border);
            border-radius: 14px;
            background: var(--pe-card-bg);
            box-shadow: var(--pe-shadow);
            overflow: hidden;
        }

        .pe-card-header {
            background: var(--pe-card-alt);
            border-bottom: 1px solid var(--pe-border);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pe-card-header h5 {
            margin: 0;
            color: var(--pe-title);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pe-card-body {
            padding: 18px;
        }

        .pages-editor-shell .form-label {
            color: var(--pe-title);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
        }

        .pages-editor-shell .form-control,
        .pages-editor-shell .form-select {
            border-color: var(--pe-border);
            min-height: 44px;
            border-radius: 10px;
            background: var(--pe-card-bg);
            color: var(--pe-title);
            font-size: 14px;
        }

        .pages-editor-shell .form-control::placeholder {
            color: var(--pe-muted);
        }

        .pages-editor-shell .form-control:focus,
        .pages-editor-shell .form-select:focus {
            border-color: var(--pe-primary);
            box-shadow: 0 0 0 4px var(--pe-focus);
        }

        .pe-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pe-btn-primary {
            border: 0;
            color: #fff;
            background: var(--pe-primary);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pe-btn-primary:hover {
            background: var(--pe-primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .pe-btn-secondary {
            border: 1px solid var(--pe-border);
            color: var(--pe-muted);
            background: var(--pe-card-alt);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pe-btn-secondary:hover {
            color: var(--pe-title);
            border-color: var(--pe-border-strong);
        }

        .pages-editor-shell .alert {
            border-radius: 12px;
            border-width: 1px;
        }

        .pages-editor-shell .form-check {
            background: var(--pe-card-alt);
            border: 1px solid var(--pe-border);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pages-editor-shell .form-check-label {
            color: var(--pe-title);
            font-size: 13px;
            font-weight: 600;
            margin-inline-start: 12px;
            flex: 1;
        }

        .pages-editor-shell .form-check-input {
            margin: 0;
            min-width: 42px;
            height: 22px;
        }
    </style>

    <div class="pages-editor-hero">
        <div>
            <ul class="pe-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><i class="feather-chevron-right"></i></li>
                <li><a href="{{ route('admin.store.discounts.index') }}">{{ __('messages.discount_codes') ?? 'Discount Codes' }}</a></li>
                <li><i class="feather-chevron-right"></i></li>
                <li>{{ __('messages.create_discount') ?? 'Create Coupon' }}</li>
            </ul>
            <h2>{{ __('messages.create_discount') ?? 'Create Coupon' }}</h2>
            <p>{{ __('messages.manage_store_discounts_desc') ?? 'Create a new discount code for your store.' }}</p>
        </div>

        <div class="pe-actions">
            <a href="{{ route('admin.store.discounts.index') }}" class="pe-btn-secondary">
                <i class="feather-x"></i>{{ __('messages.cancel') ?? 'Cancel' }}
            </a>
            <button type="submit" form="discount-form" class="pe-btn-primary">
                <i class="feather-save"></i>{{ __('messages.create') ?? 'Create' }}
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="discount-form" action="{{ route('admin.store.discounts.store') }}" method="POST">
        @csrf

        <div class="pe-card">
            <div class="pe-card-header">
                <i class="feather-tag text-primary"></i>
                <h5>{{ __('messages.coupon_details') ?? 'Coupon Details' }}</h5>
            </div>

            <div class="pe-card-body">
                <div class="row">
                    <!-- Coupon Name -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="name">{{ __('messages.coupon_name') ?? 'Coupon Name' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. 10% Storewide Sale" required>
                    </div>

                    <!-- Coupon Code -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="code">{{ __('messages.coupon_code') ?? 'Coupon Code' }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" placeholder="e.g. SAVE10" style="text-transform: uppercase;" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Type -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="discount_type">{{ __('messages.coupon_type') ?? 'Discount Type' }} <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" class="form-select" required>
                            <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>{{ __('messages.percentage') ?? 'Percentage (%)' }}</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed_points') ?? 'Fixed Points (PTS)' }}</option>
                        </select>
                    </div>

                    <!-- Value -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="discount_value">{{ __('messages.coupon_value') ?? 'Discount Value' }} <span class="text-danger">*</span></label>
                        <input type="number" name="discount_value" id="discount_value" class="form-control" value="{{ old('discount_value') }}" min="1" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Applies To -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="applies_to">{{ __('messages.applies_to') ?? 'Applies To' }} <span class="text-danger">*</span></label>
                        <select name="applies_to" id="applies_to" class="form-select" required>
                            <option value="all" {{ old('applies_to') === 'all' ? 'selected' : '' }}>{{ __('messages.all_products') ?? 'All Products' }}</option>
                            <option value="product" {{ old('applies_to') === 'product' ? 'selected' : '' }}>{{ __('messages.specific_product') ?? 'Specific Product' }}</option>
                            <option value="category" {{ old('applies_to') === 'category' ? 'selected' : '' }}>{{ __('messages.product_category') ?? 'Product Category' }}</option>
                            <option value="seller" {{ old('applies_to') === 'seller' ? 'selected' : '' }}>{{ __('messages.seller_products') ?? 'All Seller\'s Products' }}</option>
                        </select>
                    </div>

                    <!-- Target Value selectors (Only one active at a time) -->
                    <!-- Product Wrapper -->
                    <div class="col-md-6 mb-4" id="product-wrapper" style="display: none;">
                        <label class="form-label" for="product-select">{{ __('messages.select_product') ?? 'Select Product' }} <span class="text-danger">*</span></label>
                        <select id="product-select" class="form-select">
                            <option value="">-- {{ __('messages.choose_product') ?? 'Choose Product' }} --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->productPrice }} PTS)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Wrapper -->
                    <div class="col-md-6 mb-4" id="category-wrapper" style="display: none;">
                        <label class="form-label" for="category-select">{{ __('messages.select_category') ?? 'Select Category' }} <span class="text-danger">*</span></label>
                        <select id="category-select" class="form-select">
                            <option value="">-- {{ __('messages.choose_category') ?? 'Choose Category' }} --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Seller Wrapper -->
                    <div class="col-md-6 mb-4" id="seller-wrapper" style="display: none;">
                        <label class="form-label" for="seller-select">{{ __('messages.select_seller') ?? 'Select Seller' }} <span class="text-danger">*</span></label>
                        <select id="seller-select" class="form-select">
                            <option value="">-- {{ __('messages.choose_seller') ?? 'Choose Seller' }} --</option>
                            @foreach($sellers as $sel)
                                <option value="{{ $sel->id }}">{{ $sel->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Start Date -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="start_date">{{ __('messages.start_date') ?? 'Start Date' }}</label>
                        <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="end_date">{{ __('messages.end_date') ?? 'End Date' }}</label>
                        <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                <div class="row">
                    <!-- Max Uses -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="max_uses">{{ __('messages.max_uses') ?? 'Max Uses' }}</label>
                        <input type="number" name="max_uses" id="max_uses" class="form-control" value="{{ old('max_uses') }}" min="1" placeholder="e.g. 100 (Leave blank for unlimited)">
                    </div>

                    <!-- Active Switch -->
                    <div class="col-md-6 mb-4 d-flex align-items-end">
                        <div class="form-check form-switch mb-0 w-100" style="min-height: 44px;">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">{{ __('messages.active') ?? 'Active' }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const appliesToSelect = document.getElementById('applies_to');
    const productWrapper = document.getElementById('product-wrapper');
    const categoryWrapper = document.getElementById('category-wrapper');
    const sellerWrapper = document.getElementById('seller-wrapper');
    
    const productSelect = document.getElementById('product-select');
    const categorySelect = document.getElementById('category-select');
    const sellerSelect = document.getElementById('seller-select');
    
    function toggleTargetFields() {
        const val = appliesToSelect.value;
        
        // Hide all
        productWrapper.style.display = 'none';
        categoryWrapper.style.display = 'none';
        sellerWrapper.style.display = 'none';
        
        // Disable name attributes so we don't submit them
        productSelect.removeAttribute('name');
        productSelect.required = false;
        
        categorySelect.removeAttribute('name');
        categorySelect.required = false;
        
        sellerSelect.removeAttribute('name');
        sellerSelect.required = false;
        
        if (val === 'product') {
            productWrapper.style.display = 'block';
            productSelect.setAttribute('name', 'target_value');
            productSelect.required = true;
        } else if (val === 'category') {
            categoryWrapper.style.display = 'block';
            categorySelect.setAttribute('name', 'target_value');
            categorySelect.required = true;
        } else if (val === 'seller') {
            sellerWrapper.style.display = 'block';
            sellerSelect.setAttribute('name', 'target_value');
            sellerSelect.required = true;
        }
    }
    
    appliesToSelect.addEventListener('change', toggleTargetFields);
    toggleTargetFields();
    
    // Auto-uppercase code input
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }
});
</script>
@endsection
