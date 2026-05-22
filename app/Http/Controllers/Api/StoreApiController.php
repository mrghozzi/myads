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
        $query = Product::where('app', 1)->with(['seller']);

        if ($request->has('category')) {
            $query->where('cat', $request->category);
        }

        $products = $query->orderBy('ep', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return ProductResource::collection($products);
    }

    public function show($id, Request $request)
    {
        $product = Product::where('app', 1)->with(['seller'])->findOrFail($id);
        
        $product->increment('vu');

        return new ProductResource($product);
    }
}
