<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TableNewVisitorsRepository
{
    public function groupByCity(int $website_id, string $from, string $to): Collection;

    public function groupByCountry(int $website_id, string $from, string $to): Collection;

    public function groupByLanguage(int $website_id, string $from, string $to): Collection;

    public function groupByBrowser(int $website_id, string $from, string $to): Collection;

    public function groupByOperatingSystem(int $website_id, string $from, string $to): Collection;

    public function groupByScreenResolution(int $website_id, string $from, string $to): Collection;
}