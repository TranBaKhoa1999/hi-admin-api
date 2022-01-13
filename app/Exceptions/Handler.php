<?php

namespace App\Exceptions;

use App\Http\Controllers\StatusCodeObject;
use App\Core\Kafka;
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
        $response = null;
        $lang = null;
        $is_saveKafka = true;
        if($request->lang){
            $lang = $request->lang;
        };
        switch($classException){
            case NotFoundHttpException::class:
                $is_saveKafka = false;
                $response =  printJson(null,buildStatusObject('PAGE_NOT_FOUND'), $lang);
                break;
            case MethodNotAllowedHttpException::class:
                $is_saveKafka = false;
                $response = printJson(null,buildStatusObject('METHOD_NOT_ALLOWED'), $lang);
                break;
            case AccessDeniedHttpException::class:
                $response = printJson(null,buildStatusObject('FORBIDDEN'), $lang);
                break;
            case UnauthorizedHttpException::class:
                $response = printJson(null,buildStatusObject('UNAUTHORIZED'), $lang);
                break;
            case ServiceUnavailableHttpException::class:
                $response = printJson(null,buildStatusObject('SERVICE_UNAVAILABLE'), $lang);
                break;
            default:
                $response = parent::render($request, $exception);
        }

        if(env('APP_ENV') == 'production' && $is_saveKafka){
            try {
                //save kafak đẩy log lên kibana
                $json = [
                    'name'         => 'lumen-error-api-'.env("URL_VERSION"),
                    'contracNo'    => '',
                    'function'     => 'render',
                    'date_created' => date('Y-m-d'),
                    'input'        => json_encode([
                        'url'    => $request->fullUrl(),
                        'params' => $request->all()
                    ]),
                    'output'        => "{$exception->getCode()}|{$exception->getMessage()}|{$exception->getFile()}|{$exception->getLine()}"
                ];
                $kafka = new Kafka();
                $kafka->producer(env('KAFKA_TOPIC_NAME'), json_encode($json));
            } catch (\Exception $exc) {
                dd("{$exception->getCode()}|{$exception->getMessage()}|{$exception->getFile()}|{$exception->getLine()}");
            }
        }

        return $response;
    }
}
