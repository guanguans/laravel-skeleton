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
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Traits\Conditionable;

class AuthServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /** {@inheritDoc} */
    protected $policies = [
        JWTUser::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->createUrlUsings();
        $this->gateBefore();
        Gate::guessPolicyNamesUsing(static fn (string $modelClass): string => 'App\\Policies\\'.class_basename($modelClass).'Policy');
        Gate::policy(User::class, UserPolicy::class);
        RedirectIfAuthenticated::redirectUsing(static fn ($request) => route('dashboard'));
    }

    /**
     * @see https://github.com/koel/koel/blob/master/app/Providers/AuthServiceProvider.php
     * @see https://github.com/nandi95/laravel-starter/blob/main/app/Providers/AppServiceProvider.php
     */
    private function createUrlUsings(): void
    {
        ResetPassword::createUrlUsing(static function (User $user, #[\SensitiveParameter] string $token): string {
            $payload = base64_encode("{$user->getEmailForPasswordReset()}|$token");

            return url("/#/reset-password/$payload");
        });

        ResetPassword::createUrlUsing(
            static fn (
                User $user,
                #[\SensitiveParameter]
                string $token
            ): string => config('app.frontend_url')."/auth/reset/$token?email={$user->getEmailForPasswordReset()}"
        );

        VerifyEmail::createUrlUsing(static function (object $notifiable): string {
            $url = url()->temporarySignedRoute(
                'email.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'user' => $notifiable->ulid,
                ],
                false
            );

            return config('app.frontend_url').'/auth/verify?verify_url='.urlencode($url);
        });
    }

    /**
     * Intercept any Gate and check if it's super admin, Or if you use some permissions package...
     *
     * @noinspection PhpUnusedParameterInspection
     */
    private function gateBefore(): void
    {
        Gate::before(static function (User $user, string $ability): ?true {
            if (
                $user->isAdmin()
                // || $user->hasPermission('root')
            ) {
                return true;
            }

            return null;
        });
    }
}
