<?php

declare(strict_types=1);

namespace App\Actions\Visits;

use Carbon\Carbon;

final class GetPageViewsItemsRequest
{
    private $startDate;
    private $endDate;
    private $websiteId;

    public function __construct(string $startDate, string $endDate, int $websiteId)
    {
        $this->startDate = Carbon::createFromTimestamp($startDate)->toDateTimeString();
        $this->endDate = Carbon::createFromTimestamp($endDate)->toDateTimeString();
        $this->websiteId = $websiteId;
    }
    public function startDate(): string
    {
        return $this->startDate;
    }
    public function endDate(): string
    {
        return $this->endDate;
    }

    public function websiteId(): int
    {
        return $this->websiteId;
    }
}