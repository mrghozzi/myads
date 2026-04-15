# Developer Guide: Paid Subscriptions and Billing in MYADS

## 1. Purpose

This system adds an optional paid subscriptions layer to MYADS.

Administrators can:

- Enable or disable the billing system from the admin panel.
- Create and manage paid plans.
- Manage billing currencies and manual exchange rates.
- Configure supported payment gateways.
- Review bank transfer orders manually.

Members can:

- Browse plans on `/plans`.
- Review their current or queued subscription on `/settings/billing`.
- Purchase through externally hosted payment pages.
- Upload only a receipt when using `Bank Transfer`.

## 2. Core Design Rules

- The billing system is optional and can be disabled globally.
- The current release supports only:
  - `Stripe`
  - `PayPal`
  - `Bank Transfer`
- There is no `auto-renew` in the current release.
- MYADS must not store card data or sensitive payment identity data.
- `Stripe` and `PayPal` must use hosted checkout only.
- `Bank Transfer` uses plain-text instructions plus a receipt image upload.
- Any code that depends on the new billing tables must follow the project compatibility pattern:
  - `try/catch`
  - `V420SchemaService`

## 3. Main File Locations

### Controllers

- `app/Http/Controllers/BillingController.php`
- `app/Http/Controllers/AdminBillingController.php`

### Models

- `app/Models/SubscriptionPlan.php`
- `app/Models/MemberSubscription.php`
- `app/Models/BillingOrder.php`
- `app/Models/BillingTransaction.php`
- `app/Models/BillingCurrency.php`

### Services

- `app/Services/Billing/BillingGatewayRegistry.php`
- `app/Services/Billing/BillingCurrencyService.php`
- `app/Services/Billing/SubscriptionPlanService.php`
- `app/Services/Billing/SubscriptionLifecycleService.php`
- `app/Services/Billing/SubscriptionEntitlementService.php`

### Gateway Layer

- `app/Services/Billing/Gateways/BillingGatewayInterface.php`
- `app/Services/Billing/Gateways/AbstractBillingGateway.php`
- `app/Services/Billing/Gateways/StripeGateway.php`
- `app/Services/Billing/Gateways/PayPalGateway.php`
- `app/Services/Billing/Gateways/BankTransferGateway.php`

### Support / Settings

- `app/Support/SubscriptionSettings.php`
- `app/Support/SubscriptionGatewaySettings.php`

### Views

#### Member views

- `themes/default/views/billing/plans.blade.php`
- `themes/default/views/billing/dashboard.blade.php`
- `themes/default/views/billing/order.blade.php`
- `themes/default/views/billing/partials/*`

#### Admin views

- `admin_themes/default/views/admin/billing/*`

### Migration

- `database/migrations/2026_04_14_130000_create_billing_subscription_tables.php`

### Tests

- `tests/Feature/BillingFeatureTest.php`

## 4. New Database Tables

### `subscription_plans`

Stores the plan definition itself:

- `name`
- `description`
- `duration_days`
- `is_lifetime`
- `base_price`
- `is_featured`
- `is_active`
- `sort_order`
- `accent_color`
- `recommended_text`
- `marketing_bullets`
- `entitlements`

### `billing_currencies`

Stores billing currencies available in the system:

- `code`
- `name`
- `symbol`
- `exchange_rate`
- `decimal_places`
- `is_active`
- `is_base`
- `sort_order`

### `billing_orders`

Represents a paid plan purchase order:

- `order_number`
- `user_id`
- `subscription_plan_id`
- `member_subscription_id`
- `gateway`
- `status`
- `base_currency_code`
- `currency_code`
- `base_amount`
- `display_amount`
- `exchange_rate_snapshot`
- `gateway_checkout_reference`
- `gateway_reference`
- `receipt_path`
- `receipt_note`
- `admin_note`
- `paid_at`
- `approved_at`
- `rejected_at`
- `expires_at`
- `plan_snapshot`
- `meta`

### `member_subscriptions`

Represents the resulting subscription after payment:

- `user_id`
- `subscription_plan_id`
- `billing_order_id`
- `queued_from_subscription_id`
- `status`
- `plan_name`
- `plan_snapshot`
- `entitlements_snapshot`
- `starts_at`
- `ends_at`
- `activated_at`
- `benefits_applied_at`
- `completed_at`
- `meta`

