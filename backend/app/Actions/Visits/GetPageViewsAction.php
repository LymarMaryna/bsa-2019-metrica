<?php

declare(strict_types=1);

namespace App\Actions\Visits;

use App\Exceptions\WebsiteNotFoundException;
use App\Repositories\Contracts\ChartVisitRepository;
use Illuminate\Support\Facades\Auth;

final class GetPageViewsAction
{
    private $visitRepository;

    public function __construct(ChartVisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    public function execute(GetPageViewsRequest $request): GetPageViewsResponse
    {
        try {
            $websiteId = Auth::user()->website->id;
        } catch (\Exception $exception) {
            throw new WebsiteNotFoundException();
        }

        $data = $this->visitRepository->findByFilter(
            $request->period(),
            (int) $request->interval(),
            $websiteId
        );

        return new GetPageViewsResponse($data);
    }
}
