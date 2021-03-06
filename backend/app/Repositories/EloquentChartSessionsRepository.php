<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DataTransformer\ChartValue;
use App\DataTransformer\Sessions\ChartSessionValue;
use App\Repositories\Contracts\ChartSessionsRepository;
use App\Contracts\Common\DatePeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class EloquentChartSessionsRepository implements ChartSessionsRepository
{
    public function findByFilter(DatePeriod $filterData, int $websiteId): Collection
    {
        $subQuery = "SELECT s.*" .
            "FROM sessions AS s " .
            "WHERE " .
            "s.website_id = " . "$websiteId AND (" .
            $this->toTimestamp('s.start_session') . " >= :start_date AND " .
            $this->toTimestamp('s.start_session') . " <= :end_date) OR (" .
            $this->toTimestamp('s.start_session') . " <= :start_date AND " .
            $this->toTimestamp('s.end_session') . " >= :start_date)";

        $result = DB::select((string)$subQuery, [
            'start_date' => $filterData->getStartDate()->getTimestamp(),
            'end_date' => $filterData->getEndDate()->getTimestamp(),
        ]);

        return collect($result)->map(function ($item) {
            return new ChartSessionValue(
                Carbon::create($item->start_session),
                Carbon::create($item->end_session)
            );
        });
    }

    public function getSessionsByDate(DatePeriod $datePeriod, int $interval, int $websiteId): Collection
    {
        $result = DB::select("select COUNT(*) as count, " . $this->getPeriod('start_session', $interval) . "
          as period
             FROM \"sessions\"
                WHERE website_id=:websiteId AND (start_session BETWEEN :startDate AND :endDate)
            GROUP BY period
               ORDER BY period", [
            'startDate' => $datePeriod->getStartDate(),
            'endDate' => $datePeriod->getEndDate(),
            'websiteId' => $websiteId
        ]);
        return collect($result)->map(function ($item) {
            return new ChartValue($item->period, (string)$item->count);
        });
    }

    private function toTimestamp(string $columnName): string
    {
        return "extract(epoch from {$columnName})";
    }

    private function toNumeric(string $expression): string
    {
        return "({$expression})::numeric";
    }

    private function getPeriod(string $columnName, int $interval): string
    {
        return $this->toTimestamp(":startDate::timestamp") . "+$interval*(
                  DIV(" . $this->toNumeric($this->toTimestamp($columnName) . " - " .
                $this->toTimestamp(":startDate::timestamp")) . ",{$interval}))";
    }
}
