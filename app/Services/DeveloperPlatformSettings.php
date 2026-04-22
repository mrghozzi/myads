<?php

namespace App\Services;

use App\Models\Option;

class DeveloperPlatformSettings
{
    private const PREFIX = 'dev_platform_';

    public function isEnabled(): bool
    {
        return (bool) $this->get('enabled', false);
    }

    public function requireAdminApproval(): bool
    {
        return (bool) $this->get('require_admin_approval', true);
    }

    public function getMinAccountAgeDays(): int
    {
        return (int) $this->get('min_account_age_days', 0);
    }

    public function getMinFollowersCount(): int
    {
        return (int) $this->get('min_followers_count', 0);
    }

    public function requirePaidPlan(): bool
    {
        return (bool) $this->get('require_paid_plan', false);
    }

    public function getEligiblePlanIds(): array
    {
        $val = $this->get('eligible_plan_ids', '[]');
        return json_decode($val, true) ?? [];
    }

    public function get(string $key, $default = null)
    {
        $option = Option::where('name', self::PREFIX . $key)->first();
        return $option ? $option->o_valuer : $default;
    }

    public function set(string $key, $value): void
    {
        Option::updateOrCreate(
            ['name' => self::PREFIX . $key],
            ['o_valuer' => is_array($value) ? json_encode($value) : $value]
        );
    }

    public function setAll(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }
}
