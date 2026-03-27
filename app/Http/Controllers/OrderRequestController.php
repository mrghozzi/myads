<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRequestController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'newest');
        $query = OrderRequest::with('user')->withCount('offers');

        if ($sort === 'active') {
            $query->orderBy('last_activity', 'desc');
        } elseif ($sort === 'rated') {
            $query->orderBy('avg_rating', 'desc');
        } elseif ($sort === 'popular') {
            $query->orderBy('offers_count', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $orders = $query->paginate(15);

        $this->seo([
            'scope_key' => 'orders_index',
            'resource_title' => __('messages.order_requests'),
            'description' => __('messages.order_requests_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.order_requests'), 'url' => route('orders.index')],
            ],
        ]);

        return view('theme::orders.index', compact('orders'));
    }

    public function create()
    {
        return view('theme::orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
        ]);

        $ledger = app(\App\Services\PointLedgerService::class);

        try {
            DB::beginTransaction();
            
            $time = time();
            $order = OrderRequest::create([
                'uid' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'budget' => $request->budget,
                'category' => $request->category,
                'date' => $time,
                'statu' => 1,
            ]);

            // Create a status post for the community feed
            Status::create([
                'uid' => Auth::id(),
                'tp_id' => $order->id,
                's_type' => 6, // New type for Order Requests
                'date' => $time,
                'txt' => $order->title, // Or some preview text
                'statu' => 1,
            ]);

            DB::commit();

            // Reward for posting
            $ledger->award(Auth::user(), 10, 'order_posted', 'points_awarded', 'order', $order->id);

            app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'order_request_created');

            return redirect()->route('orders.index')->with('success', __('messages.order_created_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('errMSG', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $order = OrderRequest::with('user')->findOrFail($id);
        
        $this->seo([
            'scope_key' => 'order_show',
            'resource_title' => $order->title,
            'description' => $order->description,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.order_requests'), 'url' => route('orders.index')],
                ['name' => $order->title, 'url' => route('orders.show', $order->id)],
            ],
        ]);

        return view('theme::orders.show', compact('order'));
    }

    public function rateOffer(Request $request, $id)
    {
        try {
            $order = OrderRequest::findOrFail($id);
            if ($order->uid != Auth::id()) {
                abort(403);
            }

            $offerId = $request->input('offer_id');
            $rating = (int) $request->input('rating');
            
            $offer = \App\Models\Option::where('id', $offerId)->where('o_parent', $id)->where('o_type', 'o_order')->firstOrFail();
            $offer->update(['o_mode' => $rating]);

            if ($rating === 5) {
                app(\App\Services\GamificationService::class)->recordEvent($offer->o_order, 'five_star_rating_received');
            }

            $avg = $order->offers()->where('o_mode', '>', 0)->avg('o_mode');
            $order->update(['avg_rating' => $avg ?? 0]);

            return back()->with('success', __('messages.rating_submitted'));
        } catch (\Throwable $e) {
            \Log::error('Order Rate Error: ' . $e->getMessage());
            return back()->with('errMSG', $e->getMessage());
        }
    }

    public function selectBestOffer(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        if ($order->uid != Auth::id()) {
            abort(403);
        }

        $offerId = $request->input('offer_id');
        $offer = \App\Models\Option::where('id', $offerId)->where('o_parent', $id)->where('o_type', 'o_order')->firstOrFail();

        $order->update(['best_offer_id' => $offer->id]);

        $winnerId = $offer->o_order;
        app(\App\Services\PointLedgerService::class)->award($winnerId, 50, 'best_offer_winner', 'points_awarded', 'order_offer', $offer->id);

        app(\App\Services\GamificationService::class)->recordEvent($winnerId, 'best_offer_selected');

        return back()->with('success', __('messages.best_offer_selected'));
    }

    public function close(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        $user = Auth::user();

        if ($order->uid != $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $order->update(['statu' => 0]);

        if ($user->isAdmin() && $order->uid != $user->id) {
            app(\App\Services\NotificationService::class)->send(
                $order->uid,
                __('messages.order_closed_by_admin', ['title' => $order->title]),
                route('orders.show', $order->id),
                'info',
                $user->id
            );
        }

        return back()->with('success', __('messages.order_closed_successfully'));
    }

    public function destroy($id)
    {
        $order = OrderRequest::findOrFail($id);
        
        if ($order->uid != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            Status::where('tp_id', $id)->where('s_type', 6)->delete();
            \App\Models\Option::where('o_parent', $id)->where('o_type', 'o_order')->delete();
            $order->delete();
            DB::commit();
            return redirect()->route('orders.index')->with('success', __('messages.order_deleted_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('errMSG', $e->getMessage());
        }
    }
}
