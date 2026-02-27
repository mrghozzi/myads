<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get valid languages directly from the folders to avoid any cache issues
        $validLanguages = ['en', 'ar']; // Fallbacks
        $langDir = base_path('lang');
        if (\Illuminate\Support\Facades\File::exists($langDir)) {
            $dirs = \Illuminate\Support\Facades\File::directories($langDir);
            $validFromFolders = array_map('basename', $dirs);
            if (!empty($validFromFolders)) {
                $validLanguages = array_unique(array_merge($validLanguages, $validFromFolders));
            }
        }
        // 1. Check for query parameter (highest priority)
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, $validLanguages)) {
                App::setLocale($lang);
                Session::put('locale', $lang);
                Cookie::queue('lang', $lang, 60 * 24 * 365);
            }
        }
        // 2. Check for Session
        elseif (Session::has('locale')) {
            $lang = Session::get('locale');
            if (in_array($lang, $validLanguages)) {
                App::setLocale($lang);
            }
        }
        // 3. Check for Cookie
        elseif ($request->hasCookie('lang')) {
            $lang = $request->cookie('lang');
            if (in_array($lang, $validLanguages)) {
                App::setLocale($lang);
                Session::put('locale', $lang);
            }
        }

        // 4. Dark Mode Logic
        if ($request->has('mode')) {
            $mode = $request->get('mode');
            if (in_array($mode, ['css', 'css_d'])) { // css = light, css_d = dark
                 Cookie::queue('modedark', $mode, 60 * 24 * 365);
            }
        }

        return $next($request);
    }
}
