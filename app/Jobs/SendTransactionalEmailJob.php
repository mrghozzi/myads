<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTransactionalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Maximum seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     * Seconds to wait before retrying a failed job (exponential backoff).
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     *
     * @param string|array $recipient
     * @param Mailable|null $mailable
     * @param string|null $rawSubject
     * @param string|null $rawBody
     * @param string|null $rawFrom
     * @param string $channel
     */
    public function __construct(
        public string|array $recipient,
        public ?Mailable $mailable = null,
        public ?string $rawSubject = null,
        public ?string $rawBody = null,
        public ?string $rawFrom = null,
        public string $channel = 'default'
    ) {
        $this->onQueue($channel);
    }

    /**
     * Static factory helper to dispatch a Mailable.
     *
     * @param string|array $recipient
     * @param Mailable $mailable
     * @param string $channel
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function forMailable(string|array $recipient, Mailable $mailable, string $channel = 'default')
    {
        return static::dispatch($recipient, $mailable, null, null, null, $channel);
    }

    /**
     * Static factory helper to dispatch a raw text email.
     *
     * @param string|array $recipient
     * @param string $subject
     * @param string $body
     * @param string $channel
     * @param string|null $from
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function forRaw(string|array $recipient, string $subject, string $body, string $channel = 'high', ?string $from = null)
    {
        return static::dispatch($recipient, null, $subject, $body, $from, $channel);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if ($this->mailable) {
                Mail::to($this->recipient)->send($this->mailable);
                return;
            }

            if ($this->rawSubject !== null && $this->rawBody !== null) {
                Mail::raw($this->rawBody, function ($message) {
                    $message->to($this->recipient)
                        ->subject($this->rawSubject);

                    if ($this->rawFrom) {
                        $message->from($this->rawFrom);
                    }
                });
            }
        } catch (\Throwable $e) {
            $recipientStr = is_array($this->recipient) ? implode(',', $this->recipient) : $this->recipient;
            Log::error("SendTransactionalEmailJob attempt {$this->attempts()} failed for [{$recipientStr}]: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure after exhausting all retries.
     */
    public function failed(\Throwable $exception): void
    {
        $recipientStr = is_array($this->recipient) ? implode(',', $this->recipient) : $this->recipient;
        Log::error("SendTransactionalEmailJob exhausted all {$this->tries} retries for [{$recipientStr}]. Last error: " . $exception->getMessage());
    }
}
