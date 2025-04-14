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

            if (!$password) {
                $this->error('Passwords cannot be empty. You know that.');

                continue;
            }

            $confirmedPassword = $this->secret('Again, just to be sure');
        } while (!$this->comparePasswords($password, $confirmedPassword ?? null));

        return $password;
    }

    private function comparePasswords(
        #[\SensitiveParameter]
        ?string $password,
        #[\SensitiveParameter]
        ?string $confirmedPassword
    ): bool {
        if (!$password || !$confirmedPassword) {
            return false;
        }

        if (strcmp($password, $confirmedPassword) !== 0) {
            $this->error('The passwords do not match. Try again maybe?');

            return false;
        }

        return true;
    }
}
