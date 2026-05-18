<?php

namespace App\Services\CustomAds;

use App\Models\CustomAdDeal;
use App\Models\CustomAdEvent;
use App\Models\CustomAdPlacement;
use Illuminate\Support\Facades\DB;

class CustomAdAnalyticsService
{
    public function placementSummary(CustomAdPlacement $placement): array
    {
        return $this->summaryQuery(CustomAdEvent::query()->where('placement_id', $placement->id));
    }

    public function dealSummary(CustomAdDeal $deal): array
    {
        return $this->summaryQuery(CustomAdEvent::query()->where('deal_id', $deal->id));
    }

    public function hourlyHeatmapForPlacement(CustomAdPlacement $placement): array
    {
        return $this->hourlyHeatmap(CustomAdEvent::query()->where('placement_id', $placement->id));
    }

    public function hourlyHeatmapForDeal(CustomAdDeal $deal): array
    {
        return $this->hourlyHeatmap(CustomAdEvent::query()->where('deal_id', $deal->id));
    }

    public function dailySeriesForDeal(CustomAdDeal $deal, int $days = 14): array
    {
        return $this->dailySeries(CustomAdEvent::query()->where('deal_id', $deal->id), $days);
    }

    public function topReferrersForDeal(CustomAdDeal $deal, int $limit = 5): array
    {
        return CustomAdEvent::query()
            ->select('referrer', DB::raw('count(*) as total'))
            ->where('deal_id', $deal->id)
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('total', 'referrer')
            ->toArray();
    }

    public function countriesForDeal(CustomAdDeal $deal): array
    {
        return CustomAdEvent::query()
            ->select('country_code', DB::raw('count(*) as total'))
            ->where('deal_id', $deal->id)
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->pluck('total', 'country_code')
            ->toArray();
    }

    public function devicesForDeal(CustomAdDeal $deal): array
    {
        return CustomAdEvent::query()
            ->select('device_type', DB::raw('count(*) as total'))
            ->where('deal_id', $deal->id)
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->pluck('total', 'device_type')
            ->toArray();
    }

    private function summaryQuery($query): array
    {
        $impressions = (clone $query)->where('event_type', CustomAdEvent::TYPE_IMPRESSION)->count();
        $clicks = (clone $query)->where('event_type', CustomAdEvent::TYPE_CLICK)->count();

        return [
            'impressions' => $impressions,
            'clicks' => $clicks,
            'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0.0,
        ];
    }

    private function hourlyHeatmap($query): array
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $hourExpr = $isSqlite
            ? 'cast(strftime(\'%H\', occurred_at) as integer) as hour'
            : 'HOUR(occurred_at) as hour';

        $stats = (clone $query)
            ->select(DB::raw($hourExpr), DB::raw('count(*) as count'))
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $heatmap = [];
        for ($i = 0; $i < 24; $i++) {
            $heatmap[$i] = $stats[$i] ?? 0;
        }

        return $heatmap;
    }

    private function dailySeries($query, int $days): array
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $dateExpr = $isSqlite ? 'date(occurred_at) as day' : 'DATE(occurred_at) as day';
        $from = now()->subDays(max(1, $days) - 1)->startOfDay();

        $rows = (clone $query)
            ->select(DB::raw($dateExpr), 'event_type', DB::raw('count(*) as count'))
            ->where('occurred_at', '>=', $from)
            ->groupBy('day', 'event_type')
            ->get();

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $series[$day] = ['impressions' => 0, 'clicks' => 0];
        }

        foreach ($rows as $row) {
            $day = (string) $row->day;
            if (!isset($series[$day])) {
                continue;
            }

            if ($row->event_type === CustomAdEvent::TYPE_CLICK) {
                $series[$day]['clicks'] = (int) $row->count;
            } else {
                $series[$day]['impressions'] = (int) $row->count;
            }
        }

        return $series;
    }
}
