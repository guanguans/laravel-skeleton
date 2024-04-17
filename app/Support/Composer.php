<?php

declare(strict_types=1);

namespace App\Support;

use Composer\Autoload\ClassLoader;
use RuntimeException;

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

        throw new RuntimeException('Composer loader not found.');
    }
}
