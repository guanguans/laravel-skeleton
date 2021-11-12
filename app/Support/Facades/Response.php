<?php

namespace App\Support\Facades;

use Jiannei\Response\Laravel\Support\Facades\Response as JianneiResponse;

class Response extends JianneiResponse
{
    protected static function getFacadeAccessor()
    {
        return \App\Support\Response::class;
    }
}
