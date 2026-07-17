<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BannerImpression;
use App\Models\SmartAdImpression;
use App\Models\Visit;

class AdsStatsApiController extends Controller
{
    public function stats(Request $request)
    {
        $user = Auth::user();
        
        // Surf to earn (visits)
        $todayVisits = Visit::where('uid', $user->id)
            ->whereDate('date', today())
            ->count();

        $totalVisits = Visit::where('uid', $user->id)
            ->count();

        // Banner Ads
        $bannerImpressions = BannerImpression::whereHas('banner', function($q) use ($user) {
                $q->where('uid', $user->id);
            })->count();

        // Smart Ads
        $smartImpressions = SmartAdImpression::whereHas('smartAd', function($q) use ($user) {
                $q->where('uid', $user->id);
            })->count();

        return response()->json([
            'success' => true,
            'data' => [
                'visits' => [
                    'today' => $todayVisits,
                    'total' => $totalVisits,
                ],
                'ads' => [
                    'banner_impressions' => $bannerImpressions,
                    'smart_impressions' => $smartImpressions,
                ],
                'wallet' => [
                    'pts' => $user->pts,
                ]
            ]
        ]);
    }
}
