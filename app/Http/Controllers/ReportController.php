<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Link;
use App\Models\Banner;
use App\Models\SmartAd;
use App\Models\Report;
use App\Services\ModerationService;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $item = null;
        $type = null;
        $typeId = null;

        if ($request->has('link')) {
            $id = $request->input('link');
            $item = Link::find($id);
            $type = 'link'; // s_type 201
            $typeId = 201;
        } elseif ($request->has('banner')) {
            $id = $request->input('banner');
            $item = Banner::find($id);
            $type = 'banner'; // s_type 202
            $typeId = 202; 
        } elseif ($request->has('smart_ad')) {
            $id = $request->input('smart_ad');
            $item = SmartAd::find($id);
            $type = 'smart';
            $typeId = 204;
        } elseif ($request->has('order')) {
            $id = $request->input('order');
            $item = \App\Models\OrderRequest::find($id);
            $type = 'order';
            $typeId = 6;
        } elseif ($request->has('user')) {
            $id = $request->input('user');
            $item = \App\Models\User::where('username', $id)->first()
                ?? \App\Models\User::resolvePublicIdentifier($id)
                ?? \App\Models\User::find($id);
            $type = 'user';
            $typeId = 99;
        } elseif ($request->has('visits')) {
            $id = $request->input('visits');
            $item = \App\Models\Visit::find($id);
            $type = 'visits';
            $typeId = 203;
        }

        if (!$item) {
            return redirect()->route('dashboard')->with('error', 'Item not found');
        }

        $categories = Report::CATEGORIES;

        return view('theme::report.index', compact('item', 'type', 'typeId', 'categories'));
    }

    public function store(Request $request, ModerationService $moderation)
    {
        // SECURITY: Require authentication to prevent spam reports
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'txt' => 'required|string|max:1000',
            's_type' => 'required|integer',
            'tp_id' => 'required|integer',
            'category' => 'nullable|string|in:spam,harassment,inappropriate,copyright,misinformation,scam,other',
        ]);

        $userId = Auth::id();
        $sType = (int) $request->s_type;
        $tpId = (int) $request->tp_id;

        // Prevent duplicate spam reports for the same item from the same user
        $existing = Report::where('uid', $userId)
            ->where('s_type', $sType)
            ->where('tp_id', $tpId)
            ->where('statu', 1)
            ->first();

        if ($existing) {
            $msg = __('messages.report_already_submitted');
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('info', $msg);
        }

        $report = Report::create([
            'uid' => $userId,
            'txt' => $request->txt,
            'category' => $request->category ?: 'other',
            's_type' => $sType,
            'tp_id' => $tpId,
            'statu' => 1,
            'action_taken' => 'none',
        ]);

        // Evaluate auto-quarantine threshold
        $moderation->checkAutoQuarantine($sType, $tpId);

        $successMsg = __('messages.report_received_under_review');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }

        return back()->with('success', $successMsg);
    }
}
