<?php

namespace App\Services;

use App\Models\PtsLedgerEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DoubleEntryLedgerService
{
    public const ACCOUNT_SYSTEM_REWARDS = 'system:rewards';
    public const ACCOUNT_SYSTEM_ADS = 'system:ad_network';
    public const ACCOUNT_SYSTEM_MARKETPLACE = 'system:marketplace';
    public const ACCOUNT_SYSTEM_VOUCHERS = 'system:vouchers';
    public const ACCOUNT_SYSTEM_TREASURY = 'system:group_treasury';
    public const ACCOUNT_SYSTEM_FEES = 'system:platform_fees';
    public const ACCOUNT_SYSTEM_ADJUSTMENTS = 'system:admin_adjustments';

    /**
     * Record a balanced double-entry transaction between two accounts.
     * Guarantees that debit + credit = 0.00.
     *
     * @param string $debitAccount e.g. 'user:123' or 'system:rewards'
     * @param string $creditAccount e.g. 'user:456' or 'system:ad_network'
     * @param float $amount Positive transaction amount
     * @param string $entryType e.g. 'transfer', 'ad_reward', 'quest_reward'
     * @param string|null $description Human-readable note
     * @param string|null $referenceType Polymorphic reference type
     * @param int|string|null $referenceId Polymorphic reference ID
     * @param array $metadata Context metadata
     * @param bool $adjustUserPoints Whether to mutate User::$pts in database
     * @param string|null $transactionUuid Custom or pre-assigned UUID
     * @return array [PtsLedgerEntry $debitEntry, PtsLedgerEntry $creditEntry]
     */
    public function recordDoubleEntry(
        string $debitAccount,
        string $creditAccount,
        float $amount,
        string $entryType,
        ?string $description = null,
        ?string $referenceType = null,
        int|string|null $referenceId = null,
        array $metadata = [],
        bool $adjustUserPoints = true,
        ?string $transactionUuid = null
    ): array {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Transaction amount must be strictly greater than 0. Received: {$amount}");
        }

        $uuid = $transactionUuid ?: Str::uuid()->toString();

        return DB::transaction(function () use (
            $debitAccount,
            $creditAccount,
            $amount,
            $entryType,
            $description,
            $referenceType,
            $referenceId,
            $metadata,
            $adjustUserPoints,
            $uuid
        ) {
            // 1. Process Debit Leg (Outflow: negative amount)
            $debitUserId = null;
            $debitBalanceAfter = null;
            $debitAccountType = $this->extractAccountType($debitAccount);

            if (str_starts_with($debitAccount, 'user:')) {
                $debitUserId = (int) substr($debitAccount, 5);
                if ($adjustUserPoints) {
                    $sender = User::query()->lockForUpdate()->findOrFail($debitUserId);
                    $sender->pts = (float) $sender->pts - $amount;
                    $sender->save();
                    $debitBalanceAfter = (float) $sender->pts;
                } else {
                    $user = User::query()->find($debitUserId);
                    $debitBalanceAfter = $user ? (float) $user->pts : null;
                }
            }

            $debitEntry = PtsLedgerEntry::create([
                'transaction_uuid' => $uuid,
                'account_type' => $debitAccountType,
                'account_id' => $debitAccount,
                'user_id' => $debitUserId,
                'amount' => -$amount,
                'balance_after' => $debitBalanceAfter,
                'entry_type' => $entryType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId ? (string) $referenceId : null,
                'description' => $description,
                'metadata' => array_merge($metadata, ['leg' => 'debit', 'counterpart' => $creditAccount]),
            ]);

            // 2. Process Credit Leg (Inflow: positive amount)
            $creditUserId = null;
            $creditBalanceAfter = null;
            $creditAccountType = $this->extractAccountType($creditAccount);

            if (str_starts_with($creditAccount, 'user:')) {
                $creditUserId = (int) substr($creditAccount, 5);
                if ($adjustUserPoints) {
                    $recipient = User::query()->lockForUpdate()->findOrFail($creditUserId);
                    $recipient->pts = (float) $recipient->pts + $amount;
                    $recipient->save();
                    $creditBalanceAfter = (float) $recipient->pts;
                } else {
                    $user = User::query()->find($creditUserId);
                    $creditBalanceAfter = $user ? (float) $user->pts : null;
                }
            }

            $creditEntry = PtsLedgerEntry::create([
                'transaction_uuid' => $uuid,
                'account_type' => $creditAccountType,
                'account_id' => $creditAccount,
                'user_id' => $creditUserId,
                'amount' => $amount,
                'balance_after' => $creditBalanceAfter,
                'entry_type' => $entryType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId ? (string) $referenceId : null,
                'description' => $description,
                'metadata' => array_merge($metadata, ['leg' => 'credit', 'counterpart' => $debitAccount]),
            ]);

            return [$debitEntry, $creditEntry];
        });
    }

    /**
     * Execute a peer-to-peer PTS transfer between two members.
     */
    public function transfer(
        User|int $sender,
        User|int $recipient,
        float $amount,
        ?string $description = null,
        array $metadata = []
    ): array {
        $senderId = $sender instanceof User ? $sender->id : (int) $sender;
        $recipientId = $recipient instanceof User ? $recipient->id : (int) $recipient;

        if ($senderId === $recipientId) {
            throw new InvalidArgumentException("Cannot transfer PTS to self.");
        }

        return $this->recordDoubleEntry(
            debitAccount: "user:{$senderId}",
            creditAccount: "user:{$recipientId}",
            amount: $amount,
            entryType: 'transfer',
            description: $description ?: "Member transfer from User #{$senderId} to User #{$recipientId}",
            referenceType: 'user',
            referenceId: $recipientId,
            metadata: $metadata,
            adjustUserPoints: true
        );
    }

    /**
     * Record an interaction between a user and a system contra account.
     * Supports both positive awards (system -> user) and deductions (user -> system).
     */
    public function systemAward(
        User|int $user,
        float $amount,
        string $entryType,
        ?string $systemAccount = null,
        ?string $description = null,
        ?string $referenceType = null,
        int|string|null $referenceId = null,
        array $metadata = [],
        bool $adjustUserPoints = true,
        ?string $transactionUuid = null
    ): array {
        $userId = $user instanceof User ? $user->id : (int) $user;
        $contraAccount = $systemAccount ?: $this->resolveSystemAccountForType($entryType);

        if ($amount > 0) {
            // System pays User: Debit System, Credit User
            return $this->recordDoubleEntry(
                debitAccount: $contraAccount,
                creditAccount: "user:{$userId}",
                amount: $amount,
                entryType: $entryType,
                description: $description,
                referenceType: $referenceType,
                referenceId: $referenceId,
                metadata: $metadata,
                adjustUserPoints: $adjustUserPoints,
                transactionUuid: $transactionUuid
            );
        }

        if ($amount < 0) {
            // User pays System: Debit User, Credit System
            return $this->recordDoubleEntry(
                debitAccount: "user:{$userId}",
                creditAccount: $contraAccount,
                amount: abs($amount),
                entryType: $entryType,
                description: $description,
                referenceType: $referenceType,
                referenceId: $referenceId,
                metadata: $metadata,
                adjustUserPoints: $adjustUserPoints,
                transactionUuid: $transactionUuid
            );
        }

        // Amount is 0: No-op
        return [];
    }

    /**
     * Resolve the appropriate system contra account for a transaction type.
     */
    public function resolveSystemAccountForType(string $type): string
    {
        $normalized = strtolower($type);

        if (str_starts_with($normalized, 'ad_') ||
            str_starts_with($normalized, 'banner') ||
            str_starts_with($normalized, 'smart_ad') ||
            str_starts_with($normalized, 'visit_') ||
            str_starts_with($normalized, 'yt_') ||
            str_contains($normalized, 'campaign') ||
            str_contains($normalized, 'exchange')) {
            return self::ACCOUNT_SYSTEM_ADS;
        }

        if (str_starts_with($normalized, 'store_') ||
            str_starts_with($normalized, 'order_') ||
            str_starts_with($normalized, 'product_') ||
            str_contains($normalized, 'escrow')) {
            return self::ACCOUNT_SYSTEM_MARKETPLACE;
        }

        if (str_starts_with($normalized, 'voucher_') ||
            str_contains($normalized, 'voucher')) {
            return self::ACCOUNT_SYSTEM_VOUCHERS;
        }

        if (str_starts_with($normalized, 'group_') ||
            str_contains($normalized, 'group_join')) {
            return self::ACCOUNT_SYSTEM_TREASURY;
        }

        if (str_starts_with($normalized, 'fee_') ||
            str_contains($normalized, 'commission')) {
            return self::ACCOUNT_SYSTEM_FEES;
        }

        if (str_starts_with($normalized, 'admin_') ||
            str_contains($normalized, 'adjustment') ||
            str_contains($normalized, 'penalty')) {
            return self::ACCOUNT_SYSTEM_ADJUSTMENTS;
        }

        // Default reward & incentive pool
        return self::ACCOUNT_SYSTEM_REWARDS;
    }

    /**
     * Run a comprehensive mathematical integrity verification of the entire PTS ledger.
     */
    public function verifyIntegrity(): array
    {
        $stats = DB::table('pts_ledger_entries')
            ->selectRaw('
                COUNT(*) as total_entries,
                COUNT(DISTINCT transaction_uuid) as total_transactions,
                COALESCE(SUM(amount), 0) as net_variance
            ')
            ->first();

        $netVariance = (float) ($stats->net_variance ?? 0.0);
        $totalEntries = (int) ($stats->total_entries ?? 0);
        $totalTransactions = (int) ($stats->total_transactions ?? 0);

        // Find unbalanced transactions: sum(amount) != 0 OR count(leg) < 2
        $unbalanced = DB::table('pts_ledger_entries')
            ->select('transaction_uuid')
            ->selectRaw('COUNT(*) as leg_count, SUM(amount) as leg_sum')
            ->groupBy('transaction_uuid')
            ->havingRaw('ABS(SUM(amount)) > 0.001 OR COUNT(*) < 2')
            ->limit(50)
            ->get();

        // System accounts breakdown
        $systemBalances = DB::table('pts_ledger_entries')
            ->where('account_type', '!=', 'user')
            ->select('account_id')
            ->selectRaw('SUM(amount) as balance, COUNT(*) as entries_count')
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id')
            ->map(fn ($row) => [
                'balance' => (float) $row->balance,
                'entries_count' => (int) $row->entries_count,
            ])
            ->all();

        $isBalanced = abs($netVariance) < 0.001 && $unbalanced->isEmpty();

        return [
            'is_balanced' => $isBalanced,
            'net_variance' => $netVariance,
            'total_entries' => $totalEntries,
            'total_transactions' => $totalTransactions,
            'unbalanced_count' => $unbalanced->count(),
            'unbalanced_samples' => $unbalanced->toArray(),
            'system_accounts' => $systemBalances,
        ];
    }

    /**
     * Extract normalized account type string from account identifier.
     */
    private function extractAccountType(string $account): string
    {
        if (str_starts_with($account, 'user:')) {
            return 'user';
        }

        if (str_starts_with($account, 'system:')) {
            return 'system_' . substr($account, 7);
        }

        return 'custom';
    }
}

