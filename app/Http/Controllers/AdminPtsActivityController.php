<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PtsVoucher;
use App\Models\PointTransaction;

class AdminPtsActivityController extends Controller
{
    /**
     * Display a listing of the PTS activities (transfers and vouchers).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get PTS Transfers (we only query 'transfer_sent' to avoid duplicate rows for the same transaction)
        $transfers = PointTransaction::with('user')
            ->where('type', 'transfer_sent')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'transfers_page');

        // Get PTS Vouchers
        $vouchers = PtsVoucher::with(['generator', 'claimer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'vouchers_page');

        return view('admin::admin.pts_activities', compact('transfers', 'vouchers'));
    }
}
