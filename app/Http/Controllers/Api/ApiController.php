<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jiannei\Response\Laravel\Support\Facades\Response;

class ApiController extends Controller
{
    public function ping(Request $request)
    {
        if ($request->get('is_fail')) {
            Response::fail('This is a failed example.');
        }

        return Response::success('This is a successful example.');
    }
}
