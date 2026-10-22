<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MyAdsBotTick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myads-bot:tick 
                            {--force : Force execution bypassing routine cooldown and probability checks}
                            {--check-releases : Check and publish GitHub releases only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger background processing cycle for the MyAds Bot (Releases, DMs, Comments, Tasks)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!function_exists('myads_bot_service')) {
            $bootPath = base_path('plugins/myads-bot/boot.php');
            if (file_exists($bootPath)) {
                require_once $bootPath;
            }
        }

        if (!function_exists('myads_bot_service')) {
            $this->error('MyAds Bot plugin is not installed or enabled.');
            return Command::FAILURE;
        }

        $service = myads_bot_service();

        if (!$service->isEnabled()) {
            $this->warn('MyAds Bot is currently disabled in plugin settings.');
            return Command::SUCCESS;
        }

        $force = (bool) $this->option('force');

        if ($this->option('check-releases')) {
            $this->info("Checking GitHub repository for new releases of MyAds script (forced: " . ($force ? "yes" : "no") . ")...");
            $result = $service->checkAndPublishGithubRelease($force);
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $result['success'] ? Command::SUCCESS : Command::FAILURE;
        }

        $this->info("Executing MyAds Bot tick cycle" . ($force ? " [FORCED]..." : "..."));
        $result = $service->runTick($force);

        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return Command::SUCCESS;
    }
}
