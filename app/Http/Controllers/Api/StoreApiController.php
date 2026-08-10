<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class StoreApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::visible()->with(['user', 'sale', 'type']);

        if ($request->has('category') && $request->category !== 'all' && !empty($request->category)) {
            $category = \App\Support\StoreCategoryCatalog::normalize($request->category);
            $categoryNames = \App\Support\StoreCategoryCatalog::namesForFilter($category);

            $typeQuery = \App\Models\Option::where('o_type', 'store_type');
            if ($categoryNames !== []) {
                $typeQuery->whereIn('name', $categoryNames);
            } else {
                $typeQuery->where('name', $request->category);
            }

            $categoryIds = $typeQuery->pluck('o_parent')->toArray();
            $query->whereIn('id', $categoryIds);
        }

        // Order products chronologically by status date (last status promotion s_type = 7867),
        // then updated_at (last modified date), then id DESC.
        $products = $query
            ->orderByDesc(
                \App\Models\Status::select('date')
                    ->whereColumn('tp_id', 'options.id')
                    ->where('s_type', 7867)
                    ->orderByDesc('date')
                    ->limit(1)
            )
            ->orderByDesc('updated_at')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return ProductResource::collection($products);
    }

    public function show($id, Request $request)
    {
        $product = Product::visible()->with(['user', 'sale', 'type'])->findOrFail($id);

        return new ProductResource($product);
    }

    public function knowledgebase($id)
    {
        $product = Product::findOrFail($id);

        $articles = \App\Models\Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 0)
            ->with('kbCategory')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }
}
