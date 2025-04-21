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

namespace App\Providers;

use App\Models\JWTUser;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    /** {@inheritDoc} */
    protected $policies = [
        JWTUser::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        Gate::guessPolicyNamesUsing(
            static fn (string $modelClass): string => 'App\\Policies\\'.class_basename($modelClass).'Policy'
        );

        /** @see https://github.com/koel/koel/blob/master/app/Providers/AuthServiceProvider.php */
        ResetPassword::createUrlUsing(static function (User $user, #[\SensitiveParameter] string $token): string {
            $payload = base64_encode("{$user->getEmailForPasswordReset()}|$token");

            return url("/#/reset-password/$payload");
        });
    }

    public function shouldRegister(): bool
    {
        return true;
    }
}
