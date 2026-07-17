<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Quest;
use App\Models\QuestProgress;
use App\Models\PtsVoucher;
use App\Services\PointLedgerService;

class GamificationApiController extends Controller
{
    public function __construct(
        private readonly PointLedgerService $ledger
    ) {
    }

    public function quests(Request $request)
    {
        $userId = Auth::id();
        $quests = Quest::active()->get();
        $progress = QuestProgress::where('user_id', $userId)->get()->keyBy('quest_id');

        $data = $quests->map(function ($quest) use ($progress) {
            $prog = $progress->get($quest->id);
            return [
                'id' => $quest->id,
                'name' => $quest->name,
                'description' => $quest->description,
                'type' => $quest->type,
                'target_value' => $quest->target_value,
                'reward_pts' => $quest->reward_pts,
                'current_value' => $prog ? $prog->current_value : 0,
                'is_completed' => $prog ? $prog->is_completed : false,
                'is_claimed' => $prog ? $prog->is_claimed : false,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function claimQuest(Request $request, $id)
    {
        $userId = Auth::id();
        $quest = Quest::active()->findOrFail($id);
        $progress = QuestProgress::where('user_id', $userId)->where('quest_id', $id)->first();

        if (!$progress || !$progress->is_completed) {
            return response()->json(['success' => false, 'message' => __('messages.quest_not_completed')], 400);
        }

        if ($progress->is_claimed) {
            return response()->json(['success' => false, 'message' => __('messages.quest_already_claimed')], 400);
        }

        DB::beginTransaction();
        try {
            $progress->is_claimed = true;
            $progress->claimed_at = now();
            $progress->save();

            $this->ledger->award(Auth::user(), $quest->reward_pts, 'quest_reward', 'points_awarded', 'quest', $quest->id);

            DB::commit();
            return response()->json(['success' => true, 'message' => __('messages.reward_claimed_successfully')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }

    public function transferPts(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
            'amount' => ['required', 'integer', 'min:10'],
        ]);

        $sender = Auth::user();
        $recipient = User::where('username', $request->username)->firstOrFail();
        $amount = (int) $request->amount;

        if ($sender->id === $recipient->id) {
            return response()->json(['success' => false, 'message' => __('messages.cannot_transfer_to_self')], 400);
        }

        if ($sender->pts < $amount) {
            return response()->json(['success' => false, 'message' => __('messages.insufficient_points')], 400);
        }

        DB::beginTransaction();
        try {
            $sender = User::where('id', $sender->id)->lockForUpdate()->first();
            if ($sender->pts < $amount) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => __('messages.insufficient_points')], 400);
            }

            $this->ledger->deduct($sender, $amount, 'pts_transfer_sent', 'points_deducted', 'user', $recipient->id);
            $this->ledger->award($recipient, $amount, 'pts_transfer_received', 'points_awarded', 'user', $sender->id);

            app(\App\Services\NotificationService::class)->create(
                $recipient->id,
                $sender->id,
                'pts_transfer_received',
                $sender->id,
                $amount
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => __('messages.points_transferred_successfully')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }

    public function createVoucher(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:10'],
        ]);

        $user = Auth::user();
        $amount = (int) $request->amount;

        if ($user->pts < $amount) {
            return response()->json(['success' => false, 'message' => __('messages.insufficient_points')], 400);
        }

        DB::beginTransaction();
        try {
            $user = User::where('id', $user->id)->lockForUpdate()->first();
            if ($user->pts < $amount) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => __('messages.insufficient_points')], 400);
            }

            $this->ledger->deduct($user, $amount, 'pts_voucher_created', 'points_deducted');

            $code = strtoupper(\Illuminate\Support\Str::random(10));
            $voucher = PtsVoucher::create([
                'user_id' => $user->id,
                'code' => $code,
                'amount' => $amount,
                'status' => 'active',
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => __('messages.voucher_created_successfully'), 'data' => ['code' => $code]]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }

    public function claimVoucher(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = Auth::user();
        
        DB::beginTransaction();
        try {
            $voucher = PtsVoucher::where('code', $request->code)->lockForUpdate()->first();
            
            if (!$voucher || $voucher->status !== 'active') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => __('messages.invalid_voucher_code')], 400);
            }

            if ($voucher->user_id === $user->id) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => __('messages.cannot_claim_own_voucher')], 400);
            }

            $voucher->status = 'claimed';
            $voucher->claimed_by = $user->id;
            $voucher->claimed_at = now();
            $voucher->save();

            $this->ledger->award($user, $voucher->amount, 'pts_voucher_claimed', 'points_awarded', 'voucher', $voucher->id);

            app(\App\Services\NotificationService::class)->create(
                $voucher->user_id,
                $user->id,
                'pts_voucher_was_claimed',
                $voucher->id,
                $voucher->amount
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => __('messages.voucher_claimed_successfully'), 'data' => ['amount' => $voucher->amount]]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }
}
