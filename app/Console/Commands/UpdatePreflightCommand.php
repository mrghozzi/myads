<?php

namespace App\Console\Commands;

use App\Services\UpdateSafetyService;
use Illuminate\Console\Command;

class UpdatePreflightCommand extends Command
{
    protected $signature = 'myads:update:preflight';

    protected $description = 'Run a safety preflight before applying a MYADS update.';

    public function handle(UpdateSafetyService $safety): int
    {
        $report = $safety->run();

        $this->components->info(__('messages.update_preflight_title'));
        $this->line(__('messages.update_preflight_description'));
        $this->newLine();

        $this->table(
            [__('messages.status'), __('messages.details')],
            array_map(function (array $check): array {
                return [
                    $check['title'],
                    sprintf('[%s] %s', strtoupper($check['status']), $check['detail']),
                ];
            }, $report->checks)
        );

        if ($report->isSafe()) {
            $this->components->info(__('messages.update_preflight_passed'));

            return self::SUCCESS;
        }

        foreach ($report->failureMessages() as $message) {
            $this->components->error($message);
        }

        $this->components->warn(__('messages.update_preflight_failed'));

        return self::FAILURE;
    }
}
