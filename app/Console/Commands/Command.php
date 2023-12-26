<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Cerbero\CommandValidator\ValidatesInput;

abstract class Command extends \Illuminate\Console\Command
{
    use ValidatesInput;
}
