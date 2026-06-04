<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;
use App\Models\Link;
use App\Models\Visit;
use App\Models\SmartAd;
use App\Models\Option;
use App\Models\Setting;
use App\Models\User;
use App\Support\SmartAdsSettings;

class HomeController extends Controller
{
    public function index()
    {
        $this->noindex([
            'scope_key' => 'dashboard',
        ]);

        $user = Auth::user();
        
        $bannerStats = [
            'vu' => Banner::where('uid', $user->id)->sum('vu'),
            'clik' => Banner::where('uid', $user->id)->sum('clik'),
        ];
        
        $linkStats = [
            'clik' => Link::where('uid', $user->id)->sum('clik'),
        ];
        
        $visitStats = [
            'vu' => Visit::where('uid', $user->id)->sum('vu'),
        ];

        $smartAdStats = [
            'impressions' => SmartAd::where('uid', $user->id)->sum('impressions'),
            'clicks' => SmartAd::where('uid', $user->id)->sum('clicks'),
            'total' => SmartAd::where('uid', $user->id)->count(),
        ];
        
        $site_settings = Setting::first();

        return view('theme::home', compact('user', 'bannerStats', 'linkStats', 'visitStats', 'smartAdStats', 'site_settings'));
    }

    public function convertPoints(Request $request)
    {
        $user = Auth::user();
        $points = (int) $request->input('pts');
        $type = $request->input('to');

        // SECURITY: Validate type against whitelist before doing anything
        $validTypes = ['link', 'banners', 'exchv', 'smartads'];
        if (!in_array($type, $validTypes, true)) {
            return redirect()->back()->with('errMSG', __('messages.invalid_conversion_type'));
        }

        // Validation
        if ($points <= 0) {
            return redirect()->back()->with('errMSG', __('cnc0p'));
        }

        // SECURITY: Use DB transaction with pessimistic locking to prevent race conditions / double-spend
        try {
            return DB::transaction(function () use ($user, $points, $type) {
                // Re-read user with lock to prevent concurrent manipulation
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

                if ($lockedUser->pts < $points) {
                    return redirect()->back()->with('errMSG', __('tnopmtrnon') . " : " . $lockedUser->pts);
                }

                // Determine operation details
                $o_type = "hest_pts";
                $bn_desc = "-" . $points;
                $bn_name = "";

                if ($type == "link") {
                    $bn_name = "tostads";
                } elseif ($type == "banners") {
                    $bn_name = "towthbaner";
                } elseif ($type == "exchv") {
                    $bn_name = "toexchvisi";
                } elseif ($type == "smartads") {
                    $bn_name = "tosmartads";
                }

                // Insert into options
                Option::create([
                    'name' => $bn_name,
                    'o_valuer' => $bn_desc,
                    'o_type' => $o_type,
                    'o_parent' => $lockedUser->id,
                    'o_order' => 0,
                    'o_mode' => time(),
                ]);

                // Update User Points and Stats atomically
                if ($type == "link") {
                    $le_go = $points / 2;
                    $lockedUser->nlink += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();
                    
                    $msg = str_replace("[le_go]", $le_go, __('phbdp'));
                    $msg = str_replace("[le_name]", $points, $msg);
                    return redirect()->route('dashboard')->with('MSG', $msg);

                } elseif ($type == "banners") {
                    $le_go = $points / 2;
                    $lockedUser->nvu += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = str_replace("[le_go]", $le_go, __('phbdb'));
                    $msg = str_replace("[le_name]", $points, $msg);
                    return redirect()->route('dashboard')->with('MSG', $msg);

                } elseif ($type == "exchv") {
                    $le_go = $points / 4;
                    $lockedUser->vu += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = str_replace("[le_go]", $le_go, __('phbdv'));
                    $msg = str_replace("[le_name]", $points, $msg);
                    return redirect()->route('dashboard')->with('MSG', $msg);
                } elseif ($type == "smartads") {
                    $le_go = $points / SmartAdsSettings::pointsDivisor();
                    $lockedUser->nsmart += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = __('messages.smart_points_converted', [
                        'points' => $points,
                        'credits' => rtrim(rtrim(number_format($le_go, 2, '.', ''), '0'), '.'),
                    ]);
                    return redirect()->route('dashboard')->with('MSG', $msg);
                }

                return redirect()->route('dashboard');
            });
        } catch (\Exception $e) {
            \Log::error('Point Conversion Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('errMSG', __('messages.error_occurred'));
        }
    }
}
