<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdStatsService
{
    /** Fetch heatmaps for a collection of ads in a single grouped query. */
    public function getHourlyHeatmaps(array $adIds, string $type): array
    {
        $adIds = array_values(array_unique(array_map('intval', $adIds)));
        if ($adIds === []) {
            return [];
        }

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $hourExpr = $isSqlite
            ? 'cast(strftime(\'%H\', datetime(r_date, \'unixepoch\')) as integer) as hour'
            : 'HOUR(FROM_UNIXTIME(r_date)) as hour';
        $rows = DB::table('state')
            ->select('pid', DB::raw($hourExpr), DB::raw('count(*) as count'))
            ->whereIn('pid', $adIds)
            ->where('t_name', $type)
            ->groupBy('pid', 'hour')
            ->get();

        $heatmaps = [];
        foreach ($adIds as $id) {
            $heatmaps[$id] = array_fill(0, 24, 0);
        }
        foreach ($rows as $row) {
            $heatmaps[(int) $row->pid][(int) $row->hour] = (int) $row->count;
        }

        return $heatmaps;
    }

    /**
     * Get the hourly distribution of clicks for a specific ad.
     * 
     * @param int $adId
     * @param string $type ('banner', 'link', etc.)
     * @return array [hour => click_count] for 0-23
     */
    public function getHourlyHeatmap($adId, $type)
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $hourExpr = $isSqlite
            ? 'cast(strftime(\'%H\', datetime(r_date, \'unixepoch\')) as integer) as hour'
            : 'HOUR(FROM_UNIXTIME(r_date)) as hour';

        $stats = DB::table('state')
            ->select(DB::raw($hourExpr), DB::raw('count(*) as count'))
            ->where('pid', $adId)
            ->where('t_name', $type)
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Fill missing hours with 0
        $heatmap = [];
        for ($i = 0; $i < 24; $i++) {
            $heatmap[$i] = $stats[$i] ?? 0;
        }

        return $heatmap;
    }

    /**
     * Get day-of-week distribution.
     * 
     * @param int $adId
     * @param string $type
     * @return array [day => click_count] for 0-6 (Sun-Sat)
     */
    public function getWeeklyHeatmap($adId, $type)
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $dayExpr = $isSqlite
            ? '(cast(strftime(\'%w\', datetime(r_date, \'unixepoch\')) as integer) + 1) as day'
            : 'DAYOFWEEK(FROM_UNIXTIME(r_date)) as day';

        $stats = DB::table('state')
            ->select(DB::raw($dayExpr), DB::raw('count(*) as count'))
            ->where('pid', $adId)
            ->where('t_name', $type)
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $heatmap = [];
        for ($i = 1; $i <= 7; $i++) {
            $heatmap[$i] = $stats[$i] ?? 0;
        }

        return $heatmap;
    }
}
