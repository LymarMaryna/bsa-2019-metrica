<?php

namespace App\Exceptions;

use App\Contracts\ApiException;
use App\Http\Response\ApiResponse;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request $request
     * @param  Exception $exception
     * @return Response|JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            return ApiResponse::error(new ApiValidationException($exception->validator));
        }

        if ($exception instanceof ApiException) {
            return ApiResponse::error($exception);
        }

        if ($exception instanceof AuthenticationException) {
            return ApiResponse::error(new UnauthenticatedException());
        }

        if ($exception instanceof NotFoundHttpException) {
            return ApiResponse::error(new EndpointNotFoundException());
        }

        return parent::render($request, $exception);
    }
}
