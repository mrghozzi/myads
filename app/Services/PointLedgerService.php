<?php

namespace App\Services;

use App\Models\Option;
use App\Models\PointTransaction;
use App\Models\User;

class PointLedgerService
{
    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public function award(
        User|int $user,
        float $amount,
        string $type,
        ?string $descriptionKey = null,
        ?string $referenceType = null,
        int|string|null $referenceId = null,
        array $meta = [],
        bool $mirrorLegacyHistory = false
    ): PointTransaction {
        $userModel = $user instanceof User
            ? User::query()->lockForUpdate()->findOrFail($user->id)
            : User::query()->lockForUpdate()->findOrFail((int) $user);

        $userModel->pts = (float) $userModel->pts + $amount;
        $userModel->save();

        if (!$this->schema->supports('point_history')) {
            if ($mirrorLegacyHistory) {
                Option::create([
                    'name' => $descriptionKey ?: $type,
                    'o_valuer' => (string) $amount,
                    'o_type' => 'hest_pts',
                    'o_parent' => (int) $userModel->id,
                    'o_order' => 0,
                    'o_mode' => time(),
                ]);
            }

            return new PointTransaction([
                'user_id' => (int) $userModel->id,
                'amount' => $amount,
                'balance_after' => (float) $userModel->pts,
                'type' => $type,
                'description_key' => $descriptionKey,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId ? (int) $referenceId : null,
                'meta' => $meta,
            ]);
        }

        $transaction = PointTransaction::create([
            'user_id' => (int) $userModel->id,
            'amount' => $amount,
            'balance_after' => (float) $userModel->pts,
            'type' => $type,
            'description_key' => $descriptionKey,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId ? (int) $referenceId : null,
            'meta' => $meta,
        ]);

        if ($mirrorLegacyHistory) {
            Option::create([
                'name' => $descriptionKey ?: $type,
                'o_valuer' => (string) $amount,
                'o_type' => 'hest_pts',
                'o_parent' => (int) $userModel->id,
                'o_order' => 0,
                'o_mode' => time(),
            ]);
        }

        return $transaction;
    }
}
