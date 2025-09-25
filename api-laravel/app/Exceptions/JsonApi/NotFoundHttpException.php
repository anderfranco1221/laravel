<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Support\Str;
/**
 * ! La expcion fue deprecada en favor de App\Exceptions\JsonApi\HttpException
 */
class NotFoundHttpException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'errors' => [[
                'title' => 'Not Found',
                'detail' => $this->getDetail($request),
                'status' => '404',
            ]],
        ], 404);
    }

    protected function getDetail($request): string{
        $detail = $this->getMessage();

        if(str($this->getMessage())->startsWith("No query results for model")){
            $detail = "No records found with the id '{$request->getResourceId()}' in the '{$request->getResourceType()}' resource.";
        }

        return $detail;
    }
}
