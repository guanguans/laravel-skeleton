<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use NiftyCo\Skeletor\Skeletor;

return static function (Skeletor $skeletor): void {
    $skeletor->warning($skeletor->yellow('Checking ...'));
};
