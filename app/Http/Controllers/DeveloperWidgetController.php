<?php

namespace App\Http\Controllers;

use App\Models\DeveloperApp;
use App\Services\DeveloperWidgetService;
use Illuminate\Http\Request;

class DeveloperWidgetController extends Controller
{
    public function follow(DeveloperApp $app, DeveloperWidgetService $widgetService)
    {
        if (!$app->isActive()) {
            return response()->json(['error' => 'App not active'], 400);
        }

        $js = $widgetService->generateWidgetScript($app, 'follow');
        return response($js)->header('Content-Type', 'application/javascript');
    }

    public function profile(DeveloperApp $app, DeveloperWidgetService $widgetService)
    {
        if (!$app->isActive()) {
            return response()->json(['error' => 'App not active'], 400);
        }

        $js = $widgetService->generateWidgetScript($app, 'profile');
        return response($js)->header('Content-Type', 'application/javascript');
    }

    public function content(DeveloperApp $app, DeveloperWidgetService $widgetService)
    {
        if (!$app->isActive()) {
            return response()->json(['error' => 'App not active'], 400);
        }

        $js = $widgetService->generateWidgetScript($app, 'content');
        return response($js)->header('Content-Type', 'application/javascript');
    }
}
