<?php

namespace App\Services;

use App\Models\User;
use App\Models\MemberSubscription;
use Carbon\Carbon;

class DeveloperEligibilityService
{
    protected DeveloperPlatformSettings $settings;

    public function __construct(DeveloperPlatformSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Check if the user is eligible to create a developer app.
     * Returns an array with 'eligible' (bool) and 'reason' (string) if not eligible.
     */
    public function checkEligibility(User $user): array
    {
        if (!$this->settings->isEnabled()) {
            return ['eligible' => false, 'reason' => 'platform_disabled'];
        }

        // Admin bypass
        if ($user->id == 1) {
            return ['eligible' => true];
        }

        // Check account age
        $minAgeDays = $this->settings->getMinAccountAgeDays();
        if ($minAgeDays > 0) {
            $createdAt = $user->created_at ? Carbon::parse($user->created_at) : null;
            $accountAge = $createdAt ? $createdAt->diffInDays(now()) : 0;
            if ($accountAge < $minAgeDays) {
                return ['eligible' => false, 'reason' => 'min_account_age_days', 'required_days' => $minAgeDays];
            }
        }

        // Check followers count
        $minFollowers = $this->settings->getMinFollowersCount();
        if ($minFollowers > 0) {
            $followersCount = \App\Models\Like::where('user2', $user->id)->count();
            if ($followersCount < $minFollowers) {
                return ['eligible' => false, 'reason' => 'min_followers_count', 'required_followers' => $minFollowers];
            }
        }

        // Check paid plan
        if ($this->settings->requirePaidPlan()) {
            $hasValidPlan = false;
            if (class_exists(MemberSubscription::class)) {
                $activeSubscription = MemberSubscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

                if ($activeSubscription) {
                    $eligiblePlanIds = $this->settings->getEligiblePlanIds();
                    if (empty($eligiblePlanIds) || in_array($activeSubscription->plan_id, $eligiblePlanIds)) {
                        $hasValidPlan = true;
                    }
                }
            }

            if (!$hasValidPlan) {
                return ['eligible' => false, 'reason' => 'require_paid_plan'];
            }
        }

        return ['eligible' => true];
    }
}
