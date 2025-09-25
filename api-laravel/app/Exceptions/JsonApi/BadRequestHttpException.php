<?php

namespace App\Exceptions\JsonApi;

use Exception;

/**
 * ! La expcion fue deprecada en favor de App\Exceptions\JsonApi\HttpException
 */
class BadRequestHttpException extends Exception
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
                'title' => 'Bad Request',
                'detail' => $this->getMessage(),
                'status' => '400',
            ]],
        ], 400);
    }
}
