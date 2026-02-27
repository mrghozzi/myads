<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Support\Facades\Cookie;
use App\Models\Referral;
use App\Models\Option;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('theme::auth.login');
    }

    public function showRegistrationForm()
    {
        return view('theme::auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree_terms' => ['required', 'accepted'],
            'capt' => ['required', function ($attribute, $value, $fail) {
                if ($value != session('captcha_result')) {
                    $fail(__('captcha_error'));
                }
            }],
        ], [
            'agree_terms.required' => __('messages.agree_terms_required'),
            'agree_terms.accepted' => __('messages.agree_terms_required'),
        ]);

        // Clear captcha after use
        session()->forget('captcha_result');

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'pass' => Hash::make($request->password),
            'pts' => 10, // Bonus points
            'vu' => 10,
            'nvu' => 10,
            'nlink' => 10,
            'ucheck' => 0,
            'online' => time(),
            // 'active' => 1, // Column not found in DB
            // Add other default values as needed
        ]);

        // Create Option for Username/Slug (Legacy Requirement)
        $name = $user->username;
        if (is_numeric($user->username)) {
            $name = hash('crc32', $user->username) . "_" . $user->username;
        }
        
        // URL Encode / Slugify
        $slug = urlencode(mb_ereg_replace('\s+', '-', $name));
        $slug = str_replace(array(' '), array('-'), $slug);

        Option::create([
            'name' => $user->username, // Original name
            'o_valuer' => $slug, // Slug
            'o_type' => 'user',
            'o_parent' => 0, // Legacy uses $dptdk (0)
            'o_order' => $user->id, // UID
            'o_mode' => '0',
        ]);

        // Referral Logic
        if ($request->hasCookie('ref')) {
            $referrerId = $request->cookie('ref');
            $referrer = User::find($referrerId);
            
            if ($referrer) {
                // 1. Insert into referral table
                Referral::create([
                    'uid' => $referrerId,
                    'ruid' => $user->id,
                    'date' => date('Y-m-d'),
                ]);
                
                // 2. Insert into options (History/Notification)
                Option::create([
                    'name' => 'referal',
                    'o_valuer' => '10',
                    'o_type' => 'hest_pts',
                    'o_parent' => $referrerId,
                    'o_order' => $user->id,
                    'o_mode' => time(),
                ]);
                
                // 3. Update referrer stats
                $referrer->increment('pts', 10);
                $referrer->increment('vu', 10);
                $referrer->increment('nvu', 10);
                $referrer->increment('nlink', 10);
                
                // 4. Clear cookie
                Cookie::queue(Cookie::forget('ref'));
            }
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $input = $request->input('username');

        // Query both username and email columns (like the old system)
        $user = User::where('username', $input)->orWhere('email', $input)->first();

        if ($user) {
            // Try standard Laravel check first (Bcrypt)
            if (Hash::check($request->input('password'), $user->pass)) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended('/');
            }
            
            // Fallback: Check MD5 (Legacy)
            if ($user->pass === md5($request->input('password'))) {
                Auth::login($user, $request->boolean('remember'));
                
                // Rehash password to Bcrypt
                $user->pass = Hash::make($request->input('password'));
                $user->save();
                
                $request->session()->regenerate();
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'username' => __('messages.login_error') ?? 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
