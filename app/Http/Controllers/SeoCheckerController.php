<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Services\SeoCheckerService;
use Illuminate\Support\Facades\Auth;
use App\Services\Billing\SubscriptionEntitlementService;

class SeoCheckerController extends Controller
{
    /**
     * Show the SEO Checker form and page.
     */
    public function index()
    {
        return view('theme::seo_checker.index');
    }

    /**
     * Analyze the URL and return results.
     */
    public function analyze(Request $request, SeoCheckerService $seoCheckerService)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');
        
        // Analyze URL
        $results = $seoCheckerService->analyzeUrl($url);

        // Fetch settings for permissions
        $option = Option::where('name', 'seo_checker_settings')->first();
        $settings = $option ? json_decode($option->o_valuer, true) : [
            'speed' => 'guest',
            'errors' => 'member',
            'backlinks' => 'premium',
        ];

        // Determine user state
        $userRole = 'guest';
        if (Auth::check()) {
            $userRole = 'member';
            if ($this->hasPremiumSubscription()) {
                $userRole = 'premium';
            }
            // Super admins get premium access automatically
            if (Auth::id() === 1) {
                $userRole = 'premium';
            }
        }

        return view('theme::seo_checker.results', compact('results', 'settings', 'userRole'));
    }

    /**
     * Check if current user has an active premium subscription.
     */
    private function hasPremiumSubscription(): bool
    {
        try {
            $entitlementService = app(SubscriptionEntitlementService::class);
            return $entitlementService->activeSubscriptionFor(Auth::user()) !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
