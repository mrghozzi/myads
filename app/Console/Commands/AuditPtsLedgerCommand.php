<?php

namespace App\Console\Commands;

use App\Services\DoubleEntryLedgerService;
use Illuminate\Console\Command;

class AuditPtsLedgerCommand extends Command
{
    protected $signature = 'myads:pts:audit';

    protected $description = 'Run a mathematical double-entry integrity audit on the PTS ledger.';

    public function handle(DoubleEntryLedgerService $ledger): int
    {
        $this->components->info('Double-Entry PTS Ledger Integrity Audit');
        $this->line('Verifying mathematical balance, transaction pairs, and system contra accounts...');
        $this->newLine();

        $audit = $ledger->verifyIntegrity();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Journal Entries', number_format($audit['total_entries'])],
                ['Total Transactions', number_format($audit['total_transactions'])],
                ['Net Variance (Debit + Credit)', sprintf('%.4f PTS', $audit['net_variance'])],
                ['Unbalanced Transactions', number_format($audit['unbalanced_count'])],
                ['Ledger Status', $audit['is_balanced'] ? '[BALANCED] Zero Discrepancy' : '[UNBALANCED] Discrepancy Found'],
            ]
        );

        if (!empty($audit['system_accounts'])) {
            $this->newLine();
            $this->components->info('System Contra Accounts Balance:');
            $rows = [];
            foreach ($audit['system_accounts'] as $account => $data) {
                $rows[] = [
                    $account,
                    sprintf('%.2f PTS', $data['balance']),
                    number_format($data['entries_count']),
                ];
            }
            $this->table(['Account', 'Net Balance', 'Entries'], $rows);
        }

        if ($audit['is_balanced']) {
            $this->components->info('Audit Passed: Ledger is mathematically balanced (sum = 0.00).');
            return self::SUCCESS;
        }

        $this->components->error('Audit Failed: Unbalanced transactions detected.');
        return self::FAILURE;
    }
}

