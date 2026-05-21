<?php

namespace App\Http\Controllers;

use App\Services\ExtensionMarketplaceCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Short;
use App\Models\Option;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class MarketplaceExtensionFeedController extends Controller
{
    public function plugins(Request $request, ExtensionMarketplaceCatalogService $catalog): JsonResponse
    {
        // If query parameters or POST payload has 'slug' (and 'version'), behave as update checker
        if ($request->has('slug') || $request->isMethod('POST')) {
            $slug = $request->input('slug');
            $version = $request->input('version');
            $licenseKey = $request->input('license_key');
            $domain = $request->input('domain') ?: request()->getHost();

            if (!$slug || !$version) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing slug or version.'
                ], 400);
            }

            // Find product in options matching this slug
            $product = DB::table('options')
                ->where('o_type', 'store')
                ->where('name', $slug)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plugin not found.'
                ], 404);
            }

            // Check if product is paid
            $isPaid = $product->o_order > 0;
            if ($isPaid) {
                if (!$licenseKey) {
                    return response()->json([
                        'success' => false,
                        'message' => 'License key is required.'
                    ], 400);
                }

                $cleanDomain = preg_replace('/^https?:\/\/(www\.)?/i', '', $domain);
                $cleanDomain = rtrim($cleanDomain, '/');

                $license = DB::table('product_licenses')
                    ->where('product_id', $product->id)
                    ->where('license_key', $licenseKey)
                    ->first();

                if (!$license) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid license key.'
                    ], 400);
                }

                if ($license->domain) {
                    $existingDomain = preg_replace('/^https?:\/\/(www\.)?/i', '', $license->domain);
                    $existingDomain = rtrim($existingDomain, '/');
                    if (strcasecmp($existingDomain, $cleanDomain) !== 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'License key is already registered to a different domain.'
                        ], 400);
                    }
                } else {
                    // Activate domain
                    DB::table('product_licenses')
                        ->where('id', $license->id)
                        ->update([
                            'domain'       => $domain,
                            'activated_at' => now(),
                            'updated_at'   => now()
                        ]);
                }
            }

            // Find the latest file
            $fileOption = DB::table('options')
                ->where('o_parent', $product->id)
                ->where('o_type', 'store_file')
                ->orderBy('id', 'desc')
                ->first();

            if (!$fileOption) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files available for this plugin.'
                ], 404);
            }

            // Build download URL
            $downloadUrl = route('api.marketplace.extensions.download', [
                'slug'        => $slug,
                'license_key' => $licenseKey,
                'domain'      => $domain,
            ]);

            return response()->json([
                'success'      => true,
                'version'      => $fileOption->name,
                'download_url' => $downloadUrl,
                'changelog'    => $fileOption->o_valuer ?? '',
            ]);
        }

        return response()->json($catalog->build('plugins'));
    }

    public function themes(ExtensionMarketplaceCatalogService $catalog): JsonResponse
    {
        return response()->json($catalog->build('themes'));
    }

    public function download(Request $request): SymfonyResponse
    {
        $request->validate([
            'slug'        => ['required', 'string'],
            'license_key' => ['nullable', 'string'],
            'domain'      => ['nullable', 'string'],
        ]);

        $slug = $request->input('slug');
        $licenseKey = $request->input('license_key');
        $domain = $request->input('domain') ?: request()->getHost();

        $product = DB::table('options')
            ->where('o_type', 'store')
            ->where('name', $slug)
            ->first();

        if (!$product) {
            abort(404, 'Plugin not found.');
        }

        $isPaid = $product->o_order > 0;
        if ($isPaid) {
            if (!$licenseKey) {
                abort(400, 'License key is required.');
            }

            $cleanDomain = preg_replace('/^https?:\/\/(www\.)?/i', '', $domain);
            $cleanDomain = rtrim($cleanDomain, '/');

            $license = DB::table('product_licenses')
                ->where('product_id', $product->id)
                ->where('license_key', $licenseKey)
                ->first();

            if (!$license) {
                abort(400, 'Invalid license key.');
            }

            if ($license->domain) {
                $existingDomain = preg_replace('/^https?:\/\/(www\.)?/i', '', $license->domain);
                $existingDomain = rtrim($existingDomain, '/');
                if (strcasecmp($existingDomain, $cleanDomain) !== 0) {
                    abort(400, 'License key is registered to a different domain.');
                }
            }
        }

        $fileOption = DB::table('options')
            ->where('o_parent', $product->id)
            ->where('o_type', 'store_file')
            ->orderBy('id', 'desc')
            ->first();

        if (!$fileOption) {
            abort(404, 'No file found for this plugin.');
        }

        $short = Short::where('tp_id', $fileOption->id)->where('sh_type', 7867)->first();
        if (!$short) {
            abort(404, 'Download link not found.');
        }

        // Increment clicks
        $short->increment('clik');

        if (!filter_var($short->url, FILTER_VALIDATE_URL)) {
            $relativePath = ltrim($short->url, '/');
            $basePath = base_path($relativePath);
            if (file_exists($basePath)) {
                return response()->download($basePath);
            }
            $publicPath = public_path($relativePath);
            if (file_exists($publicPath)) {
                return response()->download($publicPath);
            }
            if (Storage::exists($short->url)) {
                return Storage::download($short->url);
            }
            abort(404, 'File missing from storage');
        }

        return redirect($short->url);
    }
}
