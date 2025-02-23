<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Console\Commands\Concerns;

/**
 * @see https://github.com/koel/koel/blob/master/app/Console/Commands/Concerns/AskForPassword.php
 *
 * @mixin \Illuminate\Console\Command
 */
trait AskForPassword
{
    private function askForPassword(): string
    {
        do {
            $password = $this->secret('Your desired password');

            if (! $password) {
                $this->error('Passwords cannot be empty. You know that.');

                continue;
            }

            $confirmedPassword = $this->secret('Again, just to be sure');
        } while (! $this->comparePasswords($password, $confirmedPassword ?? null));

        return $password;
    }

    private function comparePasswords(#[\SensitiveParameter] ?string $password, #[\SensitiveParameter] ?string $confirmedPassword): bool
    {
        if (! $password || ! $confirmedPassword) {
            return false;
        }

        if (strcmp($password, $confirmedPassword) !== 0) {
            $this->error('The passwords do not match. Try again maybe?');

            return false;
        }

        return true;
    }
}
