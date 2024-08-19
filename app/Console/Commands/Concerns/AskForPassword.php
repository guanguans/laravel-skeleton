<?php

namespace App\Console\Commands\Concerns;

use SensitiveParameter;

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

    private function comparePasswords(#[SensitiveParameter] ?string $password, #[SensitiveParameter] ?string $confirmedPassword): bool
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
