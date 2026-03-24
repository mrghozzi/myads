<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Link;
use App\Models\Banner;
use App\Models\SmartAd;
use App\Models\Report;

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
            $type = 'link'; // s_type 201 in old code
            $typeId = 201;
        } elseif ($request->has('banner')) {
            $id = $request->input('banner');
            $item = Banner::find($id);
            $type = 'banner'; // s_type 202? need to check old code
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
            $typeId = 701;
        } elseif ($request->has('user')) {
            $id = $request->input('user');
            $item = \App\Models\User::find($id);
            $type = 'user';
            $typeId = 702;
        }

        // If no valid item found, maybe show generic report or 404
        if (!$item) {
            return redirect()->route('dashboard')->with('error', 'Item not found');
        }

        return view('theme::report.index', compact('item', 'type', 'typeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'txt' => 'required|string|max:1000',
            's_type' => 'required|integer',
            'tp_id' => 'required|integer',
        ]);

        Report::create([
            'uid' => Auth::id() ?? 0,
            'txt' => $request->txt,
            's_type' => $request->s_type,
            'tp_id' => $request->tp_id,
            'statu' => 1,
            // 'date' => time(), // Column not found in model
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('pending')]);
        }

        return back()->with('success', __('pending'));
    }
}
