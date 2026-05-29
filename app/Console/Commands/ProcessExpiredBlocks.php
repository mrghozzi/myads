<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserBlock;
use App\Services\NotificationService;

class ProcessExpiredBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blocks:process-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired user blocks and send notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NotificationService $notificationService)
    {
        $expiredBlocks = UserBlock::with(['user', 'blockedUser'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredBlocks as $block) {
            $user = $block->user;
            $target = $block->blockedUser;

            if ($user && $target) {
                // Notify blocker
                $messageBlocker = __('messages.block_expired_for_you', ['user' => $target->username]) ?? "Your block on {$target->username} has expired.";
                $notificationService->send($user, $messageBlocker, route('profile.show', $target->username), 'unlock');

                // Notify blocked user
                $messageTarget = __('messages.block_expired_by_user', ['user' => $user->username]) ?? "The block from {$user->username} has expired.";
                $notificationService->send($target, $messageTarget, route('profile.show', $user->username), 'unlock');
            }

            // Remove the block
            $block->delete();
        }

        $this->info("Processed {$expiredBlocks->count()} expired blocks.");
        
        return Command::SUCCESS;
    }
}
