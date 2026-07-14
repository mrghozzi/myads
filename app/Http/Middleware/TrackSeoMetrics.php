<?php

namespace App\Http\Middleware;

use App\Services\SeoManager;
use App\Services\SeoMetricsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSeoMetrics
{
    public function __construct(
        private readonly SeoManager $seoManager,
        private readonly SeoMetricsService $metrics,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($request->is('install') || $request->is('install/*') || $request->is('up')) {
            return $response;
        }

        try {
            $settings = \App\Support\CommunityFeedSettings::all();
            if (empty($settings['track_seo_metrics'])) {
                return $response;
            }

            $this->seoManager->resolve($request);

            $this->metrics->record(
                request: $request,
                response: $response,
                context: $this->seoManager->context(),
                scopeKey: $this->seoManager->context()['scope_key'] ?? null
            );
        } catch (\Throwable) {
            // Silence SEO tracking errors if DB is not ready
        }

        return $response;
    }
}
