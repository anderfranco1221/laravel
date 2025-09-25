<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        /*$this->renderable(function(NotFoundHttpException $e, Request $request){
            $request->isJsonApi() && throw new JsonApi\NotFoundHttpException($e->getMessage());
        });*/
        $this->renderable(
            fn (HttpException $e, Request $request) => $request->isJsonApi() && throw new JsonApi\HttpException($e))
            ->renderable(
                fn (AuthenticationException $e, Request $request) => $request->isJsonApi() && throw new JsonApi\AuthenticationException);

    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return $request->isJsonApi()
            ? new JsonApiValidationErrorResponse($exception)
            : parent::invalidJson($request, $exception);
    }
}
