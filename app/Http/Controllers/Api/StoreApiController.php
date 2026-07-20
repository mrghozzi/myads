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
        $query = Product::visible()->with(['user']);

        if ($request->has('category')) {
            $categoryIds = \App\Models\Option::where('o_type', 'store_type')->where('name', $request->category)->pluck('o_parent');
            $query->whereIn('id', $categoryIds);
        }

        $products = $query->orderBy('id', 'desc')
            ->paginate(20);

        return ProductResource::collection($products);
    }

    public function show($id, Request $request)
    {
        $product = Product::visible()->with(['user'])->findOrFail($id);

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
