<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Visitors\GetAllVisitorsAction;
use App\Actions\Visitors\GetNewestCountAction;
use App\Actions\Visitors\GetNewestCountRequest;
use App\Actions\Visitors\GetBounceRateAction;
use App\Actions\Visitors\GetBounceRateRequest;
use App\Actions\Visitors\GetNewVisitorsAction;
use App\Http\Requests\Api\GetNewChartVisitorsHttpRequest;
use App\Http\Requests\Api\GetNewVisitorCountFilterHttpRequest;
use App\Http\Resources\VisitorCountResource;
use App\Http\Requests\Api\GetBounceRateHttpRequest;
use App\Http\Resources\BounceRateResource;
use App\Actions\Visitors\GetVisitorsByParameterAction;
use App\Actions\Visitors\GetVisitorsByParameterRequest;
use App\Http\Requests\Api\GetTableVisitorsByParameterHttpRequest;
use App\Http\Resources\VisitorResourceCollection;
use App\Http\Resources\TableVisitorsResourseCollection;
use App\Http\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChartNewVisitorResourceCollection;
use App\Actions\Visitors\GetNewChartVisitorsByDateRangeAction;
use App\Actions\Visitors\GetNewChartVisitorsByDateRangeRequest;
use App\Actions\Visitors\GetNewVisitorsByParameterAction;

final class VisitorController extends Controller
{
    private $getAllVisitorsAction;
    private $getNewVisitorsAction;
    private $getNewVisitorsByDateRangeAction;
    private $getBounceRateAction;
    private $getVisitorsByParameterAction;
    private $getNewVisitorsByParameterAction;

    public function __construct(
        GetAllVisitorsAction $getAllVisitorsAction,
        GetNewVisitorsAction $getNewVisitorsAction,
        GetNewChartVisitorsByDateRangeAction $getNewVisitorsByDateRangeAction,
        GetBounceRateAction $getBounceRateAction,
        GetVisitorsByParameterAction $getVisitorsByParameterAction,
        GetNewVisitorsByParameterAction $getNewVisitorsByParameterAction
    ) {
        $this->getAllVisitorsAction = $getAllVisitorsAction;
        $this->getNewVisitorsAction = $getNewVisitorsAction;
        $this->getNewVisitorsByDateRangeAction = $getNewVisitorsByDateRangeAction;
        $this->getBounceRateAction = $getBounceRateAction;
        $this->getVisitorsByParameterAction = $getVisitorsByParameterAction;
        $this->getNewVisitorsByParameterAction = $getNewVisitorsByParameterAction;
    }

    public function getAllVisitors(): ApiResponse
    {
        $response = $this->getAllVisitorsAction->execute();

        return ApiResponse::success(new VisitorResourceCollection($response->visitors()));
    }

    public function getNewVisitors(): ApiResponse
    {
        $response = $this->getNewVisitorsAction->execute();

        return ApiResponse::success(new VisitorResourceCollection($response->visitors()));
    }

    public function getNewVisitorsCountForFilterData(GetNewVisitorCountFilterHttpRequest $request, GetNewestCountAction $action): ApiResponse
    {
        $response = $action->execute(GetNewestCountRequest::fromRequest($request));
        return ApiResponse::success(new VisitorCountResource($response->getCount()));
    }

    public function getNewVisitorsByDateRange(GetNewChartVisitorsHttpRequest $request): ApiResponse
    {
        $response = $this->getNewVisitorsByDateRangeAction->execute(
            GetNewChartVisitorsByDateRangeRequest::fromRequest($request));

        return ApiResponse::success(new ChartNewVisitorResourceCollection($response->getVisitorsByDateRange()));
    }

    public function getBounceRate(GetBounceRateHttpRequest $request): ApiResponse
    {
        $response = $this->getBounceRateAction->execute(
            GetBounceRateRequest::fromRequest($request)
        );

        return ApiResponse::success(new BounceRateResource($response));
    }

    public function getVisitorsByParameter (GetTableVisitorsByParameterHttpRequest $request): ApiResponse
    {
        $response = $this->getVisitorsByParameterAction->execute(
            GetVisitorsByParameterRequest::fromRequest($request));

        return ApiResponse::success(new TableVisitorsResourseCollection($response->visitors()));
    }

    public function getNewVisitorsForTableByParameter(GetTableVisitorsByParameterHttpRequest $request): ApiResponse
    {
        $response = $this->getNewVisitorsByParameterAction->execute(
            GetVisitorsByParameterRequest::fromRequest($request));

        return ApiResponse::success(new TableVisitorsResourseCollection($response->visitors()));
    }


}
