<?php

namespace App\Http\Controllers;

use App\Models\DeveloperApp;
use App\Models\Like;
use App\Models\Status;
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

    public function followFrame(DeveloperApp $app)
    {
        if (!$app->isActive()) {
            return response('Application is not active', 400);
        }

        $owner = $app->user;
        $followersCount = Like::where('sid', $owner->id)->where('type', 1)->count();
        $isFollowing = auth()->check()
            ? Like::where('uid', auth()->id())->where('sid', $owner->id)->where('type', 1)->exists()
            : false;

        return view('theme::developer.widgets.follow', compact('app', 'owner', 'followersCount', 'isFollowing'));
    }

    public function profileFrame(DeveloperApp $app)
    {
        if (!$app->isActive()) {
            return response('Application is not active', 400);
        }

        $owner = $app->user;
        $followersCount = Like::where('sid', $owner->id)->where('type', 1)->count();
        $isFollowing = auth()->check()
            ? Like::where('uid', auth()->id())->where('sid', $owner->id)->where('type', 1)->exists()
            : false;

        return view('theme::developer.widgets.profile', compact('app', 'owner', 'followersCount', 'isFollowing'));
    }

    public function contentFrame(DeveloperApp $app)
    {
        if (!$app->isActive()) {
            return response('Application is not active', 400);
        }

        $owner = $app->user;
        $posts = Status::where('uid', $owner->id)
            ->where('statu', 1)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('theme::developer.widgets.content', compact('app', 'owner', 'posts'));
    }

    public function followToggle(DeveloperApp $app)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'redirect' => route('login')], 401);
        }

        $owner = $app->user;
        if ((int) auth()->id() === (int) $owner->id) {
            return response()->json(['success' => false, 'message' => 'Cannot follow yourself'], 400);
        }

        $existing = Like::where('uid', auth()->id())
            ->where('sid', $owner->id)
            ->where('type', 1)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            Like::create([
                'uid' => auth()->id(),
                'sid' => $owner->id,
                'type' => 1,
                'time_t' => time(),
            ]);
            $following = true;
        }

        $followersCount = Like::where('sid', $owner->id)->where('type', 1)->count();

        return response()->json([
            'success' => true,
            'following' => $following,
            'count' => $followersCount,
        ]);
    }
}
