<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Visit;
use App\Models\User;
use App\Models\Option;
use App\Services\PointLedgerService;
use App\Services\SecurityPolicyService;

class VisitController extends Controller
{
    /**
     * Anti-cheat configuration constants.
     */
    private const DAILY_VISIT_LIMIT     = 50;   // Max rewarded visits per day per user
    private const SAME_SITE_COOLDOWN    = 3600;  // Seconds before same site can be viewed again (1 hour)
    private const RATE_LIMIT_SECONDS    = 8;     // Minimum seconds between surf requests
    private const TOKEN_EXPIRY_SECONDS  = 300;   // Tokens older than 5 minutes are rejected
    private const CHALLENGE_MULTIPLIER  = 7;     // JS challenge: answer = challenge * 7 + 3
    private const CHALLENGE_ADDEND      = 3;

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

        // Rate limit: prevent rapid-fire surf requests
        $rateLimitKey = 'visit_surf_' . $user->id;
        if (Cache::has($rateLimitKey)) {
            return view('theme::visits.no_sites');
        }
        Cache::put($rateLimitKey, true, self::RATE_LIMIT_SECONDS);

        // Anti-cheat: Check daily limit
        $dailyKey = 'visit_daily_' . $user->id . '_' . now()->format('Y-m-d');
        $dailyCount = (int) Cache::get($dailyKey, 0);
        if ($dailyCount >= self::DAILY_VISIT_LIMIT) {
            return view('theme::visits.no_sites', ['daily_limit_reached' => true]);
        }

        // Select Next Site (exclude own sites, require owner has vu credits)
        $visitedSitesKey = 'visit_recent_sites_' . $user->id;
        $recentSiteIds = Cache::get($visitedSitesKey, []);

        $query = Visit::where('statu', 1)
            ->where('uid', '!=', $user->id)
            ->whereHas('user', function ($q) {
                $q->where('vu', '>=', 1);
            });

        // Exclude recently-visited sites to prevent farming the same site
        if (!empty($recentSiteIds)) {
            $query->whereNotIn('id', $recentSiteIds);
        }

        $site = $query->inRandomOrder()->first();

        // If no unvisited sites available, try without the exclusion filter
        if (!$site) {
            $site = Visit::where('statu', 1)
                ->where('uid', '!=', $user->id)
                ->whereHas('user', function ($q) {
                    $q->where('vu', '>=', 1);
                })
                ->inRandomOrder()
                ->first();
        }

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

        // Generate JS anti-bot challenge (random number, client must solve: n * 7 + 3)
        $challenge = random_int(100, 9999);

        // Generate a secure token to validate the view later
        $timestamp = now()->timestamp;
        $token = Crypt::encryptString(json_encode([
            'site_id' => $site->id,
            'user_id' => $user->id,
            'cost' => $cost,
            'duration' => $duration,
            'timestamp' => $timestamp,
            'challenge' => $challenge,
            'ip' => $request->ip(),
        ]));

