<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Link;
use App\Models\SmartAd;
use App\Models\State; // Assuming State model exists or using DB table
use App\Models\User;
use App\Support\BannerSizeCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdsController extends Controller
{
    // Ads Dashboard
    public function index()
    {
        $user = Auth::user();
        $banners = Banner::where('uid', $user->id)->orderBy('id', 'desc')->limit(5)->get();
        $links = Link::where('uid', $user->id)->orderBy('id', 'desc')->limit(5)->get();
        $smartAds = SmartAd::where('uid', $user->id)->orderBy('id', 'desc')->limit(5)->get();
        
        return view('theme::ads.index', compact('banners', 'links', 'smartAds'));
    }

    // List Banners (b_list.php)
    public function indexBanners()
    {
        $user = Auth::user();
        $banners = Banner::where('uid', $user->id)->orderBy('id', 'desc')->get();
        $site_settings = \App\Models\Setting::first();
        return view('theme::ads.banners.index', compact('banners', 'user', 'site_settings'));
    }

    // List Links (l_list.php)
    public function indexLinks()
    {
        $user = Auth::user();
        $links = Link::where('uid', $user->id)->orderBy('id', 'desc')->get();
        $site_settings = \App\Models\Setting::first();
        return view('theme::ads.links.index', compact('links', 'user', 'site_settings'));
    }

    // Create Banner Form
    public function createBanner()
    {
        return view('theme::ads.banners.create');
    }

    // Store Banner
    public function storeBanner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'img' => 'required|string', // URL string
            'px' => 'required|string', // Size
        ]);

        $user = Auth::user();
        $bannerSize = $this->validatedBannerSize($request->input('px'));

        Banner::create([
            'uid' => $user->id,
            'name' => $request->name,
            'url' => $request->url,
            'img' => $request->img,
            'px' => $bannerSize,
            'statu' => 1,
            'vu' => 0,
            'clik' => 0,
        ]);

        return redirect()->route('ads.banners.index')->with('success', 'Banner added successfully.');
    }

    // Edit Banner Form
    public function editBanner($id)
    {
        $user = Auth::user();
        $banner = Banner::where('id', $id)->where('uid', $user->id)->firstOrFail();
        return view('theme::ads.banners.edit', compact('banner'));
    }

    // Update Banner
    public function updateBanner(Request $request, $id)
    {
        $user = Auth::user();
        $banner = Banner::where('id', $id)->where('uid', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'img' => 'required|string',
            'px' => 'required|string',
        ]);
        $bannerSize = $this->validatedBannerSize($request->input('px'));

        $banner->update([
            'name' => $request->name,
            'url' => $request->url,
            'img' => $request->img,
            'px' => $bannerSize,
        ]);

        return redirect()->route('ads.banners.index')->with('success', 'Banner updated successfully.');
    }

    // Delete Banner
    public function destroyBanner($id)
    {
        $user = Auth::user();
        $banner = Banner::where('id', $id)->where('uid', $user->id)->firstOrFail();
        $banner->delete();
        return redirect()->route('ads.banners.index')->with('success', 'Banner deleted successfully.');
    }

    // Get Banner Code (b_code.php)
    public function codeBanner()
    {
        $user = Auth::user();
        $extensions_code = \App\Models\Option::where('o_type', 'extensions_code')->value('o_valuer');
        return view('theme::ads.banners.code', compact('user', 'extensions_code'));
    }

    // Create Link Form
    public function createLink()
    {
        return view('theme::ads.links.create');
    }

    // Store Link
    public function storeLink(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'txt' => 'required|string',
        ]);

        $user = Auth::user();

        Link::create([
            'uid' => $user->id,
            'name' => $request->name,
            'url' => $request->url,
            'txt' => $request->txt,
            'statu' => 1,
            'clik' => 0,
        ]);

        return redirect()->route('ads.links.index')->with('success', 'Link added successfully.');
    }

    // Edit Link Form
    public function editLink($id)
    {
        $user = Auth::user();
        $link = Link::where('id', $id)->where('uid', $user->id)->firstOrFail();
        return view('theme::ads.links.edit', compact('link'));
    }

    // Update Link
    public function updateLink(Request $request, $id)
    {
        $user = Auth::user();
        $link = Link::where('id', $id)->where('uid', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'txt' => 'required|string',
        ]);

        $link->update([
            'name' => $request->name,
            'url' => $request->url,
            'txt' => $request->txt,
        ]);

        return redirect()->route('ads.links.index')->with('success', 'Link updated successfully.');
    }

    // Delete Link
    public function destroyLink($id)
    {
        $user = Auth::user();
        $link = Link::where('id', $id)->where('uid', $user->id)->firstOrFail();
        $link->delete();
        return redirect()->route('ads.links.index')->with('success', 'Link deleted successfully.');
    }

    // Get Link Code (l_code.php)
    public function codeLink()
    {
        $user = Auth::user();
        $extensions_code = \App\Models\Option::where('o_type', 'extensions_code')->value('o_valuer');
        return view('theme::ads.links.code', compact('user', 'extensions_code'));
    }

    // Promote Page (promote.php)
    public function promote()
    {
        return view('theme::ads.promote');
    }

    // Referrals Page (referral.php) - List of referred users
    public function referralList()
    {
        $user = Auth::user();
        $referrals = \App\Models\Referral::where('uid', $user->id)
            ->with('referredUser')
            ->orderBy('id', 'desc')
            ->paginate(20);
            
        return view('theme::ads.referrals_list', compact('user', 'referrals'));
    }

    // Referral Codes/Banners (r_code.php)
    public function referrals()
    {
        $user = Auth::user();
        $extensions_code = \App\Models\Option::where('o_type', 'extensions_code')->value('o_valuer');
        return view('theme::ads.referrals', compact('user', 'extensions_code'));
    }

    public function state(Request $request)
    {
        $user = Auth::user();
        $tyParam = $request->query('ty');

        if (!$tyParam) {
            abort(404);
        }

        if ($tyParam === 'clik') {
            $ty = 'link';
        } elseif ($tyParam === 'vu') {
            $ty = 'banner';
        } elseif ($tyParam === 'smart_click') {
            $ty = 'smart';
        } elseif (in_array($tyParam, ['banner', 'link', 'smart'])) {
            $ty = $tyParam;
        } else {
            abort(404);
        }

        $ty2 = $tyParam;
        $itemId = $request->query('id');
        $statesQuery = State::query();
        $subtitle = null;

        if ($itemId !== null && $itemId !== '') {
            if (!is_numeric($itemId)) {
                abort(404);
            }
            $itemId = (int) $itemId;
            $item = match ($ty) {
                'banner' => Banner::find($itemId),
                'link' => Link::find($itemId),
                'smart' => SmartAd::find($itemId),
            };
            if (!$item || $item->uid != $user->id) {
                abort(404);
            }
            $statesQuery->where('pid', $itemId)->where('t_name', $ty2);
            $subtitle = 'N°' . $itemId;
        } elseif ($request->query('st') === 'vu') {
            if ($ty === 'smart') {
                $ownedSmartAdIds = SmartAd::where('uid', $user->id)->pluck('id');
                $statesQuery->whereIn('pid', $ownedSmartAdIds)->where('t_name', $ty2);
            } else {
                $statesQuery->where('sid', $user->id)->where('t_name', $ty2);
            }
        } else {
            abort(404);
        }

        $title = match ($ty2) {
            'banner' => __('messages.bannads'),
            'link' => __('messages.textads'),
            'smart' => __('messages.smart_ads'),
            'vu' => __('messages.bannads') . '<br />' . __('messages.hits'),
            'clik' => __('messages.textads') . '<br />' . __('messages.hits'),
            'smart_click' => __('messages.smart_ads') . '<br />' . __('messages.hits'),
            default => __('messages.statistics'),
        };

        $backUrl = $request->headers->get('referer');
        if (!$backUrl) {
            if ($request->query('st') === 'vu') {
                $backUrl = route('dashboard');
            } elseif ($ty === 'smart') {
                $backUrl = route('ads.smart.index');
            } elseif ($ty === 'link') {
                $backUrl = route('ads.links.index');
            } else {
                $backUrl = route('ads.banners.index');
            }
        }

        $states = $statesQuery->orderBy('id', 'desc')->get();

        if (!$subtitle && is_numeric($request->query('st'))) {
            $subtitle = '@' . (User::find($request->query('st'))->username ?? '');
        }

        return view('theme::state', compact('states', 'title', 'subtitle', 'backUrl'));
    }

    // Public: Serve Banner Script (bn.php)
    public function bannerScript(Request $request)
    {
        $user_id = $request->query('ID');
        $px = $request->query('px');

        if (!$user_id || !is_numeric($user_id)) {
            return response('// Invalid User ID', 200)->header('Content-Type', 'application/javascript');
        }

        $user = User::find($user_id);
        if (!$user) {
            return response('// User not found', 200)->header('Content-Type', 'application/javascript');
        }

        // Logic to pick a banner
        // 1. Update Publisher Points
        $user->increment('pts', 1);
        $user->increment('nvu', 0.5);

        // 2. Select a Banner
        // Logic: Banner from user who has nvu >= 1 and NOT same user
        $banner = Banner::where('statu', 1)
            ->where('px', $px)
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

            // Return JS to display banner
            $html = "<a href='" . route('ads.redirect', ['ads' => $banner->id, 'vu' => $user_id]) . "' target='_blank'><img src='" . $banner->img . "' width='" . $this->getWidth($px) . "' height='" . $this->getHeight($px) . "' border='0'></a>";
            return response('document.write("' . addslashes($html) . '");', 200)->header('Content-Type', 'application/javascript');
        } else {
            // Default Banner
            $defaultImg = theme_asset('img/banner/banner_ads.png');
            $html = "<a href='" . url('/') . "?ref=" . $user_id . "' target='_blank'><img src='" . $defaultImg . "' width='" . $this->getWidth($px) . "' height='" . $this->getHeight($px) . "' border='0'></a>";
            return response('document.write("' . addslashes($html) . '");', 200)->header('Content-Type', 'application/javascript');
        }
    }

    // Public: Serve Link Script (link.php)
    public function linkScript(Request $request)
    {
        $user_id = $request->query('ID');

        if (!$user_id || !is_numeric($user_id)) {
            return response('// Invalid User ID', 200)->header('Content-Type', 'application/javascript');
        }

        $user = User::find($user_id);
        if (!$user) {
            return response('// User not found', 200)->header('Content-Type', 'application/javascript');
        }

        // 1. Update Publisher Points (0.5 pts for links usually less)
        $user->increment('pts', 0.5);
        $user->increment('nlink', 0.5);

        // 2. Select a Link
        // Logic: Link from user who has nlink >= 1 and NOT same user
        $link = Link::where('statu', 1)
            ->whereHas('user', function ($query) use ($user_id) {
                $query->where('nlink', '>=', 1)->where('id', '!=', $user_id);
            })
            ->inRandomOrder()
            ->first();

        if ($link) {
            // Deduct from Advertiser
            $advertiser = User::find($link->uid);
            if ($advertiser) {
                $advertiser->decrement('nlink', 1);
            }
            $link->increment('clik'); // Increment view/click count (links often count views as well or just display)

            // Log State
            $this->logState($user_id, $link->id, 'link', $request);

            // Return JS to display link
            // Assuming link.php displays: <a href="...">Title</a><br>Description
            $html = "<div class='myads-link'><a href='" . route('ads.redirect', ['ads' => $link->id, 'vu' => $user_id, 'type' => 'link']) . "' target='_blank' style='font-weight:bold;'>" . addslashes($link->name) . "</a><br><span style='font-size:small;'>" . addslashes(Str::limit($link->txt, 100)) . "</span></div>";
            return response('document.write("' . addslashes($html) . '");', 200)->header('Content-Type', 'application/javascript');
        } else {
            // Default Link
            $html = "<div class='myads-link'><a href='" . url('/') . "?ref=" . $user_id . "' target='_blank' style='font-weight:bold;'>" . __('advertise_here') . "</a></div>";
            return response('document.write("' . addslashes($html) . '");', 200)->header('Content-Type', 'application/javascript');
        }
    }

    // Helper for dimensions
    private function getWidth($px)
    {
        $parts = explode('x', $px);
        return $parts[0] ?? 468;
    }

    private function getHeight($px)
    {
        $parts = explode('x', $px);
        return $parts[1] ?? 60;
    }

    // Public: Redirect/Track (show.php)
    public function redirect(Request $request)
    {
        // Banner Click
        if ($request->has('ads') && $request->has('vu')) {
            $bannerId = $request->input('ads');
            $publisherId = $request->input('vu');

            $banner = Banner::find($bannerId);
            if ($banner) {
                $banner->increment('clik');
                
                // Reward Publisher
                User::where('id', $publisherId)->increment('pts', 2);

                // Log Click
                $this->logState($publisherId, $bannerId, 'vu', $request); // 'vu' in old code means click on banner? show.php lines 38-48 log 'vu'

                return redirect($banner->url);
            }
        }
        
        // Link Click
        if ($request->has('link') && $request->has('clik')) {
            $linkId = $request->input('link');
            $publisherId = $request->input('clik'); // 'clik' param is user id in old code

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
        }

        return redirect('/');
    }

    private function logState($sid, $pid, $type, $request)
    {
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

    private function validatedBannerSize(null|string|int $value): string
    {
        $bannerSize = BannerSizeCatalog::normalize($value);

        if ($bannerSize === null) {
            throw ValidationException::withMessages([
                'px' => 'Invalid banner size selected.',
            ]);
        }

        return $bannerSize;
    }
}
