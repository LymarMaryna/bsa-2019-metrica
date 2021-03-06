<?php

declare(strict_types=1);

namespace App\Actions\Visitors;

use Illuminate\Database\Eloquent\Collection;

final class GetNewVisitorsResponse
{
    private $visitors;

    public function __construct(Collection $visitors)
    {
        $this->visitors = $visitors;
    }

    public function visitors(): Collection
    {
        return $this->visitors;
    }
}