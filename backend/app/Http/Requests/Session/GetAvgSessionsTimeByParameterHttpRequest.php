<?php

declare(strict_types=1);

namespace App\Http\Requests\Session;

use App\Http\Request\ApiFormRequest;
use App\Rules\Parameter;
use App\Rules\Timestamp;
use App\Rules\TimestampAfter;

final class GetAvgSessionsTimeByParameterHttpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filter' => 'required|array',
            'filter.startDate' => [
                'required',
                new Timestamp()
            ],
            'filter.endDate' => [
                'required',
                new Timestamp(),
                new TimestampAfter($this->get('filter')['startDate'])
            ],
            'filter.parameter' => [
                'required',
                'string',
                new Parameter()
            ]
        ];
    }

    public function startDate(): string
    {
        return $this->get('filter')['startDate'];
    }

    public function endDate(): string
    {
        return $this->get('filter')['endDate'];
    }

    public function parameter(): string
    {
        return $this->get('filter')['parameter'];
    }
}
