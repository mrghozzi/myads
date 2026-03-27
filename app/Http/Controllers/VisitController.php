<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Visit;
use App\Models\User;

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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'tims' => 'required|in:1,2,3,4', // 1=10s, 2=20s, 3=30s, 4=60s
        ]);

        $user = Auth::user();

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
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $site = Visit::where('id', $id)->where('uid', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'tims' => 'required|in:1,2,3,4',
        ]);

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
    public function surf(Request $request)
    {
        $user = Auth::user();

        // 1. Credit Viewer Immediately (Legacy Logic: "Pay on Load")
        // UPDATE users SET pts=pts+5, vu=vu+.5 WHERE id=:id
        $user->increment('pts', 5);
        $user->increment('vu', 0.5);

        // 2. Select Next Site
        // Logic: Site from user who has vu >= 1 and NOT same user
        $site = Visit::where('statu', 1)
            ->where('uid', '!=', $user->id)
            ->whereHas('user', function ($query) {
                $query->where('vu', '>=', 1);
            })
            ->inRandomOrder()
            ->first();

        if (!$site) {
             // Legacy behavior: Reload after 10s even if no sites
             // We can use the same surf view but with a placeholder or just the no_sites view with auto-reload
            return view('theme::visits.no_sites');
        }

        // 3. Credit Site Stats & Debit Site Owner (Legacy Logic)
        $site->increment('vu');

        // Deduct points from owner based on duration
        // Legacy mapping: 1->1, 2->2, 3->5, 4->10
        $cost = 1;
        $duration = 10;
        switch ($site->tims) {
            case 1: $cost = 1; $duration = 10; break;
            case 2: $cost = 2; $duration = 20; break;
            case 3: $cost = 5; $duration = 30; break;
            case 4: $cost = 10; $duration = 60; break;
        }

        // UPDATE users SET vu=vu-:ivu WHERE id=:id
        DB::table('users')->where('id', $site->uid)->decrement('vu', $cost);

        app(\App\Services\GamificationService::class)->recordEvent($user->id, 'visit_exchange_completed');

        // 4. Show View
        return view('theme::visits.surf', compact('site', 'duration'));

    }
}