### `billing_transactions`

Stores the minimal payment event log:

- `billing_order_id`
- `user_id`
- `gateway`
- `transaction_type`
- `status`
- `external_transaction_id`
- `amount`
- `currency_code`
- `exchange_rate_snapshot`
- `processed_at`
- `meta`

## 5. Settings Stored in `options`

### `subscription_settings`

Managed through `App\Support\SubscriptionSettings`.

Current keys:

- `enabled`
- `base_currency_code`

### `subscription_gateway_settings`

Managed through `App\Support\SubscriptionGatewaySettings`.

Each gateway has its own stored payload, for example:

- `enabled`
- `mode`
- `supported_currencies`
- provider keys and secrets
- `instructions` and `note` for bank transfer

Important notes:

- Secrets are encrypted with Laravel `Crypt`.
- Sensitive values are masked in the admin UI.
- Leaving a secret field blank during save means: keep the currently stored value.

## 6. Main Routes

### Member

- `GET /plans` -> `billing.plans`
- `GET /settings/billing` -> `billing.dashboard`
- `POST /plans/{plan}/purchase` -> `billing.purchase`
- `GET /billing/orders/{order}` -> `billing.orders.show`
- `POST /billing/orders/{order}/receipt` -> `billing.orders.receipt.update`
- `GET /billing/return/{gateway}/{order}` -> `billing.return`
- `POST /billing/webhook/{gateway}` -> `billing.webhook`

### Admin

All admin billing routes live under `/admin/billing/*`:

- `admin.billing.overview`
- `admin.billing.settings`
- `admin.billing.settings.update`
- `admin.billing.plans`
- `admin.billing.plans.store`
- `admin.billing.plans.update`
- `admin.billing.orders`
- `admin.billing.orders.show`
- `admin.billing.orders.review`
- `admin.billing.transactions`
- `admin.billing.currencies`
- `admin.billing.currencies.store`
- `admin.billing.currencies.update`
- `admin.billing.currencies.delete`
- `admin.billing.currencies.base`
- `admin.billing.gateways`
- `admin.billing.gateways.update`

## 7. Order and Subscription Statuses

### `billing_orders` statuses

- `pending_checkout`
- `pending_receipt`
- `pending_review`
- `paid`
- `rejected`
- `cancelled`
- `failed`

### `member_subscriptions` statuses

- `active`
- `queued`
- `expired`
- `cancelled`
- `rejected`

## 8. Main Payment Flows

### 8.1 Stripe or PayPal purchase

1. The member selects a plan, gateway, and currency on `/plans`.
2. `BillingController@purchase` verifies:
   - the system is enabled
   - at least one gateway is enabled
   - the plan is valid
   - the currency is valid
   - the selected gateway supports that currency
3. `SubscriptionLifecycleService::createOrder()` creates a `billing_orders` row.
4. The gateway creates a hosted checkout session/order.
5. When the user returns from the provider or the webhook arrives:
   - the order is marked `paid` on success
   - the member subscription is created, extended, or queued

### 8.2 Bank Transfer purchase

1. The order is created with `pending_receipt`.
2. The member uploads a receipt from the order details page.
3. The order moves to `pending_review`.
4. The admin either:
   - approves it -> order becomes `paid` and the subscription is activated
   - rejects it -> order becomes `rejected` with `admin_note` and `rejected_at`

## 9. Subscription Lifecycle Logic

The main logic lives in:

- `SubscriptionLifecycleService`
- `SubscriptionEntitlementService`

Current behavior:

- If there is no active subscription, a new `active` subscription is created.
- If the member buys the same plan again, `ends_at` is extended.
- If the member buys a different plan while another plan is active, a new `queued` subscription is created.
- When the active subscription ends, the queued one is activated.

## 10. Entitlements

The current supported entitlement keys are:

- `profile_badge_label`
- `profile_badge_color`
- `bonus_pts`
- `bonus_nvu`
- `bonus_nlink`
- `bonus_nsmart`
- `status_promotion_discount_pct`

### Where they are applied

- `bonus_pts` through `PointLedgerService`
- ad credits through user columns:
  - `nvu`
  - `nlink`
  - `nsmart`
