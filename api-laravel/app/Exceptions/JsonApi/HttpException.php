<?php

namespace App\Exceptions\JsonApi;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as ExceptionHttpException;

class HttpException extends ExceptionHttpException
{
    public function __construct(ExceptionHttpException $e)
    {
        parent::__construct(
            $e->getStatusCode(),
            $e->getMessage(),
            $e->getPrevious(),
            $e->getHeaders(),
            $e->getCode()
        );
    }

    public function render($request)
    {

        $detail = method_exists($this, $method="get{$this->getStatusCode()}Detail")
            ? $this->{$method}($request)
            : $this->getMessage();

        return response()->json([
            'errors' => [[
                'title' => Response::$statusTexts[$this->getStatusCode()],
                'detail' => $detail,
                'status' => (string) $this->getStatusCode(),
            ]],
        ], $this->getStatusCode());
    }

    protected function get404Detail($request): string{
        $detail = $this->getMessage();

        if(str($this->getMessage())->startsWith("No query results for model")){
            $detail = "No records found with the id '{$request->getResourceId()}' in the '{$request->getResourceType()}' resource.";
        }

        return $detail;
    }
}
