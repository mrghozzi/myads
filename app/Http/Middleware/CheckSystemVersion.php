<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class CheckSystemVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Define current code version
        $myads_generation = "4";
        $myads_Version = "0";
        $myads_Update = "0";
        $stversion = "{$myads_generation}.{$myads_Version}.{$myads_Update}";
        $version_name = "{$myads_generation}-{$myads_Version}-{$myads_Update}";

        // Use cache to avoid hitting DB on every request (e.g., check once per hour or session)
        // But for development/first run, we want it to run.
        // Let's cache the "checked" status for 60 minutes.
        if (!Cache::has('system_version_checked')) {
            try {
                // Check DB version
                $option = Option::where('o_type', 'version')->first();

                if ($option) {
                    if ($option->o_valuer != $stversion) {
                        $option->update([
                            'o_valuer' => $stversion,
                            'name' => $version_name // Update name too as per old script logic
                        ]);
                    }
                } else {
                    // Insert if missing
                    Option::create([
                        'name' => $version_name,
                        'o_valuer' => $stversion,
                        'o_type' => 'version',
                        'o_parent' => 0,
                        'o_order' => 0,
                        'o_mode' => '0'
                    ]);
                }
                
                Cache::put('system_version_checked', true, 60 * 60); // Cache for 1 hour

            } catch (\Exception $e) {
                // Ignore DB errors (e.g. during migration)
            }
        }

        return $next($request);
    }
}
