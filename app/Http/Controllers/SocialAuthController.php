<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Option;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        // Check if provider is supported
        if (!in_array($provider, ['facebook', 'google'])) {
            return redirect()->route('login')->with('error', 'Provider not supported');
        }

        // Check if provider is configured (API keys present in .env)
        $clientId = config("services.{$provider}.client_id");
        if (empty($clientId)) {
            return redirect()->route('login')->with('error', 'Social Login is not configured. Please set up ' . ucfirst($provider) . ' API keys in the .env file.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Could not redirect to provider: ' . $e->getMessage());
        }
    }

    public function callback($provider)
    {
        // Check if provider is configured
        $clientId = config("services.{$provider}.client_id");
        if (empty($clientId)) {
            return redirect()->route('login')->with('error', 'Social Login is not configured.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login failed: ' . $e->getMessage());
        }

        $email = $socialUser->getEmail();
        if (!$email) {
             return redirect()->route('login')->with('error', 'Could not retrieve email from provider.');
        }

        // Check if user exists
        $user = User::where('email', $email)->first();

        if ($user) {
            // User exists, log them in
            Auth::login($user);
            return redirect()->route('dashboard');
        } else {
            // Register new user
            $username = $socialUser->getName() ?? explode('@', $email)[0];
            $username = str_replace(' ', '', $username);
            
            // Ensure unique username
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter++;
            }

            $user = User::create([
                'username' => $username,
                'email' => $email,
                'pass' => Hash::make(Str::random(16)), // Random password
                'pts' => 10,
                'vu' => 10,
                'nvu' => 10,
                'nlink' => 10,
                'ucheck' => 0,
                'online' => time(),
                'img' => $socialUser->getAvatar(),
            ]);

            // Create User Slug Option
            $name = $user->username;
            if (is_numeric($user->username)) {
                $name = hash('crc32', $user->username) . "_" . $user->username;
            }
            $slug = urlencode(mb_ereg_replace('\s+', '-', $name));
            $slug = str_replace(array(' '), array('-'), $slug);

            Option::create([
                'name' => $user->username,
                'o_valuer' => $slug,
                'o_type' => 'user',
                'o_parent' => 0,
                'o_order' => $user->id,
                'o_mode' => '0',
            ]);

            // Store Social Link in Options
            Option::create([
                'name' => $provider,
                'o_valuer' => $socialUser->getId(),
                'o_type' => 'social_link',
                'o_parent' => $user->id,
                'o_order' => 1,
                'o_mode' => 'linked',
            ]);

            Auth::login($user);
            return redirect()->route('dashboard');
        }
    }
}
