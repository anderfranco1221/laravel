<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct() {
        $this->middleware("auth:sanctum");
    }

    public function __invoke(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
