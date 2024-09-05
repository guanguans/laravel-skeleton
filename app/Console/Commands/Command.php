<?php

declare(strict_types=1);

namespace App\Console\Commands;

// use Cerbero\CommandValidator\ValidatesInput;

use App\Console\Commands\Concerns\ValidatesInput;

abstract class Command extends \Illuminate\Console\Command
{
    // use \Cerbero\CommandValidator\ValidatesInput;
    use ValidatesInput;
}
