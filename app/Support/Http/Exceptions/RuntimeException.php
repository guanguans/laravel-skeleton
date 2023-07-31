<?php

declare(strict_types=1);

namespace App\Support\Http\Exceptions;

use App\Support\Http\Contracts\Throwable;

class RuntimeException extends \RuntimeException implements Throwable
{
}
