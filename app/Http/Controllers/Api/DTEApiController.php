<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Business\DTEController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DTEApiController extends Controller
{
    public function submitDte(Request $request)
    {
        $controller = new DTEController();
        return $controller->submitFromJson($request);
    }
}
