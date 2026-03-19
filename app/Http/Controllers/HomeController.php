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
use App\Support\SmartAdsSettings;

class HomeController extends Controller
{
    public function index()
    {
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
        $points = $request->input('pts');
        $type = $request->input('to');

        // Validation
        if ($points < 0 || !is_numeric($points)) {
            $points = 0;
        }

        if ($user->pts < $points) {
            return redirect()->back()->with('errMSG', __('tnopmtrnon') . " : " . $user->pts);
        } elseif ($points == 0) {
            return redirect()->back()->with('errMSG', __('cnc0p'));
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
            'o_parent' => $user->id,
            'o_order' => 0,
            'o_mode' => time(),
        ]);

        // Update User Points and Stats
        if ($type == "link") {
            $le_go = $points / 2;
            $user->nlink += $le_go;
            $user->pts -= $points;
            $user->save();
            
            $msg = str_replace("[le_go]", $le_go, __('phbdp'));
            $msg = str_replace("[le_name]", $points, $msg);
            return redirect()->route('dashboard')->with('MSG', $msg);

        } elseif ($type == "banners") {
            $le_go = $points / 2;
            $user->nvu += $le_go;
            $user->pts -= $points;
            $user->save();

            $msg = str_replace("[le_go]", $le_go, __('phbdb'));
            $msg = str_replace("[le_name]", $points, $msg);
            return redirect()->route('dashboard')->with('MSG', $msg);

        } elseif ($type == "exchv") {
            $le_go = $points / 4;
            $user->vu += $le_go;
            $user->pts -= $points;
            $user->save();

            $msg = str_replace("[le_go]", $le_go, __('phbdv'));
            $msg = str_replace("[le_name]", $points, $msg);
            return redirect()->route('dashboard')->with('MSG', $msg);
        } elseif ($type == "smartads") {
            $le_go = $points / SmartAdsSettings::pointsDivisor();
            $user->nsmart += $le_go;
            $user->pts -= $points;
            $user->save();

            $msg = __('messages.smart_points_converted', [
                'points' => $points,
                'credits' => rtrim(rtrim(number_format($le_go, 2, '.', ''), '0'), '.'),
            ]);
            return redirect()->route('dashboard')->with('MSG', $msg);
        }

        return redirect()->route('dashboard');
    }
}
