<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdStatsService
{
    /**
     * Get the hourly distribution of clicks for a specific ad.
     * 
     * @param int $adId
     * @param string $type ('banner', 'link', etc.)
     * @return array [hour => click_count] for 0-23
     */
    public function getHourlyHeatmap($adId, $type)
    {
        // We aggregate all clicks and group them by hour of day (0-23)
        // Since r_date is a Unix timestamp, we use MySQL HOUR(FROM_UNIXTIME(r_date))
        
        $stats = DB::table('state')
            ->select(DB::raw('HOUR(FROM_UNIXTIME(r_date)) as hour'), DB::raw('count(*) as count'))
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
        $stats = DB::table('state')
            ->select(DB::raw('DAYOFWEEK(FROM_UNIXTIME(r_date)) as day'), DB::raw('count(*) as count'))
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
