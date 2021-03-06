<?php

declare(strict_types=1);

namespace App\Actions\Sessions;

use App\DataTransformer\ButtonValue;
use App\Repositories\Contracts\SessionRepository;
use App\Repositories\Contracts\VisitorRepository;

final class GetAvgSessionAction
{
    private $sessionRepository;
    private $visitorRepository;

    public function __construct(
        SessionRepository $sessionRepository,
        VisitorRepository $visitorRepository
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->visitorRepository = $visitorRepository;
    }

    public function execute(GetAvgSessionRequest $request): ButtonValue
    {
        $websiteId = auth()->user()->website->id;

        $visitorsIDsOfWebsite = $this->visitorRepository->getVisitorsOfWebsite((int) $websiteId)
                                                        ->pluck('id');

        $avgSessionFilter = new AverageSessionFilter($request, $visitorsIDsOfWebsite);

        $avgSessionInSeconds = (int)$this->sessionRepository->getAvgSession($avgSessionFilter)
                                                       ->first()
                                                       ->avg;

        return new ButtonValue((string)$avgSessionInSeconds);
    }
}
