<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->json([
                'status code' => 405,
                'success' => false,
                'message' => 'MethodNotAllowedException | Use of wrong method.                .'
            ]);
        });

        /*$this->renderable(function (QueryException $e) {
            return response()->json([
                'status code' => 400,
                'success' => false,
                'message' => 'QueryException | Use of wrong query.                .'
            ]);
        });*/
        $this->renderable(function (RouteNotFoundException $e) {
            return response()->json([
                'status code' => 404,
                'success' => false,
                'message' => 'Route Not Found.',
            ]);
        });
        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'status code' => 401,
                'success' => false,
                'message' => 'AuthenticationException | Unauthenticated user.'
            ]);
        });
    }
}
