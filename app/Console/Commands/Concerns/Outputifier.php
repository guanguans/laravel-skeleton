<?php

/** @noinspection MethodVisibilityInspection */

declare(strict_types=1);

namespace App\Console\Commands\Concerns;

use function Termwind\{render};

trait Outputifier
{
    protected function information(string $message): void
    {
        render(sprintf(
            <<<'html'
                <div class="font-bold">
                    <span class="bg-blue-400 px-2 text-white mr-1">
                        INFO
                    </span>
                    %s
                </div>
                html,
            trim($message)
        ));
    }

    protected function fail(string $message): void
    {
        render(sprintf(
            <<<'html'
                <div class="font-bold">
                    <span class="bg-red-400 px-2 text-white mr-1">
                        FAIL
                    </span>
                    %s
                </div>
                html,
            trim($message)
        ));
    }

    protected function warning(string $message): void
    {
        render(sprintf(
            <<<'html'
                <div class="font-bold">
                    <span class="bg-orange-400 px-2 text-white mr-1">
                        WARNING
                    </span>
                    %s
                </div>
                html,
            trim($message)
        ));
    }

    protected function success(string $message): void
    {
        render(sprintf(
            <<<'html'
                <div class="font-bold">
                    <span class="bg-green-400 px-2 text-white mr-1">
                        SUCCESS
                    </span>
                    %s
                </div>
                html,
            trim($message)
        ));
    }
}
