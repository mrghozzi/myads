<?php

namespace App\Http\Controllers;

use App\Models\BillingOrder;
use App\Models\BillingTransaction;
use App\Models\SubscriptionPlan;
use App\Services\Billing\BillingCurrencyService;
use App\Services\Billing\BillingGatewayRegistry;
use App\Services\Billing\SubscriptionLifecycleService;
use App\Services\Billing\SubscriptionPlanService;
use App\Services\V420SchemaService;
use App\Support\SubscriptionGatewaySettings;
use App\Support\SubscriptionSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class AdminBillingController extends Controller
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly BillingGatewayRegistry $gateways,
        private readonly BillingCurrencyService $currencies,
        private readonly SubscriptionPlanService $plans,
        private readonly SubscriptionLifecycleService $lifecycle
    ) {
    }

    public function overview()
    {
        $featureAvailable = $this->schema->supports('subscriptions_billing');
        $upgradeNotice = $this->schema->notice('subscriptions_billing', __('messages.billing_feature_title'));
        $settings = SubscriptionSettings::all();
        $recentOrders = collect();
        $summary = [
            'active_plans' => 0,
            'active_subscriptions' => 0,
            'pending_bank_transfers' => 0,
            'monthly_revenue' => 0,
        ];

        if ($featureAvailable) {
            try {
                $recentOrders = BillingOrder::query()
                    ->with(['user', 'subscription'])
                    ->latest()
                    ->take(8)
                    ->get();

                $summary = [
                    'active_plans' => SubscriptionPlan::query()->where('is_active', true)->count(),
                    'active_subscriptions' => \App\Models\MemberSubscription::query()->where('status', 'active')->count(),
                    'pending_bank_transfers' => BillingOrder::query()
                        ->where('gateway', 'bank_transfer')
                        ->where('status', BillingOrder::STATUS_PENDING_REVIEW)
                        ->count(),
                    'monthly_revenue' => (float) BillingOrder::query()
                        ->where('status', BillingOrder::STATUS_PAID)
                        ->where('paid_at', '>=', now()->subDays(30))
                        ->sum('base_amount'),
                ];
            } catch (\Throwable) {
                $recentOrders = collect();
            }
        }

        return view('admin::admin.billing.overview', [
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
            'settings' => $settings,
            'gatewayDefinitions' => $this->gateways->definitionsForAdmin(),
            'recentOrders' => $recentOrders,
            'currencies' => $this->currencies->all(true),
            'summary' => $summary,
        ]);
    }

    public function settings()
    {
        return view('admin::admin.billing.settings', [
            'featureAvailable' => $this->schema->supports('subscriptions_billing'),
            'upgradeNotice' => $this->schema->notice('subscriptions_billing', __('messages.billing_feature_title')),
            'settings' => SubscriptionSettings::all(),
            'currencies' => $this->currencies->all(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.settings')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_feature_title')));
        }

        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'base_currency_code' => 'required|string|max:10',
        ]);

        SubscriptionSettings::save($validated);

        $currency = $this->currencies->findByCode((string) $validated['base_currency_code']);
        if ($currency) {
            $this->currencies->setBaseCurrency($currency);
        }

        return redirect()->route('admin.billing.settings')
            ->with('success', __('messages.billing_settings_saved'));
    }

    public function plans(Request $request)
    {
        $featureAvailable = $this->schema->supports('subscriptions_billing');
        $upgradeNotice = $this->schema->notice('subscriptions_billing', __('messages.billing_plans_title'));
        $search = trim((string) $request->query('search', ''));
        $plans = $this->plans->paginateForAdmin($search);
        $editingPlan = $request->filled('edit')
            ? $this->plans->find($request->integer('edit'))
            : null;

        return view('admin::admin.billing.plans', [
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
            'plans' => $plans,
            'search' => $search,
            'editingPlan' => $editingPlan,
            'entitlementDefaults' => array_merge(
                $this->plans->entitlementDefaults(),
                (array) ($editingPlan?->entitlements ?? [])
            ),
        ]);
    }

    public function storePlan(Request $request): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.plans')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_plans_title')));
        }

        $validated = $this->validatePlan($request);
        $this->plans->store($validated);

        return redirect()->route('admin.billing.plans')
            ->with('success', __('messages.billing_plan_saved'));
    }

    public function updatePlan(Request $request, int $plan): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.plans')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_plans_title')));
        }

        $planModel = $this->plans->find($plan);
        abort_if(!$planModel, 404);

        $validated = $this->validatePlan($request);
        $this->plans->update($planModel, $validated);

        return redirect()->route('admin.billing.plans')
            ->with('success', __('messages.billing_plan_saved'));
    }

    public function orders(Request $request)
    {
        $featureAvailable = $this->schema->supports('subscriptions_billing');
        $upgradeNotice = $this->schema->notice('subscriptions_billing', __('messages.billing_orders_title'));
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $gateway = trim((string) $request->query('gateway', ''));
        $orders = $this->emptyPaginator();

        if ($featureAvailable) {
            try {
                $orders = BillingOrder::query()
                    ->with(['user', 'subscription'])
                    ->when($search !== '', function ($query) use ($search) {
                        $query->where(function ($inner) use ($search) {
                            $inner->where('order_number', 'like', '%' . $search . '%')
                                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'));
                        });
                    })
                    ->when($status !== '', fn ($query) => $query->where('status', $status))
                    ->when($gateway !== '', fn ($query) => $query->where('gateway', $gateway))
                    ->latest()
                    ->paginate(20)
                    ->withQueryString();
            } catch (\Throwable) {
                $orders = $this->emptyPaginator();
            }
        }

        return view('admin::admin.billing.orders', [
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'gateway' => $gateway,
            'gateways' => $this->gateways->definitionsForAdmin(),
        ]);
    }

    public function showOrder(int $order)
    {
        $featureAvailable = $this->schema->supports('subscriptions_billing');
        $upgradeNotice = $this->schema->notice('subscriptions_billing', __('messages.billing_orders_title'));
        $orderModel = null;

        if ($featureAvailable) {
            try {
                $orderModel = BillingOrder::query()
                    ->with(['user', 'plan', 'subscription', 'transactions'])
                    ->findOrFail($order);
            } catch (\Throwable) {
                $orderModel = null;
            }
        }

        return view('admin::admin.billing.order_show', [
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
            'order' => $orderModel,
            'bankTransferConfig' => SubscriptionGatewaySettings::for('bank_transfer'),
        ]);
    }

    public function reviewOrder(Request $request, int $order): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.orders')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_orders_title')));
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $orderModel = BillingOrder::query()->findOrFail($order);

        if ($orderModel->gateway !== 'bank_transfer' || !$orderModel->isAwaitingManualReview()) {
            throw ValidationException::withMessages([
                'action' => __('messages.billing_order_review_unavailable'),
            ]);
        }

        $this->lifecycle->reviewBankTransfer($orderModel, (string) $validated['action'], $validated['admin_note'] ?? null);

        return redirect()->route('admin.billing.orders.show', $order)
            ->with('success', __('messages.billing_order_review_saved'));
    }

    public function transactions(Request $request)
    {
        $featureAvailable = $this->schema->supports('subscriptions_billing');
        $upgradeNotice = $this->schema->notice('subscriptions_billing', __('messages.billing_transactions_title'));
        $search = trim((string) $request->query('search', ''));
        $transactions = $this->emptyPaginator();

        if ($featureAvailable) {
            try {
                $transactions = BillingTransaction::query()
                    ->with(['user', 'order'])
                    ->when($search !== '', function ($query) use ($search) {
                        $query->where(function ($inner) use ($search) {
                            $inner->where('external_transaction_id', 'like', '%' . $search . '%')
                                ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('order_number', 'like', '%' . $search . '%'));
                        });
                    })
                    ->latest()
                    ->paginate(20)
                    ->withQueryString();
            } catch (\Throwable) {
                $transactions = $this->emptyPaginator();
            }
        }

        return view('admin::admin.billing.transactions', [
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
            'transactions' => $transactions,
            'search' => $search,
        ]);
    }

    public function currencies(Request $request)
    {
        $editingCurrency = $request->filled('edit')
            ? $this->currencies->find($request->integer('edit'))
            : null;

        return view('admin::admin.billing.currencies', [
            'featureAvailable' => $this->schema->supports('subscriptions_billing'),
            'upgradeNotice' => $this->schema->notice('subscriptions_billing', __('messages.billing_currencies_title')),
            'currencies' => $this->currencies->all(),
            'editingCurrency' => $editingCurrency,
        ]);
    }

    public function storeCurrency(Request $request): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.currencies')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_currencies_title')));
        }

        $validated = $this->validateCurrency($request, null);
        $this->currencies->store($validated);

        return redirect()->route('admin.billing.currencies')
            ->with('success', __('messages.billing_currency_saved'));
    }

    public function updateCurrency(Request $request, int $currency): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.currencies')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_currencies_title')));
        }

        $currencyModel = $this->currencies->find($currency);
        abort_if(!$currencyModel, 404);

        $validated = $this->validateCurrency($request, $currencyModel->id);
        $this->currencies->update($currencyModel, $validated);

        return redirect()->route('admin.billing.currencies')
            ->with('success', __('messages.billing_currency_saved'));
    }

    public function deleteCurrency(int $currency): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.currencies')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_currencies_title')));
        }

        $currencyModel = $this->currencies->find($currency);
        abort_if(!$currencyModel, 404);

        try {
            $this->currencies->delete($currencyModel);
        } catch (\Throwable $exception) {
            return redirect()->route('admin.billing.currencies')
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.billing.currencies')
            ->with('success', __('messages.billing_currency_deleted'));
    }

    public function setBaseCurrency(int $currency): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.currencies')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_currencies_title')));
        }

        $currencyModel = $this->currencies->find($currency);
        abort_if(!$currencyModel, 404);
        $this->currencies->setBaseCurrency($currencyModel);

        return redirect()->route('admin.billing.currencies')
            ->with('success', __('messages.billing_currency_base_saved'));
    }

    public function gateways()
    {
        return view('admin::admin.billing.gateways', [
            'featureAvailable' => $this->schema->supports('subscriptions_billing'),
            'upgradeNotice' => $this->schema->notice('subscriptions_billing', __('messages.billing_gateways_title')),
            'gatewayDefinitions' => $this->gateways->definitionsForAdmin(),
            'currencies' => $this->currencies->all(true),
        ]);
    }

    public function updateGateway(Request $request, string $gateway): RedirectResponse
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return redirect()->route('admin.billing.gateways')
                ->with('error', $this->schema->blockedActionMessage('subscriptions_billing', __('messages.billing_gateways_title')));
        }

        $validated = $this->validateGateway($request, $gateway);
        SubscriptionGatewaySettings::save($gateway, $validated);

        return redirect()->route('admin.billing.gateways')
            ->with('success', __('messages.billing_gateway_saved'));
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'duration_days' => 'nullable|integer|min:1|max:3650',
            'is_lifetime' => 'nullable|boolean',
            'base_price' => 'required|numeric|min:0|max:999999',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'accent_color' => 'nullable|string|max:20',
            'recommended_text' => 'nullable|string|max:100',
            'marketing_bullets_text' => 'nullable|string|max:5000',
            'profile_badge_label' => 'nullable|string|max:100',
            'profile_badge_color' => 'nullable|string|max:20',
            'bonus_pts' => 'nullable|numeric|min:0|max:999999',
            'bonus_nvu' => 'nullable|numeric|min:0|max:999999',
            'bonus_nlink' => 'nullable|numeric|min:0|max:999999',
            'bonus_nsmart' => 'nullable|numeric|min:0|max:999999',
            'status_promotion_discount_pct' => 'nullable|numeric|min:0|max:95',
        ]);
    }

    private function validateCurrency(Request $request, ?int $ignoreId): array
    {
        $uniqueRule = 'unique:billing_currencies,code';
        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:10', $uniqueRule],
            'name' => 'nullable|string|max:100',
            'symbol' => 'nullable|string|max:16',
            'exchange_rate' => 'required|numeric|min:0.000001|max:999999',
            'decimal_places' => 'nullable|integer|min:0|max:4',
            'is_active' => 'nullable|boolean',
            'is_base' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999999',
        ]);
    }

    private function validateGateway(Request $request, string $gateway): array
    {
        $baseRules = [
            'enabled' => 'nullable|boolean',
            'supported_currencies' => 'nullable|array',
            'supported_currencies.*' => 'string|max:10',
        ];

        $rules = match ($gateway) {
            'stripe' => [
                ...$baseRules,
                'mode' => 'required|in:sandbox,live',
                'publishable_key' => 'nullable|string|max:255',
                'secret_key' => 'nullable|string|max:500',
                'webhook_secret' => 'nullable|string|max:500',
            ],
            'paypal' => [
                ...$baseRules,
                'mode' => 'required|in:sandbox,live',
                'client_id' => 'nullable|string|max:255',
                'secret_key' => 'nullable|string|max:500',
                'webhook_id' => 'nullable|string|max:255',
            ],
            'bank_transfer' => [
                ...$baseRules,
                'instructions' => 'nullable|string|max:10000',
                'note' => 'nullable|string|max:3000',
            ],
            default => abort(404),
        };

        return $request->validate($rules);
    }

    private function emptyPaginator(int $perPage = 20): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, request()->integer('page', 1), [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
