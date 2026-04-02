<?php

namespace App\Http\Controllers;

use App\Services\ExtensionMarketplaceCatalogService;
use Illuminate\Http\JsonResponse;

class MarketplaceExtensionFeedController extends Controller
{
    public function plugins(ExtensionMarketplaceCatalogService $catalog): JsonResponse
    {
        return response()->json($catalog->build('plugins'));
    }

    public function themes(ExtensionMarketplaceCatalogService $catalog): JsonResponse
    {
        return response()->json($catalog->build('themes'));
    }
}
