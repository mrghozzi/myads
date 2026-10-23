<?php

namespace App\Http\Controllers;

use App\Models\DeveloperApp;
use App\Models\DeveloperWebhook;
use App\Models\DeveloperWebhookDelivery;
use App\Services\WebhookDispatcherService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperWebhookController extends Controller
{
    protected WebhookDispatcherService $dispatcher;

    public function __construct(WebhookDispatcherService $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    protected function checkAppOwnership(DeveloperApp $app): void
    {
        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }

    public function index(DeveloperApp $app)
    {
        $this->checkAppOwnership($app);

        $webhooks = $app->webhooks()->withCount('deliveries')->orderBy('id', 'desc')->get();
        $supportedEvents = WebhookDispatcherService::getSupportedEvents();

        $recentDeliveries = DeveloperWebhookDelivery::where('developer_app_id', $app->id)
            ->with('webhook')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return view('theme::developer.apps.webhooks', compact('app', 'webhooks', 'supportedEvents', 'recentDeliveries'));
    }

    public function store(Request $request, DeveloperApp $app)
    {
        $this->checkAppOwnership($app);

        $request->validate([
            'url' => 'required|url|max:1000',
            'events' => 'required|array|min:1',
            'events.*' => 'string',
        ]);

        $secret = 'whsec_' . bin2hex(random_bytes(24));

        $webhook = DeveloperWebhook::create([
            'developer_app_id' => $app->id,
            'url' => trim($request->url),
            'secret' => $secret,
            'events' => array_values(array_filter($request->events)),
            'is_active' => true,
            'failure_count' => 0,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.webhook_created'),
                'webhook' => $webhook,
            ]);
        }

        return back()->with('success', __('messages.webhook_created'));
    }

    public function update(Request $request, DeveloperApp $app, DeveloperWebhook $webhook)
    {
        $this->checkAppOwnership($app);

        if ((int) $webhook->developer_app_id !== (int) $app->id) {
            abort(404);
        }

        $request->validate([
            'url' => 'required|url|max:1000',
            'events' => 'required|array|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $webhook->update([
            'url' => trim($request->url),
            'events' => array_values(array_filter($request->events)),
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.webhook_updated'),
            ]);
        }

        return back()->with('success', __('messages.webhook_updated'));
    }

    public function destroy(DeveloperApp $app, DeveloperWebhook $webhook)
    {
        $this->checkAppOwnership($app);

        if ((int) $webhook->developer_app_id !== (int) $app->id) {
            abort(404);
        }

        $webhook->deliveries()->delete();
        $webhook->delete();

        return back()->with('success', __('messages.webhook_deleted'));
    }

    public function test(DeveloperApp $app, DeveloperWebhook $webhook)
    {
        $this->checkAppOwnership($app);

        if ((int) $webhook->developer_app_id !== (int) $app->id) {
            abort(404);
        }

        $delivery = $this->dispatcher->testPing($webhook);

        return response()->json([
            'success' => $delivery->isSuccess(),
            'status_code' => $delivery->status_code,
            'duration_ms' => $delivery->duration_ms,
            'response' => $delivery->response_body,
            'error' => $delivery->error_message,
        ]);
    }

    public function resend(DeveloperApp $app, DeveloperWebhookDelivery $delivery)
    {
        $this->checkAppOwnership($app);

        if ((int) $delivery->developer_app_id !== (int) $app->id) {
            abort(404);
        }

        $newDelivery = $this->dispatcher->redeliver($delivery);

        return response()->json([
            'success' => $newDelivery->isSuccess(),
            'status_code' => $newDelivery->status_code,
            'duration_ms' => $newDelivery->duration_ms,
            'response' => $newDelivery->response_body,
            'error' => $newDelivery->error_message,
        ]);
    }
}
