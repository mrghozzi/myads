<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPlan;
use App\Services\V420SchemaService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SubscriptionPlanService
{
    public const ENTITLEMENT_KEYS = [
        'profile_badge_label',
        'profile_badge_color',
        'bonus_pts',
        'bonus_nvu',
        'bonus_nlink',
        'bonus_nsmart',
        'status_promotion_discount_pct',
    ];

    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public function paginateForAdmin(string $search = '', int $perPage = 12): LengthAwarePaginator
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return new LengthAwarePaginator([], 0, $perPage, request()->integer('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        try {
            return SubscriptionPlan::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhere('recommended_text', 'like', '%' . $search . '%');
                    });
                })
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->paginate($perPage)
                ->withQueryString();
        } catch (\Throwable) {
            return new LengthAwarePaginator([], 0, $perPage, request()->integer('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }
    }

    public function activePlans(string $search = ''): Collection
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return collect();
        }

        try {
            return SubscriptionPlan::query()
                ->where('is_active', true)
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhere('recommended_text', 'like', '%' . $search . '%');
                    });
                })
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    public function find(int $id, bool $activeOnly = false): ?SubscriptionPlan
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return null;
        }

        try {
            return SubscriptionPlan::query()
                ->when($activeOnly, fn ($query) => $query->where('is_active', true))
                ->find($id);
        } catch (\Throwable) {
            return null;
        }
    }

    public function store(array $values): SubscriptionPlan
    {
        return SubscriptionPlan::query()->create($this->normalizeIncoming($values));
    }

    public function update(SubscriptionPlan $plan, array $values): SubscriptionPlan
    {
        $plan->fill($this->normalizeIncoming($values, $plan));
        $plan->save();

        return $plan->refresh();
    }

    public function entitlementDefaults(): array
    {
        return [
            'profile_badge_label' => '',
            'profile_badge_color' => '#615dfa',
            'bonus_pts' => 0,
            'bonus_nvu' => 0,
            'bonus_nlink' => 0,
            'bonus_nsmart' => 0,
            'status_promotion_discount_pct' => 0,
        ];
    }

    public function planSnapshot(SubscriptionPlan $plan): array
    {
        return [
            'plan_id' => (int) $plan->id,
            'name' => (string) $plan->name,
            'description' => (string) ($plan->description ?? ''),
            'duration_days' => $plan->duration_days ? (int) $plan->duration_days : null,
            'is_lifetime' => (bool) $plan->is_lifetime,
            'base_price' => (float) $plan->base_price,
            'marketing_bullets' => array_values((array) ($plan->marketing_bullets ?? [])),
            'entitlements' => array_merge($this->entitlementDefaults(), (array) ($plan->entitlements ?? [])),
            'accent_color' => (string) ($plan->accent_color ?? ''),
            'recommended_text' => (string) ($plan->recommended_text ?? ''),
            'is_featured' => (bool) $plan->is_featured,
        ];
    }

    private function normalizeIncoming(array $values, ?SubscriptionPlan $current = null): array
    {
        $defaults = $current?->entitlements ?? $this->entitlementDefaults();
        $entitlements = $this->entitlementDefaults();

        foreach (self::ENTITLEMENT_KEYS as $key) {
            $submitted = $values[$key] ?? $defaults[$key] ?? $entitlements[$key];
            $entitlements[$key] = in_array($key, ['profile_badge_label', 'profile_badge_color'], true)
                ? trim((string) $submitted)
                : max(0, round((float) $submitted, 2));
        }

        return [
            'name' => trim((string) ($values['name'] ?? $current?->name ?? '')),
            'description' => trim((string) ($values['description'] ?? $current?->description ?? '')),
            'duration_days' => !empty($values['is_lifetime']) ? null : max(1, (int) ($values['duration_days'] ?? $current?->duration_days ?? 30)),
            'is_lifetime' => !empty($values['is_lifetime']) ? 1 : 0,
            'base_price' => max(0, round((float) ($values['base_price'] ?? $current?->base_price ?? 0), 2)),
            'is_featured' => !empty($values['is_featured']) ? 1 : 0,
            'is_active' => !empty($values['is_active']) ? 1 : 0,
            'sort_order' => max(0, (int) ($values['sort_order'] ?? $current?->sort_order ?? 0)),
            'accent_color' => trim((string) ($values['accent_color'] ?? $current?->accent_color ?? '')),
            'recommended_text' => trim((string) ($values['recommended_text'] ?? $current?->recommended_text ?? '')),
            'marketing_bullets' => $this->normalizeBulletList($values['marketing_bullets_text'] ?? $values['marketing_bullets'] ?? ($current?->marketing_bullets ?? [])),
            'entitlements' => $entitlements,
        ];
    }

    private function normalizeBulletList(array|string $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $value) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