- profile badge through:
  - `SubscriptionEntitlementService::activeProfileBadgeForUserId()`
- promotion discount through:
  - `StatusPromotionPricingService`

## 11. How to Add a New Payment Gateway

Use this order:

1. Create a new class in:
   - `app/Services/Billing/Gateways/`
2. Implement:
   - `BillingGatewayInterface`
3. Register it in:
   - `BillingGatewayRegistry::all()`
4. Add its defaults to:
   - `SubscriptionGatewaySettings::DEFAULTS`
5. Add its secret fields to:
   - `SubscriptionGatewaySettings::SECRET_FIELDS`
6. Update validation in:
   - `AdminBillingController::validateGateway()`
7. Add its admin form fields to:
   - `admin_themes/default/views/admin/billing/gateways.blade.php`
8. Add translation keys in all supported languages.
9. Add feature tests for its main paths.

### Required contract

```php
public function key(): string;
public function label(): string;
public function supportsCurrency(string $currencyCode): bool;
public function createCheckout(BillingOrder $order): array;
public function handleReturn(Request $request, BillingOrder $order): array;
public function handleWebhook(Request $request): ?array;
public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array;
public function maskConfig(array $config): array;
```

## 12. How to Add a New Plan Entitlement

If you want to add a new entitlement:

1. Add it to:
   - `SubscriptionPlanService::ENTITLEMENT_KEYS`
   - `SubscriptionPlanService::entitlementDefaults()`
   - `SubscriptionEntitlementService::entitlementsForSubscription()`
2. Add its field to the admin plan form:
   - `admin_themes/default/views/admin/billing/plans.blade.php`
3. Update validation in:
   - `AdminBillingController::validatePlan()`
4. If it changes behavior, implement it in:
   - `SubscriptionEntitlementService`
   - or the service that consumes that entitlement
5. Add translations in all supported languages.
6. Add or update tests for the new behavior.

## 13. UI Development Notes

### Member UI

Directory:

- `themes/default/views/billing/`

Important rules:

- `/plans` must be hidden completely when billing is disabled.
- `/settings/billing` must remain accessible to the member even when billing is disabled, so they can review history.
- Bank transfer instructions must stay escaped plain text, not raw HTML.

### Admin UI

Directory:

- `admin_themes/default/views/admin/billing/`

Important rules:

- Follow the existing Duralux language and component patterns.
- Reuse the shared billing partials:
  - `partials/nav.blade.php`
  - `partials/alerts.blade.php`
  - `partials/status_badge.blade.php`

## 14. Security and Privacy Rules

These rules must remain true:

- Never store card details.
- Never ask the member for unnecessary personal payment data.
- Store only:
  - external references
  - payment status
  - amount
  - currency
  - exchange-rate snapshot
  - sanitized `meta`
- Store receipt uploads only in:
  - `public/upload/billing/receipts/`
- Validate uploads with:
  - `image`
  - `mimes: jpg,jpeg,png,webp`
  - `max:4096`
- Only billing webhooks are exempt from CSRF:
  - `billing/webhook/*`

## 15. Upgrade Compatibility Rules

Any new billing-dependent code must respect:

- `V420SchemaService::supports('subscriptions_billing')`
- `try/catch` around queries that depend on the new tables
- showing an `upgradeNotice` instead of crashing with `500`

This matters because MYADS supports installations that may be partially upgraded.

## 16. Tests

Current reference:

- `tests/Feature/BillingFeatureTest.php`

It currently covers:

- `/plans` visible vs hidden
- `/settings/billing` still accessible when the system is disabled
- `Bank Transfer` purchase + receipt upload + admin approval
- bank transfer rejection with `admin_note` and `rejected_at`
- same-plan extension
- different-plan queueing
- `billing` ACL enforcement
- fallback behavior when billing tables are missing

Any new billing development should update or extend these tests instead of leaving behavior uncovered.

## 17. Pre-Merge Checklist

- Did you add translations to all nine locales?
- Did you verify both LTR and RTL rendering?
- Did you keep business logic in services and controllers thin?
- Did you respect `V420SchemaService`?
- Did you avoid storing sensitive payment data?
- Did you preserve hosted checkout for external gateways?
- Did you update `Agents.md` if architecture, routes, or tables changed?
- Did you update this guide if the extension path or development workflow changed?

