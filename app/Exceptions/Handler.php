<?php

namespace App\Exceptions;

use App\Http\Controllers\StatusCodeObject;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $classException = get_class($exception);
        switch($classException){
            case NotFoundHttpException::class:
                return printJson(null,buildStatusObject('PAGE_NOT_FOUND'));
            case MethodNotAllowedHttpException::class:
                return printJson(null,buildStatusObject('METHOD_NOT_ALLOWED'));
            case AccessDeniedHttpException::class:
                return printJson(null,buildStatusObject('FORBIDDEN'));
            case UnauthorizedHttpException::class:
                return printJson(null,buildStatusObject('UNAUTHORIZED'));
            case ServiceUnavailableHttpException::class:
                return printJson(null,buildStatusObject('SERVICE_UNAVAILABLE'));
            default:
                return parent::render($request, $exception);
        }
    }
}
