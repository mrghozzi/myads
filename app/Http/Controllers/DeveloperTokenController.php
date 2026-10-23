<?php

namespace App\Http\Controllers;

use App\Models\DeveloperPersonalToken;
use App\Services\DeveloperEligibilityService;
use App\Services\DeveloperScopeCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperTokenController extends Controller
{
    public function index(DeveloperEligibilityService $eligibilityService)
    {
        if (!auth()->check()) return redirect()->route('login');

        $check = $eligibilityService->checkEligibility(auth()->user());
        if (!$check['eligible']) {
            return redirect()->route('developer.index')->with('error', __('messages.dev_not_eligible'));
        }

        $tokens = DeveloperPersonalToken::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        $scopes = DeveloperScopeCatalog::getAllScopes();

        return view('theme::developer.tokens.index', compact('tokens', 'scopes'));
    }

    public function store(Request $request, DeveloperEligibilityService $eligibilityService)
    {
        if (!auth()->check()) return redirect()->route('login');

        $check = $eligibilityService->checkEligibility(auth()->user());
        if (!$check['eligible']) {
            return redirect()->route('developer.index')->with('error', __('messages.dev_not_eligible'));
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'scopes' => 'required|array|min:1',
            'expires_days' => 'nullable|integer|in:30,60,90,365,0',
        ]);

        $plainToken = 'pat_' . Str::random(48);
        $hashedToken = hash('sha256', $plainToken);

        $expiresAt = null;
        $days = (int) ($request->expires_days ?? 90);
        if ($days > 0) {
            $expiresAt = now()->addDays($days);
        }

        $validCatalogScopes = array_keys(DeveloperScopeCatalog::getAllScopes());
        $requestedScopes = array_values(array_intersect(
            array_map([DeveloperScopeCatalog::class, 'normalizeScopeId'], $request->scopes),
            $validCatalogScopes
        ));

        $tokenRecord = DeveloperPersonalToken::create([
            'user_id' => auth()->id(),
            'name' => trim($request->name),
            'token' => $hashedToken,
            'scopes' => $requestedScopes,
            'expires_at' => $expiresAt,
        ]);

        // Flash plainToken once to session so user can copy it!
        return redirect()->route('developer.tokens.index')
            ->with('success', __('messages.pat_token_created'))
            ->with('new_token_plain', $plainToken)
            ->with('new_token_name', $tokenRecord->name);
    }

    public function destroy(DeveloperPersonalToken $token)
    {
        if (!auth()->check()) return redirect()->route('login');

        if ((int) $token->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $token->delete();

        return back()->with('success', __('messages.pat_token_deleted'));
    }
}
