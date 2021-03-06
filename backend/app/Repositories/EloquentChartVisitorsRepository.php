<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Visitors\VisitorsBounceRateFilterData;
use App\DataTransformer\Visitors\ChartNewVisitor;
use App\Entities\Visitor;
use App\Repositories\Contracts\ChartVisitorsRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class EloquentChartVisitorsRepository implements ChartVisitorsRepository
{
    public function getNewVisitorsByDate(string $startData, string $endData, string $period, int $userId): Collection
    {
        $subQueryFirst = "SELECT id FROM websites where user_id = :user_id";
        $subQuerySecond = "SELECT visitors.*, ( " . $this->getPeriod('created_at', $period) . ") as period
                             FROM \"visitors\" WHERE
                                      created_at >= :startData and created_at <= :endData and website_id = ($subQueryFirst)";
        $query = DB::raw("SELECT COUNT(*), period from ($subQuerySecond) as periods group by period");
        $response = DB::select((string)$query, [
            'startData' => $startData,
            'endData' => $endData,
            'user_id' => $userId
        ]);
        return collect($response)->map(function ($item) {
            return new ChartNewVisitor($item->period, $item->count);
        });
    }

    public function getVisitorsCountByTimeFrame(VisitorsBounceRateFilterData $filterData): array
    {
        $from = $filterData->getStartDate();
        $to = $filterData->getEndDate();
        $timeFrame = $filterData->getTimeFrame();
        $allVisitorsByTimeFrame = Visitor::query()
            ->whereHas('sessions', function (Builder $query) use ($from, $to) {
                $query->whereBetween('start_session', [$from, $to]);
            })
            ->forUserWebsite()
            ->selectRaw('COUNT (*)')
            ->selectRaw(' (extract(epoch FROM created_at) - MOD( (CAST (extract(epoch FROM created_at) AS INTEGER)), ? )) AS period', [$timeFrame])
            ->groupBy('period')
            ->get();

        return array_column($allVisitorsByTimeFrame->toArray(), 'count', 'period');
    }

    public function getBouncedVisitorsCountByTimeFrame(VisitorsBounceRateFilterData $filterData): array
    {
        $from = $filterData->getStartDate();
        $to = $filterData->getEndDate();
        $timeFrame = $filterData->getTimeFrame();
        $bounceVisitorsByTimeFrame =  Visitor::query()
            ->forUserWebsite()
            ->has('sessions', '=', '1')
            ->whereHas('sessions', function (Builder $query) use ($from, $to) {
                $query->whereBetween('start_session', [$from, $to])
                    ->has('visits', '=', '1');
            })
            ->selectRaw('COUNT (*)')
            ->selectRaw(' (extract(epoch FROM created_at) - MOD( (CAST (extract(epoch FROM created_at) AS INTEGER)), ? )) AS period', [$timeFrame])
            ->groupBy('period')
            ->get();

        return array_column($bounceVisitorsByTimeFrame->toArray(), 'count', 'period');
    }

    private function toTimestamp(string $columnName): string
    {
        return "extract(epoch from $columnName)";
    }

    private function toNumeric(string $expression): string
    {
        return $expression . "::numeric";
    }

    private function getPeriod(string $columnName, $period): string
    {
        return $this->toNumeric($this->toTimestamp($columnName)) . "-( " .
            $this->toNumeric($this->toTimestamp($columnName)) . " %" . $period . ")";
    }
}
