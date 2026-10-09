<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Link;
use App\Models\ModerationLog;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\Report;
use App\Models\SecurityIpBan;
use App\Models\SmartAd;
use App\Models\Status;
use App\Models\User;
use App\Models\UserWarning;
use App\Services\NotificationService;
use App\Services\PointLedgerService;
use App\Support\SecuritySettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    public const DEFAULT_AUTO_QUARANTINE_THRESHOLD = 3;

    public function __construct(
        private readonly NotificationService $notifications,
        private readonly PointLedgerService $pointLedger
    ) {
    }

    /**
     * Execute a structured moderation action on a report.
     */
    public function takeAction(Report $report, string $action, array $data = [], ?User $moderator = null): array
    {
        $moderatorId = $moderator?->id ?? auth()->id();
        $notes = trim((string) ($data['notes'] ?? ''));
        $target = $this->resolveReportTarget($report);
        $targetUser = $this->resolveTargetUser($report, $target);

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'dismiss':
                    $report->statu = 0;
                    $report->action_taken = 'dismissed';
                    $report->action_notes = $notes;
                    $report->moderator_id = $moderatorId;
                    $report->resolved_at = time();
                    $report->save();

                    ModerationLog::create([
                        'moderator_id' => $moderatorId,
                        'target_type' => $this->resolveTargetTypeName((int) $report->s_type),
                        'target_id' => (int) $report->tp_id,
                        'action' => 'dismiss',
                        'reason' => $notes ?: __('messages.report_dismissed_unfounded'),
                        'report_id' => $report->id,
                        'metadata' => ['reporter_id' => $report->uid],
                        'created_at' => time(),
                    ]);

                    if ($report->reporter) {
                        $this->notifications->send(
                            $report->reporter,
                            __('messages.report_dismissed_notice', ['id' => $report->id]),
                            '',
                            'shield'
                        );
                    }
                    break;

                case 'hide_content':
                    $this->hideTargetContent($report, $target);

                    $report->statu = 0;
                    $report->action_taken = 'content_hidden';
                    $report->action_notes = $notes;
                    $report->moderator_id = $moderatorId;
                    $report->resolved_at = time();
                    $report->save();

                    ModerationLog::create([
                        'moderator_id' => $moderatorId,
                        'target_type' => $this->resolveTargetTypeName((int) $report->s_type),
                        'target_id' => (int) $report->tp_id,
                        'action' => 'hide_content',
                        'reason' => $notes ?: __('messages.content_hidden_community_standards'),
                        'report_id' => $report->id,
                        'metadata' => ['target_user_id' => $targetUser?->id],
                        'created_at' => time(),
                    ]);

                    if ($targetUser) {
                        $this->notifications->send(
                            $targetUser,
                            __('messages.moderation_content_hidden_notice', [
                                'reason' => $notes ?: __('messages.violates_community_standards'),
                            ]),
                            '',
                            'alert'
                        );
                    }

                    if ($report->reporter) {
                        $this->notifications->send(
                            $report->reporter,
                            __('messages.report_resolved_action_taken', ['id' => $report->id]),
                            '',
                            'check'
                        );
                    }
                    break;

                case 'delete_content':
                    $deductPts = max(0, (int) ($data['deduct_points'] ?? 0));
                    if ($deductPts > 0 && $targetUser) {
                        $this->pointLedger->award(
                            $targetUser,
                            -$deductPts,
                            'moderation_violation_penalty',
                            'penalty',
                            'report',
                            $report->id
                        );
                    }

                    $this->deleteTargetContent($report, $target);

                    $report->statu = 0;
                    $report->action_taken = 'content_deleted';
                    $report->action_notes = $notes;
                    $report->moderator_id = $moderatorId;
                    $report->resolved_at = time();
                    $report->save();

                    ModerationLog::create([
                        'moderator_id' => $moderatorId,
                        'target_type' => $this->resolveTargetTypeName((int) $report->s_type),
                        'target_id' => (int) $report->tp_id,
                        'action' => 'delete_content',
                        'reason' => $notes ?: __('messages.content_deleted_community_standards'),
                        'report_id' => $report->id,
                        'metadata' => [
                            'target_user_id' => $targetUser?->id,
                            'points_deducted' => $deductPts,
                        ],
                        'created_at' => time(),
                    ]);

                    if ($targetUser) {
                        $this->notifications->send(
                            $targetUser,
                            __('messages.moderation_content_deleted_notice', [
                                'reason' => $notes ?: __('messages.violates_community_standards'),
                            ]),
                            '',
                            'alert'
                        );
                    }

                    if ($report->reporter) {
                        $this->notifications->send(
                            $report->reporter,
                            __('messages.report_resolved_action_taken', ['id' => $report->id]),
                            '',
                            'check'
                        );
                    }
                    break;

                case 'warn_user':
                    if (!$targetUser) {
                        throw new \InvalidArgumentException('Cannot warn user: Target user not found.');
                    }

                    $warningReason = trim((string) ($data['warning_reason'] ?? $notes)) ?: __('messages.violates_community_standards');
                    $deductPts = max(0, (int) ($data['deduct_points'] ?? 0));
                    $currentStrikes = UserWarning::where('user_id', $targetUser->id)->count();

                    UserWarning::create([
                        'user_id' => $targetUser->id,
                        'moderator_id' => $moderatorId,
                        'reason' => $warningReason,
                        'details' => $notes,
                        'points_deducted' => $deductPts,
                        'strike_level' => $currentStrikes + 1,
                        'created_at' => time(),
                    ]);

                    if ($deductPts > 0) {
                        $this->pointLedger->award(
                            $targetUser,
                            -$deductPts,
                            'moderation_warning_penalty',
                            'penalty',
                            'user_warning',
                            $targetUser->id
                        );
                    }

                    $this->notifications->send(
                        $targetUser,
                        __('messages.official_moderation_warning', ['reason' => $warningReason]),
                        '',
                        'warning'
                    );

                    $report->statu = 0;
                    $report->action_taken = 'user_warned';
                    $report->action_notes = $notes;
                    $report->moderator_id = $moderatorId;
                    $report->resolved_at = time();
                    $report->save();

                    ModerationLog::create([
                        'moderator_id' => $moderatorId,
                        'target_type' => 'user',
                        'target_id' => (int) $targetUser->id,
                        'action' => 'warn_user',
                        'reason' => $warningReason,
                        'report_id' => $report->id,
                        'metadata' => [
                            'strike_level' => $currentStrikes + 1,
                            'points_deducted' => $deductPts,
                        ],
                        'created_at' => time(),
                    ]);

                    if ($report->reporter) {
                        $this->notifications->send(
                            $report->reporter,
                            __('messages.report_resolved_action_taken', ['id' => $report->id]),
                            '',
                            'check'
                        );
                    }
                    break;

                case 'ban_user':
                    if (!$targetUser) {
                        throw new \InvalidArgumentException('Cannot ban user: Target user not found.');
                    }

                    // Ban user IP if known from recent session or telemetry
                    $reason = $notes ?: __('messages.repeated_community_violations');
                    SecurityIpBan::create([
                        'ip_address' => request()->ip() ?: '0.0.0.0',
                        'reason' => 'Banned user: ' . $targetUser->username . ' - ' . $reason,
                        'banned_by' => $moderatorId,
                        'created_at' => now(),
                    ]);

                    $report->statu = 0;
                    $report->action_taken = 'user_banned';
                    $report->action_notes = $notes;
                    $report->moderator_id = $moderatorId;
                    $report->resolved_at = time();
                    $report->save();

                    ModerationLog::create([
                        'moderator_id' => $moderatorId,
                        'target_type' => 'user',
                        'target_id' => (int) $targetUser->id,
                        'action' => 'ban_user',
                        'reason' => $reason,
                        'report_id' => $report->id,
                        'metadata' => ['username' => $targetUser->username],
                        'created_at' => time(),
                    ]);

                    if ($report->reporter) {
                        $this->notifications->send(
                            $report->reporter,
                            __('messages.report_resolved_action_taken', ['id' => $report->id]),
                            '',
                            'check'
                        );
                    }
                    break;

                default:
                    throw new \InvalidArgumentException('Unknown moderation action: ' . $action);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => __('messages.moderation_action_executed_successfully'),
                'report' => $report->fresh(),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Moderation action failed: ' . $e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if a content item has reached the auto-quarantine threshold of active reports.
     */
    public function checkAutoQuarantine(int $sType, int $tpId): bool
    {
        $activeReportsCount = Report::where('s_type', $sType)
            ->where('tp_id', $tpId)
            ->where('statu', 1)
            ->count();

        if ($activeReportsCount >= self::DEFAULT_AUTO_QUARANTINE_THRESHOLD) {
            $report = Report::where('s_type', $sType)->where('tp_id', $tpId)->where('statu', 1)->first();
            if ($report) {
                $target = $this->resolveReportTarget($report);
                if ($target) {
                    $this->hideTargetContent($report, $target);

                    ModerationLog::create([
                        'moderator_id' => null, // Automated
                        'target_type' => $this->resolveTargetTypeName($sType),
                        'target_id' => $tpId,
                        'action' => 'auto_quarantine',
                        'reason' => 'Auto-quarantined after receiving ' . $activeReportsCount . ' reports pending admin review.',
                        'report_id' => $report->id,
                        'metadata' => ['active_reports' => $activeReportsCount],
                        'created_at' => time(),
                    ]);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if text contains forbidden profanity or blacklist words.
     */
    public function containsProfanity(?string $text): bool
    {
        $text = trim((string) ($text ?? ''));
        if ($text === '') {
            return false;
        }

        $bannedWords = $this->getBannedWordsList();
        if (empty($bannedWords)) {
            return false;
        }

        $normalized = mb_strtolower($text, 'UTF-8');
        foreach ($bannedWords as $word) {
            $word = mb_strtolower(trim($word), 'UTF-8');
            if ($word !== '' && mb_stripos($normalized, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get list of banned words from SecuritySettings or default list.
     */
    public function getBannedWordsList(): array
    {
        $raw = SecuritySettings::get('moderation_banned_words', '');
        if (!empty($raw)) {
            $words = preg_split('/[\r\n,]+/', (string) $raw) ?: [];
            return array_values(array_filter(array_map('trim', $words)));
        }

        return [];
    }

    /**
     * Resolve target Eloquent model from a report.
     */
    public function resolveReportTarget(Report $report): mixed
    {
        $sType = (int) $report->s_type;
        $tpId = (int) $report->tp_id;

        return match ($sType) {
            1 => Directory::find($tpId),
            2, 4, 100, 10, 11, 12, 13, 14 => ForumTopic::find($tpId),
            6, 701 => OrderRequest::find($tpId),
            99, 702 => User::find($tpId),
            201 => Link::find($tpId),
            202 => Banner::find($tpId),
            204 => SmartAd::find($tpId),
            7867 => Product::withoutGlobalScope('store')->find($tpId),
            default => null,
        };
    }

    /**
     * Resolve target user / author of the reported content.
     */
    public function resolveTargetUser(Report $report, mixed $target = null): ?User
    {
        $target = $target ?? $this->resolveReportTarget($report);
        if (!$target) {
            return null;
        }

        if ($target instanceof User) {
            return $target;
        }

        if (isset($target->user) && $target->user instanceof User) {
            return $target->user;
        }

        if (isset($target->uid) && (int) $target->uid > 0) {
            return User::find((int) $target->uid);
        }

        return null;
    }

    /**
     * Temporarily hide reported content.
     */
    protected function hideTargetContent(Report $report, mixed $target): void
    {
        if (!$target) {
            return;
        }

        if (in_array((int) $report->s_type, [2, 4, 100, 10, 11, 12, 13, 14], true)) {
            // Forum topic & related status post
            $target->statu = 0;
            $target->save();

            Status::where('tp_id', $target->id)->where('s_type', $report->s_type)->update(['statu' => 0]);
        } elseif (isset($target->statu)) {
            $target->statu = 0;
            $target->save();
        }
    }

    /**
     * Permanently delete reported content.
     */
    protected function deleteTargetContent(Report $report, mixed $target): void
    {
        if (!$target) {
            return;
        }

        if (in_array((int) $report->s_type, [2, 4, 100, 10, 11, 12, 13, 14], true)) {
            Status::where('tp_id', $target->id)->where('s_type', $report->s_type)->delete();
            $target->delete();
        } else {
            $target->delete();
        }
    }

    /**
     * Resolve readable target type name.
     */
    protected function resolveTargetTypeName(int $sType): string
    {
        return match ($sType) {
            1 => 'directory',
            2, 4, 100 => 'forum_topic',
            10 => 'video',
            11 => 'audio',
            12 => 'file',
            13 => 'music',
            14 => 'clip',
            6, 701 => 'order',
            99, 702 => 'user',
            201 => 'link',
            202 => 'banner',
            204 => 'smart_ad',
            7867 => 'product',
            default => 'content_' . $sType,
        };
    }
}
