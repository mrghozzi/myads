<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PtsVoucher;
use App\Models\PointTransaction;
use App\Services\V420SchemaService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AdminPtsActivityController extends Controller
{
    /**
     * Display a listing of the PTS activities (transfers and vouchers).
     *
     * @param V420SchemaService $schema
     * @return \Illuminate\View\View
     */
    public function index(V420SchemaService $schema)
    {
        // Get PTS Transfers
        try {
            if ($schema->hasTable('point_transactions')) {
                $transfers = PointTransaction::with('user')
                    ->where('type', 'transfer_sent')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15, ['*'], 'transfers_page');
            } else {
                $transfers = new LengthAwarePaginator([], 0, 15, 1, ['pageName' => 'transfers_page']);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to load PTS transfers: ' . $e->getMessage());
            $transfers = new LengthAwarePaginator([], 0, 15, 1, ['pageName' => 'transfers_page']);
        }

        // Get PTS Vouchers
        try {
            if ($schema->hasTable('pts_vouchers')) {
                $vouchers = PtsVoucher::with(['generator', 'claimer'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15, ['*'], 'vouchers_page');
            } else {
                $vouchers = new LengthAwarePaginator([], 0, 15, 1, ['pageName' => 'vouchers_page']);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to load PTS vouchers: ' . $e->getMessage());
            $vouchers = new LengthAwarePaginator([], 0, 15, 1, ['pageName' => 'vouchers_page']);
        }

        return view('admin::admin.pts_activities', compact('transfers', 'vouchers'));
    }
}
