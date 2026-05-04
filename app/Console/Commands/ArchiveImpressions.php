<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveImpressions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myads:archive-impressions {--days=30 : Number of days of data to keep in active tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move old impression logs to archive tables to optimize database performance.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $thresholdDate = now()->subDays($days)->timestamp;

        $this->info("Archiving impressions older than {$days} days (threshold: " . date('Y-m-d H:i:s', $thresholdDate) . ")...");

        // 1. Archive Banner Impressions
        $this->archiveTable('banner_impressions', 'banner_impressions_archive', $thresholdDate);

        // 2. Archive Smart Ad Impressions
        $this->archiveTable('smart_ad_impressions', 'smart_ad_impressions_archive', $thresholdDate);

        $this->info('Archiving process completed successfully.');
        
        return Command::SUCCESS;
    }

    /**
     * Archive records from source to destination based on served_at timestamp.
     */
    private function archiveTable(string $source, string $destination, int $threshold)
    {
        $this->comment("Processing {$source}...");

        $count = DB::table($source)->where('served_at', '<', $threshold)->count();

        if ($count === 0) {
            $this->line("No records to archive in {$source}.");
            return;
        }

        $this->line("Moving {$count} records from {$source} to {$destination}...");

        // Use chunking to avoid memory issues if there are millions of rows
        DB::table($source)
            ->where('served_at', '<', $threshold)
            ->orderBy('id')
            ->chunkById(5000, function ($records) use ($source, $destination) {
                $data = $records->map(function ($record) {
                    return (array) $record;
                })->toArray();

                DB::table($destination)->insert($data);
                
                $ids = $records->pluck('id')->toArray();
                DB::table($source)->whereIn('id', $ids)->delete();
                
                $this->output->write('.');
            });

        $this->line("\nFinished archiving {$source}.");
    }
}
