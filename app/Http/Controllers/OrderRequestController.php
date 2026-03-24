<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRequestController extends Controller
{
    public function index()
    {
        $orders = OrderRequest::where('statu', 1)
            ->with('user')
            ->orderBy('date', 'desc')
            ->paginate(15);

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

    public function destroy($id)
    {
        $order = OrderRequest::findOrFail($id);
        
        if ($order->uid != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            Status::where('tp_id', $id)->where('s_type', 6)->delete();
            $order->delete();
            DB::commit();
            return redirect()->route('orders.index')->with('success', __('messages.order_deleted_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('errMSG', $e->getMessage());
        }
    }
}
