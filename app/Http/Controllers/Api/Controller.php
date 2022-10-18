<?php

namespace App\Http\Controllers\Api;

use F9Web\ApiResponseHelpers;
use Jiannei\Response\Laravel\Support\Traits\JsonResponseTrait;

class Controller extends \App\Http\Controllers\Controller
{
    use JsonResponseTrait;
    use ApiResponseHelpers;
}
