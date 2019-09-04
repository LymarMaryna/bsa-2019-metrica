<?php


namespace App\Http\Controllers\Api;

use App\Actions\PageTimings\GetAverageTimingByParamAction;
use App\Actions\PageTimings\GetAverageTimingByParamRequest;
use App\Actions\PageTimings\GetDomainLookupChartAction;
use App\Actions\PageTimings\GetPageLoadingChartAction;
use App\Actions\PageTimings\GetChartRequest;
use App\Actions\PageTimings\GetServerResponseChartAction;
use App\Http\Controllers\Controller;
use App\Http\Request\PageTimingTableHttpRequest;
use App\Http\Requests\PageTimings\PageTimingChartHttpRequest;
use App\Http\Resources\ChartResource;
use App\Http\Resources\SpeedOverviewTableResource;
use App\Http\Response\ApiResponse;

final class PageTimingController extends Controller
{
    private $getAverageValueAction;

    public function __construct(GetAverageTimingByParamAction $getAverageValueAction)
    {
        $this->getAverageValueAction = $getAverageValueAction;
    }

    public function getPageLoadingChartData(PageTimingChartHttpRequest $request, GetPageLoadingChartAction $action)
    {
        $response = $action->execute(GetChartRequest::fromRequest($request));
        return ApiResponse::success(new ChartResource($response->getCollection()));
    }

    public function getDomainLookupChartData(PageTimingChartHttpRequest $request, GetDomainLookupChartAction $action)
    {
        $response = $action->execute(GetChartRequest::fromRequest($request));
        return ApiResponse::success(new ChartResource($response->getCollection()));
    }

    public function getServerResponseChartData(PageTimingChartHttpRequest $request, GetServerResponseChartAction $action)
    {
        $response = $action->execute(GetChartRequest::fromRequest($request));
        return ApiResponse::success(new ChartResource($response->getCollection()));
    }

    public function getAveragePageLoadingTimeForParam(PageTimingTableHttpRequest $request)
    {
        $results = $this->getAverageValueAction->execute(
            new GetAverageTimingByParamRequest($request, 'page_load_time')
        );
        return ApiResponse::success(new SpeedOverviewTableResource($results));
    }

    public function getAverageDomainLookupTimeForParam(PageTimingTableHttpRequest $request)
    {
        $results = $this->getAverageValueAction->execute(
            new GetAverageTimingByParamRequest($request, 'domain_lookup_time')
        );
        return ApiResponse::success(new SpeedOverviewTableResource($results));
    }

    public function getAverageServerResponseTimeForParam(PageTimingTableHttpRequest $request)
    {
        $results = $this->getAverageValueAction->execute(
            new GetAverageTimingByParamRequest($request, 'server_response_time')
        );
        return ApiResponse::success(new SpeedOverviewTableResource($results));
    }
}
