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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns;

use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use function Psl\Filesystem\create_file;

/**
 * @mixin \App\Support\PhpCsFixer\Fixer\CommandLineTool\AbstractCommandLineToolFixer
 */
trait TemporaryFileCreator
{
    private static ?string $temporaryFile = null;

    /**
     * @see \Illuminate\Filesystem\Filesystem::delete()
     */
    public function __destruct()
    {
        if (self::$temporaryFile && unlink(self::$temporaryFile)) {
            clearstatcache(false, self::$temporaryFile);
            self::$temporaryFile = null;
        }
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function createTemporaryFile(string $location = '', ?string $dirName = null): string
    {
        if (self::$temporaryFile) {
            return self::$temporaryFile;
        }

        $temporaryFile = (new TemporaryDirectory)
            ->deleteWhenDestroyed()
            ->force()
            ->location($location)
            ->name($dirName ?? $this->getShortName())
            ->create()
            ->path(
                str(Str::random())
                    ->remove(\DIRECTORY_SEPARATOR)
                    ->finish('.')
                    ->finish(collect($this->extensions())->random())
                    ->toString()
            );

        // touch($temporaryFile);
        create_file($temporaryFile);

        // return self::$temporaryFile ??= create_temporary_file(null, $this->getSortName());
        return self::$temporaryFile ??= $temporaryFile;
    }
}
