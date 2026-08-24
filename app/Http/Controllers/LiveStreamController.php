<?php

namespace App\Http\Controllers;

use App\Services\Realtime\LiveEventStreamService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LiveStreamController extends Controller
{
    public function __construct(
        protected LiveEventStreamService $streamService
    ) {}

    /**
     * Stream real-time events to the authenticated client using Server-Sent Events (SSE).
     */
    public function stream(Request $request): StreamedResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Release PHP session lock immediately so parallel web requests from the same user are not blocked
        if ($request->hasSession() && $request->session()->isStarted()) {
            $request->session()->save();
        }

        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ];

        // Configurable max execution duration per SSE connection (default: 20 seconds, or 1 iteration in unit tests)
        $isUnitTest = app()->runningUnitTests();
        $maxDuration = $isUnitTest ? (int) $request->query('max_duration', 1) : 20;
        $pollInterval = $isUnitTest ? 1 : 2; // seconds between polls

        return response()->stream(function () use ($user, $maxDuration, $pollInterval, $isUnitTest) {
            // Disable output compression and flush any existing buffers
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            if (!$isUnitTest) {
                while (ob_get_level() > 0) {
                    @ob_end_flush();
                }
            }

            // 1. Initial Handshake Message
            $handshake = $this->streamService->getInitialHandshake($user);
            echo $this->streamService->formatSseMessage('handshake', $handshake, 'init');
            flush();

            $startTime = time();
            $lastCheckTimestamp = $startTime;
            $iteration = 0;

            // 2. Event Streaming Loop
            while ((time() - $startTime) < $maxDuration) {
                // Check if connection was aborted by client
                if (connection_aborted()) {
                    break;
                }

                $iteration++;

                // Poll for new user events
                $events = $this->streamService->pollUserEvents($user, $lastCheckTimestamp);
                foreach ($events as $event) {
                    $eventId = $event['type'] . '_' . time();
                    echo $this->streamService->formatSseMessage($event['type'], $event['data'], $eventId);
                    flush();
                }

                // Periodic Heartbeat Ping
                echo $this->streamService->formatSseMessage('ping', ['time' => time()], 'ping_' . time());
                flush();

                $lastCheckTimestamp = time();

                // If running in unit test mode and reached maxDuration, stop loop
                if ($isUnitTest && $iteration >= $maxDuration) {
                    break;
                }

                sleep($pollInterval);
            }

            // 3. Clean Reconnect Signal to Browser
            echo $this->streamService->formatSseMessage('reconnect', ['status' => 'reconnect_requested', 'timestamp' => time()]);
            flush();
        }, 200, $headers);
    }
}
