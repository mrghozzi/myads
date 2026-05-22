@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title"><span><i class="fa fa-tags" aria-hidden="true"></i></span>&nbsp;{{ __('messages.discount_codes') ?? 'Discount Codes' }}</p>
    <p class="section-banner-text">{{ __('messages.manage_store_discounts_desc') ?? 'Create and manage promo codes for your products.' }}</p>
</div>

<div class="store-editor-page container" style="margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="color: #fff; font-weight: 700; margin: 0;">{{ __('messages.my_discounts') ?? 'My Discount Codes' }}</h3>
        <a href="{{ route('store.discounts.create') }}" class="button primary">
            <i class="fa fa-plus"></i>&nbsp;{{ __('messages.create_discount') ?? 'Create Coupon' }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 8px; margin-bottom: 20px; background: rgba(46, 204, 113, 0.15); border-color: #2ecc71; color: #2ecc71;">
            <strong><i class="fa fa-check-circle"></i></strong>&nbsp;{{ session('success') }}
        </div>
    @endif

    <div class="widget-box" style="background: #1d2333; border: 1px solid #2f3749; border-radius: 12px; padding: 24px;">
        <div class="table-responsive">
            <table class="table" style="color: #fff; vertical-align: middle; margin-bottom: 0;">
                <thead>
                    <tr style="color: #9aa4bf; border-bottom: 2px solid #2f3749;">
                        <th>{{ __('messages.coupon_name') ?? 'Name' }}</th>
                        <th>{{ __('messages.coupon_code') ?? 'Code' }}</th>
                        <th>{{ __('messages.coupon_value') ?? 'Value' }}</th>
                        <th>{{ __('messages.applies_to') ?? 'Applies To' }}</th>
                        <th>{{ __('messages.coupon_uses') ?? 'Uses' }}</th>
                        <th>{{ __('messages.coupon_dates') ?? 'Validity Period' }}</th>
                        <th>{{ __('messages.status') ?? 'Status' }}</th>
                        <th class="text-end">{{ __('messages.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $discount)
                        <tr style="border-bottom: 1px solid #2f3749;">
                            <td><strong style="color: #fff;">{{ $discount->name }}</strong></td>
                            <td><span style="background: #252d42; color: #4f46e5; border: 1px solid #2f3749; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 14px; font-weight: 700;">{{ $discount->code }}</span></td>
                            <td>
                                @if($discount->discount_type === 'percent')
                                    <span class="badge bg-primary" style="font-size: 13px; font-weight: 600;">{{ $discount->discount_value }}%</span>
                                @else
                                    <span class="badge bg-success" style="font-size: 13px; font-weight: 600;">{{ $discount->discount_value }} PTS</span>
                                @endif
                            </td>
                            <td>
                                @if($discount->applies_to === 'all')
                                    <span style="color: #9aa4bf;"><i class="fa fa-globe"></i>&nbsp;{{ __('messages.all_my_products') ?? 'All my products' }}</span>
                                @elseif($discount->applies_to === 'product')
                                    @php
                                        $targetProd = \App\Models\Product::withoutGlobalScope('store')->find($discount->target_value);
                                    @endphp
                                    @if($targetProd)
                                        <a href="{{ route('store.show', $targetProd->name) }}" style="color: #4f46e5; text-decoration: none; font-weight: 600;">
                                            <i class="fa fa-shopping-bag"></i>&nbsp;{{ $targetProd->name }}
                                        </a>
                                    @else
                                        <span style="color: #e74c3c;"><i class="fa fa-exclamation-circle"></i>&nbsp;{{ __('messages.unknown_product') ?? 'Deleted Product' }}</span>
                                    @endif
                                @else
                                    <span style="color: #9aa4bf;">{{ $discount->applies_to }} ({{ $discount->target_value }})</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600;">{{ $discount->uses }}</span>
                                @if($discount->max_uses)
                                    <span style="color: #9aa4bf;">/ {{ $discount->max_uses }}</span>
                                @else
                                    <span style="color: #9aa4bf;">/ &infin;</span>
                                @endif
                            </td>
                            <td>
                                @if($discount->start_date || $discount->end_date)
                                    <div style="font-size: 13px; color: #9aa4bf;">
                                        @if($discount->start_date)
                                            <div>{{ __('messages.from') ?? 'From' }}: {{ $discount->start_date->format('Y-m-d H:i') }}</div>
                                        @endif
                                        @if($discount->end_date)
                                            <div>{{ __('messages.to') ?? 'To' }}: {{ $discount->end_date->format('Y-m-d H:i') }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: #9aa4bf;">&infin;&nbsp;{{ __('messages.always_valid') ?? 'Always valid' }}</span>
                                @endif
                            </td>
                            <td>
                                @if(!$discount->is_active)
                                    <span class="badge bg-danger" style="font-size: 12px; font-weight: 600;">{{ __('messages.inactive') ?? 'Inactive' }}</span>
                                @elseif($discount->end_date && $discount->end_date->isPast())
                                    <span class="badge bg-secondary" style="font-size: 12px; font-weight: 600;">{{ __('messages.expired') ?? 'Expired' }}</span>
                                @elseif($discount->max_uses && $discount->uses >= $discount->max_uses)
                                    <span class="badge bg-warning text-dark" style="font-size: 12px; font-weight: 600;">{{ __('messages.limit_reached') ?? 'Limit Reached' }}</span>
                                @else
                                    <span class="badge bg-success" style="font-size: 12px; font-weight: 600;">{{ __('messages.active') ?? 'Active' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('store.discounts.edit', $discount->id) }}" class="btn btn-sm btn-outline-info" style="color: #0dcaf0; border-color: #0dcaf0; border-radius: 6px; padding: 5px 10px; margin-right: 5px;">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="{{ route('store.discounts.destroy', $discount->id) }}" onclick="return confirm('{{ __('messages.confirm_delete') ?? 'Are you sure you want to delete this?' }}')" class="btn btn-sm btn-outline-danger" style="color: #dc3545; border-color: #dc3545; border-radius: 6px; padding: 5px 10px;">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 40px 0; color: #9aa4bf;">
                                <i class="fa fa-tags" style="font-size: 48px; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                                {{ __('messages.no_discounts_found') ?? 'You have not created any discount codes yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($discounts, 'links'))
            <div style="margin-top: 20px;">
                {!! $discounts->links() !!}
            </div>
        @endif
    </div>
</div>
@endsection