        // Show View with token (points granted only after verify)
        return view('theme::visits.surf', compact('site', 'duration', 'token', 'challenge'));
    }

    /**
     * Verify a surf session and award points.
     * Called via AJAX after the required viewing duration has elapsed.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'challenge_answer' => 'required|integer',
        ]);

        try {
            $payload = json_decode(Crypt::decryptString($request->token), true);

            if (!$payload || !isset(
                $payload['site_id'],
                $payload['user_id'],
                $payload['timestamp'],
                $payload['cost'],
                $payload['duration'],
                $payload['challenge']
            )) {
                return response()->json(['success' => false, 'message' => __('Invalid token.')], 400);
            }

            // 1. Verify user matches
            if ($payload['user_id'] !== Auth::id()) {
                return response()->json(['success' => false, 'message' => __('Token user mismatch.')], 403);
            }

            // 2. Verify JS challenge answer (anti-bot)
            $expectedAnswer = $payload['challenge'] * self::CHALLENGE_MULTIPLIER + self::CHALLENGE_ADDEND;
            if ((int) $request->challenge_answer !== $expectedAnswer) {
                \Log::warning('Visit anti-cheat: JS challenge failed', [
                    'user_id' => Auth::id(),
                    'expected' => $expectedAnswer,
                    'got' => $request->challenge_answer,
                ]);
                return response()->json(['success' => false, 'message' => __('Verification failed.')], 400);
            }

            // 3. Verify time elapsed (allow 3 seconds grace)
            $timeElapsed = now()->timestamp - $payload['timestamp'];
            if ($timeElapsed < ($payload['duration'] - 3)) {
                return response()->json(['success' => false, 'message' => __('View duration not met.')], 400);
            }

            // 4. Reject tokens older than TOKEN_EXPIRY_SECONDS (stale/replayed sessions)
            if ($timeElapsed > self::TOKEN_EXPIRY_SECONDS) {
                return response()->json(['success' => false, 'message' => __('Session expired. Please try again.')], 400);
            }

            // 5. Prevent token replay: each token can only be used once
            $tokenHash = hash('sha256', $request->token);
            $replayKey = 'visit_token_' . $tokenHash;
            if (Cache::has($replayKey)) {
                return response()->json(['success' => false, 'message' => __('Token already used.')], 400);
            }
            // Mark token as used for 2x the duration window
            Cache::put($replayKey, true, max(120, $payload['duration'] * 2));

            // 6. Check daily limit
            $user = Auth::user();
            $dailyKey = 'visit_daily_' . $user->id . '_' . now()->format('Y-m-d');
            $dailyCount = (int) Cache::get($dailyKey, 0);
            if ($dailyCount >= self::DAILY_VISIT_LIMIT) {
                return response()->json(['success' => false, 'message' => __('Daily visit limit reached.')], 429);
            }

            // 7. Check IP consistency (if available)
            if (isset($payload['ip']) && $payload['ip'] !== $request->ip()) {
                \Log::warning('Visit anti-cheat: IP mismatch', [
                    'user_id' => $user->id,
                    'token_ip' => $payload['ip'],
                    'verify_ip' => $request->ip(),
                ]);
                // We log but don't block — IPs can change legitimately (VPN, mobile)
            }

            $site = Visit::find($payload['site_id']);
            if (!$site || $site->statu != 1) {
                return response()->json(['success' => false, 'message' => __('Site unavailable.')], 404);
            }

            // 8. Verify site owner still has enough credits
            $siteOwner = User::find($site->uid);
            if (!$siteOwner || $siteOwner->vu < $payload['cost']) {
                return response()->json(['success' => false, 'message' => __('Site owner has insufficient credits.')], 400);
            }

            DB::beginTransaction();

            // Credit viewer: award PTS via PointLedgerService for proper ledger tracking
            $pointsReward = 5;
            $vuReward = 0.5;

            app(PointLedgerService::class)->award(
                $user,
                $pointsReward,
                'visit_exchange',
                'messages.pts_visit_exchange',
                'visit',
                $site->id,
                ['site_name' => $site->name, 'duration' => $payload['duration']],
                true
            );

            // Credit viewer visit credits
            $user->increment('vu', $vuReward);

            // Credit site stats & debit site owner
            $site->increment('vu');
            DB::table('users')->where('id', $site->uid)->decrement('vu', $payload['cost']);

            // Record visit exchange log
            Option::create([
                'name' => $site->name,
                'o_valuer' => (string) $site->id,
                'o_type' => 'v_visited',
                'o_parent' => $user->id,
                'o_order' => $user->id,
                'o_mode' => (string) time(),
            ]);

            DB::commit();

            // Update daily counter
            Cache::put($dailyKey, $dailyCount + 1, now()->endOfDay());

            // Track recently-visited sites (prevent same-site farming)
            $visitedSitesKey = 'visit_recent_sites_' . $user->id;
            $recentSiteIds = Cache::get($visitedSitesKey, []);
            $recentSiteIds[] = $site->id;
            // Keep only last 20 entries
            $recentSiteIds = array_slice(array_unique($recentSiteIds), -20);
            Cache::put($visitedSitesKey, $recentSiteIds, self::SAME_SITE_COOLDOWN);

            // Gamification event
            app(\App\Services\GamificationService::class)->recordEvent($user->id, 'visit_exchange_completed');

            $remaining = self::DAILY_VISIT_LIMIT - ($dailyCount + 1);
            return response()->json([
                'success' => true,
                'message' => __('View verified. Points awarded!'),
                'pts_awarded' => $pointsReward,
                'daily_remaining' => $remaining,
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['success' => false, 'message' => __('Invalid or tampered token.')], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Visit Verify Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => __('An error occurred.')], 500);
        }
    }
}
