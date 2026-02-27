<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Banner;
use App\Models\Link;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdsServingController extends Controller
{
    // Public: Serve Banner Script (bn.php)
    public function bannerScript(Request $request)
    {
        $user_id = $request->query('ID');
        $px = $request->query('px');
        $pxValid = is_string($px) && preg_match('/^\d{2,4}x\d{2,4}$/', $px);
        $pxValue = $pxValid ? $px : '468x60';

        if (!$user_id || !is_numeric($user_id)) {
            return response('// Invalid User ID', 200)->header('Content-Type', 'application/javascript; charset=UTF-8');
        }

        $user = User::find($user_id);
        if (!$user) {
            return response('// User not found', 200)->header('Content-Type', 'application/javascript; charset=UTF-8');
        }

        // 1. Update Publisher Points (Matches old bn.php: pts+1, nvu+.5)
        $user->increment('pts', 1);
        $user->increment('nvu', 0.5);

        // 2. Select a Banner
        // Logic: Banner from user who has nvu >= 1 and NOT same user
        $w_px = $this->getWidth($pxValue);
        
        $banner = Banner::where('statu', 1)
            ->where('px', $pxValue)
            ->whereHas('user', function ($query) use ($user_id) {
                $query->where('nvu', '>=', 1)->where('id', '!=', $user_id);
            })
            ->inRandomOrder()
            ->first();

        if ($banner) {
            // Deduct from Advertiser
            $advertiser = User::find($banner->uid);
            if ($advertiser) {
                $advertiser->decrement('nvu', 1);
            }
            $banner->increment('vu');

            // Log State
            $this->logState($user_id, $banner->id, 'banner', $request);

            // Return JS to display banner (Matches old bn.php output style)
            $url = route('ads.redirect', ['ads' => $banner->id, 'vu' => $user_id]);
            $html = "<style>.banner_{$banner->id} {background-image: url('{$banner->img}');height: {$this->getHeight($pxValue)}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_{$banner->id} a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_icon_{$banner->id} {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: {$w_px}px) {.banner_{$banner->id} {width: 100%;}}</style><div class='banner_{$banner->id}'><a href='{$url}' target='_blank'><div class='banner_icon_{$banner->id}'><a href='" . url('/') . "?ref={$user_id}' target='_blank'><img src='" . theme_asset('img/logo_w.png') . "' width='16' height='16' alt='" . config('app.name') . "'></a><a href='" . url('/report') . "?banner={$banner->id}' target='_blank'><img src='" . theme_asset('img/Alert-icon.png') . "' alt='Report'></a></div></a></div>";
            
            return response('document.write("' . addslashes($html) . '");', 200)
                ->header('Content-Type', 'application/javascript')
                ->header('Cache-Control', 'public, max-age=900')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
        } else {
            $refUrl = url('/') . "?ref=" . $user_id;
            $h_px = $this->getHeight($pxValue);
            $fallbackMap = [
                '160x600' => ['w' => 160, 'h' => 600, 'path' => 'img/banner/160x600.gif'],
                '300x250' => ['w' => 300, 'h' => 250, 'path' => 'img/banner/300x250.gif'],
                '468x60' => ['w' => 468, 'h' => 60, 'path' => 'img/banner/468x60.gif'],
                '728x90' => ['w' => 728, 'h' => 90, 'path' => 'img/banner/728x90.gif'],
            ];
            $fallbackAssets = [];
            foreach ($fallbackMap as $key => $data) {
                $fallbackAssets[$key] = ['w' => $data['w'], 'h' => $data['h'], 'src' => theme_asset($data['path'])];
            }
            $fallbackJson = json_encode($fallbackAssets);
            $pxJson = json_encode($pxValid ? $px : '');
            $fallbackValuesJson = json_encode(array_values($fallbackAssets));
            $js = "(function(){var px={$pxJson};var map={$fallbackJson};var list={$fallbackValuesJson};var w=0,h=0;var size='';if(px&&/^[0-9]+x[0-9]+$/.test(px)&&map[px]){size=px;}if(!size){var frame=window.frameElement;if(frame){w=parseInt(frame.width||frame.clientWidth||frame.offsetWidth||0,10);h=parseInt(frame.height||frame.clientHeight||frame.offsetHeight||0,10);}if((!w||!h)&&document.currentScript&&document.currentScript.parentElement){var r=document.currentScript.parentElement.getBoundingClientRect();w=parseInt(r.width,10)||0;h=parseInt(r.height,10)||0;}}var picked=null;if(size&&map[size]){picked=map[size];}else if(w&&h){for(var key in map){if(map[key].w===w&&map[key].h===h){picked=map[key];break;}}}if(!picked&&map['{$pxValue}']){picked=map['{$pxValue}'];}if(!picked&&list.length){picked=list[0];}if(!picked){picked={w:{$w_px},h:{$h_px},src:''};}var html=\"<a href='{$refUrl}' target='_blank'><img src='\"+picked.src+\"' width='\"+picked.w+\"' height='\"+picked.h+\"' border='0'></a>\";document.write(html);})();";
            return response($js, 200)
                ->header('Content-Type', 'application/javascript')
                ->header('Cache-Control', 'public, max-age=900')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
        }
    }

    // Public: Serve Link Script (link.php)
    public function linkScript(Request $request)
    {
        $user_id = $request->query('ID');
        $px = $request->query('px'); // 468x60 (1) or Responsive (2)

        if (!$user_id || !is_numeric($user_id)) {
            return response('// Invalid User ID', 200)->header('Content-Type', 'application/javascript');
        }

        $user = User::find($user_id);
        if (!$user) {
            return response('// User not found', 200)->header('Content-Type', 'application/javascript');
        }

        // 1. Update Publisher Points (Matches old link.php logic)
        $user->increment('pts', 1);
        // Note: old link.php does NOT increment nlink here, only pts. It increments nlink on CLICK (in show.php/AdsController::redirect).
        
        // 2. Select First Link ($ab)
        // Logic: Link from user who has nlink >= 1 and NOT same user
        $link1 = Link::where('statu', 1)
            ->whereHas('user', function ($query) use ($user_id) {
                $query->where('nlink', '>=', 1)->where('id', '!=', $user_id);
            })
            ->inRandomOrder()
            ->first();

        if (!$link1) {
             // If no first link found, we can't show much. Maybe default?
             // Old logic: if($results->rowCount() == 0) { } else { ... }
             // It seems it does nothing if no first link.
             // But we should probably output something or empty.
             // For now, let's output default if no links at all.
             return response('// No ads available', 200)->header('Content-Type', 'application/javascript; charset=UTF-8');
        }

        // 3. Select Second Link ($ab2)
        // Logic: Link from user who has nlink >= 1, NOT same user as publisher, AND NOT same user as first link owner
        // AND NOT the same link as first link.
        $link2 = Link::where('statu', 1)
            ->where('id', '!=', $link1->id)
            ->whereHas('user', function ($query) use ($user_id, $link1) {
                $query->where('nlink', '>=', 1)
                      ->where('id', '!=', $user_id)
                      ->where('id', '!=', $link1->uid);
            })
            ->inRandomOrder()
            ->first();

        // If no second link found, what does old code do?
        // "if ($num_rows = $results2->fetchColumn() == 0) { } else { ... }"
        // It seems it proceeds but maybe logic is slightly different.
        // If we don't have a second link, we might break the template.
        // Let's assume we might reuse link1 or find another one allowing same user but diff link?
        // Or just fail gracefully.
        // For robustness, let's try to find another link even if from same user as link1 (but distinct link), 
        // or just duplicate link1 if really nothing else.
        if (!$link2) {
             // Fallback: Try finding any other link not equal to link1, even from same user
             $link2 = Link::where('statu', 1)
                ->where('id', '!=', $link1->id)
                ->whereHas('user', function ($query) use ($user_id) {
                    $query->where('nlink', '>=', 1)->where('id', '!=', $user_id);
                })
                ->inRandomOrder()
                ->first();
             
             // If still nothing, use link1 again
             if (!$link2) {
                 $link2 = $link1;
             }
        }

        // 4. Log Stats (state table)
        // Log for Link 1
        $this->logState($user_id, $link1->id, 'link', $request);
        
        // Log for Link 2 (if distinct or just log again)
        // Old code: Logs stats for both.
        if ($link2->id != $link1->id) {
            $this->logState($user_id, $link2->id, 'link', $request);
        }

        // 5. Render View
        // Determine which view based on px
        // Old logic: px can be "1" (468x60) or "2" (Responsive/Blog Card)
        // New logic passing from code.blade.php: "468x60" or "510x320" (which corresponds to responsive/blog card)
        // We need to map these.
        
        $viewName = 'theme::ads.serving.link1';
        
        if ($px == '510x320' || $px == 'responsive' || $px == '2') {
             $viewName = 'theme::ads.serving.link2';
        } elseif ($px == '468x60' || $px == '1') {
             $viewName = 'theme::ads.serving.link1';
        }

        // Publisher ID for references
        $publisherId = $user_id;

        $link1Name = $this->normalizeText($link1->name);
        $link1Txt = $this->normalizeText($link1->txt);
        $link2Name = $this->normalizeText($link2->name);
        $link2Txt = $this->normalizeText($link2->txt);

        $html = view($viewName, compact(
            'link1',
            'link2',
            'publisherId',
            'user',
            'link1Name',
            'link1Txt',
            'link2Name',
            'link2Txt'
        ))->render();
        
        // Strip newlines to avoid JS errors in document.write
        $html = str_replace(["\r", "\n"], ' ', $html);
        
        return response('document.write("' . addslashes($html) . '");', 200)->header('Content-Type', 'application/javascript; charset=UTF-8');
    }

    // Public: Redirect/Track (show.php)
    public function redirect(Request $request)
    {
        // Banner Click
        if ($request->has('ads') && $request->has('vu')) {
            $bannerId = $request->input('ads');
            $publisherId = $request->input('vu');
            
            // Check if it's a link click passed via 'type' param
            if ($request->input('type') == 'link') {
                return $this->handleLinkClick($bannerId, $publisherId, $request);
            }

            $banner = Banner::find($bannerId);
            if ($banner) {
                $banner->increment('clik');
                
                // Reward Publisher (Matches show.php logic?)
                // Old show.php usually rewards for clicks too.
                // Assuming +2 pts for click.
                User::where('id', $publisherId)->increment('pts', 2);

                // Log Click
                $this->logState($publisherId, $bannerId, 'vu', $request); 

                return redirect($banner->url);
            }
        }
        
        // Link Click (Legacy format support if any)
        if ($request->has('link') && $request->has('clik')) {
            return $this->handleLinkClick($request->input('link'), $request->input('clik'), $request);
        }

        return redirect('/');
    }
    
    private function handleLinkClick($linkId, $publisherId, $request)
    {
        $link = Link::find($linkId);
        if ($link) {
            $link->increment('clik');

            // Reward Publisher
            User::where('id', $publisherId)->increment('pts', 2);
            User::where('id', $publisherId)->increment('nlink', 0.5);

            // Log Click
            $this->logState($publisherId, $linkId, 'clik', $request);

            return redirect($link->url);
        }
        return redirect('/');
    }

    private function logState($sid, $pid, $type, $request)
    {
        // Using DB facade as State model might not exist or table 'state' is used directly
        DB::table('state')->insert([
            'sid' => $sid,
            'pid' => $pid,
            't_name' => $type,
            'r_link' => $request->server('HTTP_REFERER') ?? 'N',
            'r_date' => time(),
            'visitor_Agent' => $request->server('HTTP_USER_AGENT'),
            'v_ip' => $request->ip(),
        ]);
    }

    // Helper for dimensions
    private function getWidth($px)
    {
        if (!is_string($px) || !str_contains($px, 'x')) {
            return 468;
        }
        $parts = explode('x', $px);
        return isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : 468;
    }

    private function getHeight($px)
    {
        if (!is_string($px) || !str_contains($px, 'x')) {
            return 60;
        }
        $parts = explode('x', $px);
        return isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 60;
    }

    private function normalizeText($text)
    {
        if (!is_string($text) || $text === '') {
            return $text;
        }
        $encoding = mb_detect_encoding($text, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($text, 'UTF-8', $encoding);
        }
        return $text;
    }
}
