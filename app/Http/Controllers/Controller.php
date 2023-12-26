<?php

namespace App\Http\Controllers;

use App\Support\Traits\ValidatesData;
use App\Support\Traits\ValidateStrictAll;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesData;
    use ValidatesRequests;
    use ValidateStrictAll;
}
