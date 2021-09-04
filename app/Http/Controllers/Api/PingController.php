<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class PingController extends Controller
{
    public function ping(Request $request)
    {
        if ($request->get('is_bad')) {
            return $this->errorBadRequest('This is a bad example.');
        }

        return $this->success('This is a successful example.');
    }
}
