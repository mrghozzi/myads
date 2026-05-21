<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicenseApiController extends Controller
{
    /**
     * Verify a plugin license key for a given client domain.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'license_key' => ['required', 'string'],
            'domain'      => ['required', 'string'],
            'plugin'      => ['required', 'string'],
        ]);

        $licenseKey = $request->input('license_key');
        $domain = $request->input('domain');
        $pluginSlug = $request->input('plugin');

        // Clean domain (remove protocol and www if present)
        $domain = preg_replace('/^https?:\/\/(www\.)?/i', '', $domain);
        $domain = rtrim($domain, '/');

        // Check if there is a store product matching the plugin slug
        $product = DB::table('options')
            ->where('o_type', 'store')
            ->where('name', $pluginSlug)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Plugin product not found in the store.'
            ], 404);
        }

        // Find the license
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

        // If the license is already activated on a different domain
        if ($license->domain) {
            $existingDomain = preg_replace('/^https?:\/\/(www\.)?/i', '', $license->domain);
            $existingDomain = rtrim($existingDomain, '/');

            if (strcasecmp($existingDomain, $domain) !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => "This license is already registered on domain: {$license->domain}."
                ], 400);
            }
        }

        // Activate it if not already activated
        if (!$license->domain) {
            DB::table('product_licenses')
                ->where('id', $license->id)
                ->update([
                    'domain'       => $domain,
                    'activated_at' => now(),
                    'updated_at'   => now()
                ]);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'License successfully verified and activated.',
            'license_key' => $licenseKey,
            'domain'      => $domain
        ]);
    }
}
