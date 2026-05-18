<?php

namespace App\Console\Commands;

use App\Services\CustomAds\CustomAdSettlementService;
use Illuminate\Console\Command;

class SettleCustomAds extends Command
{
    protected $signature = 'myads:custom-ads-settle {--date= : Settlement date in YYYY-MM-DD format. Defaults to yesterday.}';

    protected $description = 'Release daily PTS payouts for active custom ad deals.';

    public function handle(CustomAdSettlementService $settlement): int
    {
        $summary = $settlement->releaseDailyPayouts($this->option('date') ?: null);

        $this->info(sprintf(
            'Custom ads settlement complete. Paid: %d, skipped: %d, completed: %d.',
            $summary['paid'],
            $summary['skipped'],
            $summary['completed']
        ));

        return self::SUCCESS;
    }
}
