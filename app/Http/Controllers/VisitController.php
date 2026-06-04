<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Visit;
use App\Models\User;
use App\Services\SecurityPolicyService;

class VisitController extends Controller
{
    // Management: List User's Sites
    public function index(Request $request)
    {
        // Handle Legacy Surf Request: visits?id=1
        if ($request->has('id')) {
            return $this->surf($request);
        }

        $user = Auth::user();
        $sites = Visit::where('uid', $user->id)->orderBy('id', 'desc')->get();
        $site_settings = \App\Models\Setting::first();
        $visits = Visit::where('uid', $user->id)->sum('vu'); // Total views on user's sites
        return view('theme::visits.index', compact('sites', 'user', 'site_settings', 'visits'));
    }

    // Management: Create Site
    public function create()
    {
        return view('theme::visits.create');
    }

    // Management: Store Site
    public function store(Request $request, SecurityPolicyService $securityPolicy)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'tims' => 'required|in:1,2,3,4', // 1=10s, 2=20s, 3=30s, 4=60s
        ]);

        $user = Auth::user();

        if ($violation = $securityPolicy->urlViolation((string) $request->input('url'), 'ads')) {
            return back()->withErrors(['url' => $violation])->withInput();
        }

        Visit::create([
            'uid' => $user->id,
            'name' => $request->name,
            'url' => $request->url,
            'tims' => $request->tims,
            'statu' => 1, // Default ON
            'vu' => 0,
        ]);

        return redirect()->route('visits.index')->with('success', 'Site added successfully.');
    }

    // Management: Edit Site
    public function edit($id)
    {
        $user = Auth::user();
        $site = Visit::where('id', $id)->where('uid', $user->id)->firstOrFail();
        return view('theme::visits.edit', compact('site'));
    }

    // Management: Update Site
    public function update(Request $request, $id, SecurityPolicyService $securityPolicy)
    {
        $user = Auth::user();
        $site = Visit::where('id', $id)->where('uid', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'tims' => 'required|in:1,2,3,4',
        ]);

        if ($violation = $securityPolicy->urlViolation((string) $request->input('url'), 'ads')) {
            return back()->withErrors(['url' => $violation])->withInput();
        }

        $site->update([
            'name' => $request->name,
            'url' => $request->url,
            'tims' => $request->tims,
        ]);

        return redirect()->route('visits.index')->with('success', 'Site updated successfully.');
    }

    // Management: Delete Site
    public function destroy($id)
    {
        $user = Auth::user();
        $site = Visit::where('id', $id)->where('uid', $user->id)->firstOrFail();
        $site->delete();

        return redirect()->route('visits.index')->with('success', 'Site deleted successfully.');
    }

    // Surfing: The Auto-Surf Page
    // SECURITY: Points are NO LONGER granted on page load. Instead, a secure token
    // is generated and passed to the view. Points are only granted after the user
    // calls verify() with the token, which checks that the required viewing
    // duration has actually elapsed.
    public function surf(Request $request)
    {
        $user = Auth::user();

        // Rate limit: prevent rapid-fire surf requests (1 per 8 seconds per user)
        $rateLimitKey = 'visit_surf_' . $user->id;
        if (Cache::has($rateLimitKey)) {
            return view('theme::visits.no_sites');
        }
        Cache::put($rateLimitKey, true, 8);

        // Select Next Site
        $site = Visit::where('statu', 1)
            ->where('uid', '!=', $user->id)
            ->whereHas('user', function ($query) {
                $query->where('vu', '>=', 1);
            })
            ->inRandomOrder()
            ->first();

        if (!$site) {
            return view('theme::visits.no_sites');
        }

        // Determine duration for this site
        $cost = 1;
        $duration = 10;
        switch ($site->tims) {
            case 1: $cost = 1; $duration = 10; break;
            case 2: $cost = 2; $duration = 20; break;
            case 3: $cost = 5; $duration = 30; break;
            case 4: $cost = 10; $duration = 60; break;
        }

        // Generate a secure token to validate the view later
        $timestamp = now()->timestamp;
        $token = Crypt::encryptString(json_encode([
            'site_id' => $site->id,
            'user_id' => $user->id,
            'cost' => $cost,
            'duration' => $duration,
            'timestamp' => $timestamp,
        ]));

        // Show View with token (points granted only after verify)
        return view('theme::visits.surf', compact('site', 'duration', 'token'));
    }

    /**
     * Verify a surf session and award points.
     * Called via AJAX after the required viewing duration has elapsed.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $payload = json_decode(Crypt::decryptString($request->token), true);

            if (!$payload || !isset($payload['site_id'], $payload['user_id'], $payload['timestamp'], $payload['cost'], $payload['duration'])) {
                return response()->json(['success' => false, 'message' => __('Invalid token.')], 400);
            }

            // Verify user matches
            if ($payload['user_id'] !== Auth::id()) {
                return response()->json(['success' => false, 'message' => __('Token user mismatch.')], 403);
            }

            // Verify time elapsed (allow 3 seconds grace)
            $timeElapsed = now()->timestamp - $payload['timestamp'];
            if ($timeElapsed < ($payload['duration'] - 3)) {
                return response()->json(['success' => false, 'message' => __('View duration not met.')], 400);
            }

            // Prevent token replay: each token can only be used once
            $tokenHash = hash('sha256', $request->token);
            $replayKey = 'visit_token_' . $tokenHash;
            if (Cache::has($replayKey)) {
                return response()->json(['success' => false, 'message' => __('Token already used.')], 400);
            }
            // Mark token as used for 2x the duration window
            Cache::put($replayKey, true, max(120, $payload['duration'] * 2));

            $site = Visit::find($payload['site_id']);
            if (!$site || $site->statu != 1) {
                return response()->json(['success' => false, 'message' => __('Site unavailable.')], 404);
            }

            $user = Auth::user();

            DB::beginTransaction();

            // Credit viewer
            $user->increment('pts', 5);
            $user->increment('vu', 0.5);

            // Credit site stats & debit site owner
            $site->increment('vu');
            DB::table('users')->where('id', $site->uid)->decrement('vu', $payload['cost']);

            DB::commit();

            app(\App\Services\GamificationService::class)->recordEvent($user->id, 'visit_exchange_completed');

            return response()->json(['success' => true, 'message' => __('View verified. Points awarded!')]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['success' => false, 'message' => __('Invalid or tampered token.')], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Visit Verify Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('An error occurred.')], 500);
        }
    }
}
