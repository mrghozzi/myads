<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Option;

class RequireMobileApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force Accept: application/json to avoid 500 errors on exceptions like ModelNotFoundException
        $request->headers->set('Accept', 'application/json');

        // Allow bypassing if explicitly allowed or in debug mode (optional, but let's keep it strictly secured as requested)
        $providedKey = $request->header('X-API-KEY') ?? $request->query('api_key');
        
        if (empty($providedKey)) {
            return response()->json(['error' => 'API Key is missing.'], 401);
        }

        $validKeyOption = Option::where('o_type', 'mobile_api')->where('name', 'api_key')->first();
        $validKey = $validKeyOption ? $validKeyOption->o_valuer : null;

        if (empty($validKey)) {
            return response()->json(['error' => 'API is currently disabled by administrator.'], 403);
        }

        if ($providedKey !== $validKey) {
            return response()->json(['error' => 'Invalid API Key.'], 401);
        }

        return $next($request);
    }
}
