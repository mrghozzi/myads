<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class InstallerGuard
{
    /**
     * Prevent access to installer after installation is complete.
     * "Complete" means: storage/installed file exists AND an admin user exists.
     * This allows the installer to continue even if migrations ran but admin
     * creation hasn't completed yet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installedFile = storage_path('installed');

        // Always allow update route ONLY if not fully installed yet.
        // Once installed, require admin authentication.
        if ($request->is('install/update') || $request->is('install/update/*')) {
            // If not installed at all, allow access (first-time setup)
            if (!file_exists($installedFile)) {
                return $next($request);
            }

            // SECURITY: After installation, require admin authentication for update routes
            // to prevent unauthorized users from running migrations/seeders.
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isSuperAdmin()) {
                return $next($request);
            }

            return redirect('/')->with('error', __('access_denied'));
        }

        // If installed marker doesn't exist → allow all installer routes
        if (!file_exists($installedFile)) {
            return $next($request);
        }

        // Installed marker exists — verify DB is actually functional and has an admin
        try {
            // If users table is missing → DB not migrated yet, allow installer
            if (!Schema::hasTable('users')) {
                return $next($request);
            }

            // If no user with id=1 exists → installation not complete, allow installer
            $adminExists = DB::table('users')->where('id', 1)->exists();
            if (!$adminExists) {
                return $next($request);
            }

        } catch (\Exception $e) {
            // DB connection failed entirely → allow installer
            return $next($request);
        }

        // Allow access to finish route (needed after update completes)
        if ($request->is('install/finish')) {
            return $next($request);
        }

        // Installation is fully complete — block all installer routes
        return redirect('/')->with('error', 'Application is already installed.');
    }
}

