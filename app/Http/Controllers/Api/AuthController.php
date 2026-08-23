<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $input = $request->input('login');
        
        $user = User::where('username', $input)->orWhere('email', $input)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.login_error') ?? 'Invalid credentials'
            ], 401);
        }

        // Try standard Laravel check first (Bcrypt)
        if (Hash::check($request->input('password'), $user->pass)) {
            $token = $user->createToken('MobileApp')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $user->usesPublicMemberIds()
                        ? $user->publicRouteIdentifier()
                        : $user->id,
                    'username' => $user->username,
                    'name' => $user->name ?? $user->username,
                    'avatar_url' => asset($user->img ?? 'upload/avatar.png'),
                ]
            ]);
        }
        
        // Fallback: Check MD5 (Legacy)
        // SECURITY: Use hash_equals() instead of === to prevent timing attacks
        if (hash_equals($user->pass, md5($request->input('password')))) {
            // Rehash password to Bcrypt
            $user->pass = Hash::make($request->input('password'));
            $user->save();
            
            \Log::info('MD5 legacy password used via API — auto-rehashed to bcrypt', ['user_id' => $user->id]);

            $token = $user->createToken('MobileApp')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $user->usesPublicMemberIds()
                        ? $user->publicRouteIdentifier()
                        : $user->id,
                    'username' => $user->username,
                    'name' => $user->name ?? $user->username,
                    'avatar_url' => asset($user->img ?? 'upload/avatar.png'),
                ]
            ]);
        }

        // SECURITY: Log failed API login attempt for monitoring
        \Log::warning('Failed API login attempt', [
            'input' => $input,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => __('messages.login_error') ?? 'Invalid credentials'
        ], 401);
    }

    public function register(Request $request)
    {
        // ... omitted for brevity ...
        return response()->json(['status' => 'error', 'message' => 'Registration via app is disabled for now'], 403);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}
