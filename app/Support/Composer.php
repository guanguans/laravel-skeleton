<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support;

use Composer\Autoload\ClassLoader;

class Composer
{
    private static ?ClassLoader $classLoader = null;

    public static function getLoader(): ClassLoader
    {
        return self::$classLoader ??= self::findLoader();
    }

    public static function setLoader(ClassLoader $classLoader): ClassLoader
    {
        return self::$classLoader = $classLoader;
    }

    private static function findLoader(): ClassLoader
    {
        $loaders = spl_autoload_functions();

        foreach ($loaders as $loader) {
            if (\is_array($loader) && $loader[0] instanceof ClassLoader) {
                return $loader[0];
            }
        }

        throw new \RuntimeException('Composer loader not found.');
    }
}
