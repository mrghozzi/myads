<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Option;
use App\Services\SecurityPolicyService;
use App\Services\SecuritySessionService;
use App\Services\SecurityThrottleService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        // Check if provider is supported
        if (!in_array($provider, ['facebook', 'google', 'adstn'])) {
            return redirect()->route('login')->with('error', 'Provider not supported');
        }

        // Check if provider is configured (API keys present in .env)
        $clientId = config("services.{$provider}.client_id");
        if (empty($clientId)) {
            return redirect()->route('login')->with('error', 'Social Login is not configured. Please set up ' . ucfirst($provider) . ' API keys in the .env file.');
        }

        try {
            if ($provider === 'adstn') {
                $state = Str::random(40);
                request()->session()->put('state', $state);
                $query = http_build_query([
                    'client_id' => $clientId,
                    'redirect_uri' => route('social.callback', 'adstn'),
                    'response_type' => 'code',
                    'scope' => 'user.identity.read user.profile.read',
                    'state' => $state,
                ]);
                return redirect('https://www.adstn.ovh/oauth/authorize?' . $query);
            }
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Could not redirect to provider: ' . $e->getMessage());
        }
    }

    public function callback(
        $provider,
        SecurityPolicyService $securityPolicy,
        SecurityThrottleService $securityThrottle,
        SecuritySessionService $securitySessions
    )
    {
        // Check if provider is configured
        $clientId = config("services.{$provider}.client_id");
        if (empty($clientId)) {
            return redirect()->route('login')->with('error', 'Social Login is not configured.');
        }

        try {
            if ($provider === 'adstn') {
                $state = request()->session()->pull('state');
                if (!$state || $state !== request('state')) {
                    return redirect()->route('login')->with('error', 'Invalid state');
                }

                $response = \Illuminate\Support\Facades\Http::post('https://www.adstn.ovh/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => $clientId,
                    'client_secret' => config("services.{$provider}.client_secret"),
                    'code' => request('code'),
                    'redirect_uri' => route('social.callback', 'adstn'),
                ]);

                if (!$response->successful()) {
                    return redirect()->route('login')->with('error', 'Token exchange failed: ' . $response->body());
                }

                $tokens = $response->json();
                $accessToken = $tokens['access_token'];

                // Get Identity
                $identityResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                    ->get('https://www.adstn.ovh/api/developer/v1/me');
                
                if (!$identityResponse->successful()) {
                    return redirect()->route('login')->with('error', 'Failed to fetch user identity');
                }

                $identity = $identityResponse->json('data');

                // Get Profile (for avatar)
                $profileResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                    ->get('https://www.adstn.ovh/api/developer/v1/me/profile');
                
                $profile = $profileResponse->successful() ? $profileResponse->json('data') : [];

                // Normalize to a social user object or similar data structure
                $socialUser = new class($identity, $profile) {
                    public $identity;
                    public $profile;
                    public function __construct($identity, $profile) {
                        $this->identity = $identity;
                        $this->profile = $profile;
                    }
                    public function getId() { return $this->identity['id']; }
                    public function getEmail() { return $this->identity['email']; }
                    public function getName() { return $this->identity['username']; }
                    public function getAvatar() { return $this->profile['avatar'] ?? 'upload/avatar.png'; }
                };
            } else {
                $socialUser = Socialite::driver($provider)->user();
            }
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
            request()->session()->regenerate();
            $securitySessions->trackLogin(request(), $user, 'social_' . $provider);
            return redirect()->route('dashboard');
        } else {
            if (!$securityThrottle->canRegisterFromIp(request()->ip())) {
                return redirect()->route('login')->withErrors([
                    'email' => $securityThrottle->registrationLimitMessage(request()->ip()),
                ]);
            }

            if ($emailViolation = $securityPolicy->emailViolation($email)) {
                return redirect()->route('login')->withErrors([
                    'email' => $emailViolation,
                ]);
            }

            // Register new user
            $username = $socialUser->getName() ?? explode('@', $email)[0];
            $username = str_replace(' ', '', $username);
            
            // Ensure unique username
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter++;
            }

            if ($usernameViolation = $securityPolicy->usernameViolation($username)) {
                return redirect()->route('login')->withErrors([
                    'email' => $usernameViolation,
                ]);
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
                'o_mode' => 'upload/cover.jpg',
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
            request()->session()->regenerate();
            $securityThrottle->hitRegistrationFromIp(request()->ip());
            $securitySessions->trackLogin(request(), $user, 'social_' . $provider);
            return redirect()->route('dashboard');
        }
    }
}
