<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 404) {
                return response([
                    'status' => 404,
                    'message' => [
                        'error' => 'Not Found Exception'
                    ]
                ], 404);
            }

            if ($exception->getStatusCode() == 500) {
                return response([
                    'status' => 500,
                    'message' => [
                        'error' => 'Internal Server Error'
                    ]
                ], 500);
            }

            if ($exception->getStatusCode() == 405) {
                return response([
                    'status' => 405,
                    'message' => 'Unauthorized',
                ], 405);
            }
        }
        return parent::render($request, $exception);
    }
}
