<?php

namespace App\Http\Controllers;

use App\Models\BillingOrder;
use App\Models\BillingTransaction;
use App\Services\Billing\BillingCurrencyService;
use App\Services\Billing\BillingGatewayRegistry;
use App\Services\Billing\SubscriptionEntitlementService;
use App\Services\Billing\SubscriptionLifecycleService;
use App\Services\Billing\SubscriptionPlanService;
use App\Services\V420SchemaService;
use App\Support\SubscriptionGatewaySettings;
use App\Support\SubscriptionSettings;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class BillingController extends Controller
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly BillingGatewayRegistry $gateways,
        private readonly BillingCurrencyService $currencies,
        private readonly SubscriptionPlanService $plans,
        private readonly SubscriptionLifecycleService $lifecycle,
        private readonly SubscriptionEntitlementService $entitlements
    ) {
    }

    public function plans(Request $request)
    {
        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            abort(404);
        }

        $search = trim((string) $request->query('search', ''));
        $plans = $this->plans->activePlans($search);
        $currentSubscription = auth()->check()
            ? $this->entitlements->activeSubscriptionFor(auth()->id())
            : null;

        $this->seo([
            'scope_key' => 'billing_plans',
            'resource_title' => __('messages.billing_plans_title'),
            'description' => __('messages.billing_plans_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.billing_plans_title'), 'url' => route('billing.plans')],
            ],
        ]);

        return view('theme::billing.plans', [
            'plans' => $plans,
            'search' => $search,
            'gatewayDefinitions' => $this->gateways->definitionsForAdmin(),
            'currentSubscription' => $currentSubscription,
            'activeCurrencies' => $this->currencies->all(true),
            'systemEnabled' => SubscriptionSettings::isEnabled(),
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();
        $featureAvailable = $this->schema->supports('subscriptions_billing');
        $upgradeNotice = $this->schema->notice('subscriptions_billing', __('messages.billing_feature_title'));
        $orders = $this->emptyPaginator(10);
        $transactions = $this->emptyPaginator(10);
        $currentSubscription = null;
        $queuedSubscription = null;

        if ($featureAvailable) {
            $state = $this->lifecycle->syncUserSubscriptions($user->id);
            $currentSubscription = $state['active'] ?? null;
            $queuedSubscription = $state['queued'] ?? null;

            try {
                $orders = BillingOrder::query()
                    ->where('user_id', $user->id)
                    ->with(['plan', 'subscription'])
                    ->latest()
                    ->paginate(10, ['*'], 'orders_page')
                    ->withQueryString();

                $transactions = BillingTransaction::query()
                    ->where('user_id', $user->id)
                    ->with('order')
                    ->latest()
                    ->paginate(10, ['*'], 'tx_page')
                    ->withQueryString();
            } catch (\Throwable) {
                $orders = $this->emptyPaginator(10);
                $transactions = $this->emptyPaginator(10);
            }
        }

        return view('theme::billing.dashboard', [
            'user' => $user,
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
            'systemEnabled' => SubscriptionSettings::isEnabled(),
            'currentSubscription' => $currentSubscription,
            'queuedSubscription' => $queuedSubscription,
            'orders' => $orders,
            'transactions' => $transactions,
            'plansUrl' => route('billing.plans'),
        ]);
    }

    public function purchase(Request $request, int $plan)
    {
        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            return redirect()->route('billing.dashboard')
                ->with('error', __('messages.billing_system_disabled_notice'));
        }

        $enabledGatewayKeys = $this->gateways->enabledKeys();
        if ($enabledGatewayKeys === []) {
            return redirect()->route('billing.plans')
                ->with('error', __('messages.billing_no_gateways_available'));
        }

        $validated = $request->validate([
            'gateway' => 'required|string|in:' . implode(',', $enabledGatewayKeys),
            'currency_code' => 'required|string|max:10',
        ]);

        $planModel = $this->plans->find($plan, true);
        abort_if(!$planModel, 404);

        $currency = $this->currencies->findByCode((string) $validated['currency_code']);
        if (!$currency || !$currency->is_active) {
            return redirect()->route('billing.plans')
                ->with('error', __('messages.billing_currency_unavailable'));
        }

        $gateway = $this->gateways->get((string) $validated['gateway']);
        if (!$gateway->supportsCurrency((string) $currency->code)) {
            return redirect()->route('billing.plans')
                ->with('error', __('messages.billing_gateway_currency_not_supported'));
        }

        try {
            $order = $this->lifecycle->createOrder(auth()->user(), $planModel, (string) $validated['gateway'], (string) $currency->code);
            $checkoutPayload = $gateway->createCheckout($order);
            $order = $this->lifecycle->attachGatewayCheckout($order, $checkoutPayload);
        } catch (ValidationException $exception) {
            return redirect()->route('billing.plans')
                ->withErrors($exception->errors())
                ->withInput();
        }

        if ($order->gateway === 'bank_transfer') {
            return redirect()->route('billing.orders.show', $order->id)
                ->with('success', __('messages.billing_order_created_bank_transfer'));
        }

        return redirect()->away((string) $checkoutPayload['checkout_url']);
    }

    public function showOrder(int $order)
    {
        abort_unless($this->schema->supports('subscriptions_billing'), 404);

        $orderModel = BillingOrder::query()
            ->where('user_id', auth()->id())
            ->with(['plan', 'subscription', 'transactions'])
            ->findOrFail($order);

        return view('theme::billing.order', [
            'order' => $orderModel,
            'bankTransferConfig' => SubscriptionGatewaySettings::for('bank_transfer'),
        ]);
    }

    public function uploadReceipt(Request $request, int $order)
    {
        abort_unless($this->schema->supports('subscriptions_billing'), 404);

        $validated = $request->validate([
            'receipt' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'receipt_note' => 'nullable|string|max:500',
        ]);

        $orderModel = BillingOrder::query()
            ->where('user_id', auth()->id())
            ->findOrFail($order);

        if ($orderModel->gateway !== 'bank_transfer') {
            return redirect()->route('billing.orders.show', $order)
                ->with('error', __('messages.billing_receipt_not_required'));
        }

        if (!in_array($orderModel->status, [BillingOrder::STATUS_PENDING_RECEIPT, BillingOrder::STATUS_REJECTED], true)) {
            return redirect()->route('billing.orders.show', $order)
                ->with('error', __('messages.billing_receipt_upload_unavailable'));
        }

        $uploadDirectory = public_path('upload/billing/receipts');
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0775, true);
        }

        $extension = $request->file('receipt')->getClientOriginalExtension();
        $filename = 'billing_receipt_' . auth()->id() . '_' . time() . '.' . $extension;
        $request->file('receipt')->move($uploadDirectory, $filename);

        $this->lifecycle->uploadReceipt(
            $orderModel,
            'upload/billing/receipts/' . $filename,
            $validated['receipt_note'] ?? null
        );

        return redirect()->route('billing.orders.show', $order)
            ->with('success', __('messages.billing_receipt_uploaded'));
    }

    public function handleReturn(Request $request, string $gateway, int $order)
    {
        abort_unless($this->schema->supports('subscriptions_billing'), 404);

        $orderModel = BillingOrder::query()
            ->where('user_id', auth()->id())
            ->findOrFail($order);

        try {
            $gatewayDriver = $this->gateways->get($gateway);
            $payload = $gatewayDriver->handleReturn($request, $orderModel);
        } catch (\InvalidArgumentException) {
            abort(404);
        } catch (ValidationException $exception) {
            return redirect()->route('billing.orders.show', $orderModel->id)
                ->withErrors($exception->errors());
        }

        if (($payload['status'] ?? null) === BillingOrder::STATUS_PAID) {
            $this->lifecycle->completePaidOrder($orderModel, $payload, 'gateway_return');

            return redirect()->route('billing.dashboard')
                ->with('success', __('messages.billing_payment_success'));
        }

        $this->lifecycle->recordGatewayState($orderModel, $payload, 'gateway_return');

        return redirect()->route('billing.orders.show', $orderModel->id)
            ->with('info', __('messages.billing_payment_pending'));
    }

    public function handleWebhook(Request $request, string $gateway)
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return response()->json(['ok' => true], 202);
        }

        try {
            $gatewayDriver = $this->gateways->get($gateway);
            $payload = $gatewayDriver->handleWebhook($request);
        } catch (\InvalidArgumentException) {
            return response()->json(['ok' => false], 404);
        } catch (ValidationException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], 422);
        } catch (\Throwable) {
            return response()->json(['ok' => false], 202);
        }

        if (!$payload) {
            return response()->json(['ok' => true], 202);
        }

        $order = $this->resolveWebhookOrder($payload);
        if (!$order) {
            return response()->json(['ok' => true], 202);
        }

        if (($payload['status'] ?? null) === BillingOrder::STATUS_PAID) {
            $this->lifecycle->completePaidOrder($order, $payload, 'gateway_webhook');
        } else {
            $this->lifecycle->recordGatewayState($order, $payload, 'gateway_webhook');
        }

        return response()->json(['ok' => true]);
    }

    private function resolveWebhookOrder(array $payload): ?BillingOrder
    {
        try {
            if (!empty($payload['local_order_id'])) {
                $order = BillingOrder::query()->find((int) $payload['local_order_id']);
                if ($order) {
                    return $order;
                }
            }

            if (!empty($payload['gateway_checkout_reference'])) {
                $order = BillingOrder::query()
                    ->where('gateway_checkout_reference', (string) $payload['gateway_checkout_reference'])
                    ->first();
                if ($order) {
                    return $order;
                }
            }

            if (!empty($payload['external_transaction_id'])) {
                return BillingOrder::query()
                    ->where('gateway_reference', (string) $payload['external_transaction_id'])
                    ->first();
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    private function emptyPaginator(int $perPage = 20): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, request()->integer('page', 1), [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
