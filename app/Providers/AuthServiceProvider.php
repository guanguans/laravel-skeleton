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
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Traits\Conditionable;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider implements ShouldRegisterContract
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
        // Intercept any Gate and check if it's super admin, Or if you use some permissions package...
        Gate::before(static function ($user, $ability): void {
            // if ($user->is_super_admin == 1) {
            //     return true;
            // }
            //
            // if ($user->hasPermission('root')) {
            //     return true;
            // }
        });

        Gate::guessPolicyNamesUsing(
            static fn (string $modelClass): string => 'App\\Policies\\'.class_basename($modelClass).'Policy'
        );

        /** @see https://github.com/koel/koel/blob/master/app/Providers/AuthServiceProvider.php */
        ResetPassword::createUrlUsing(static function (User $user, #[\SensitiveParameter] string $token): string {
            $payload = base64_encode("{$user->getEmailForPasswordReset()}|$token");

            return url("/#/reset-password/$payload");
        });

        $this->createUrls();

        // Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        // Sanctum::ignoreMigrations();
        // Gate::policy(User::class, UserPolicy::class);
        // Passport::enablePasswordGrant();
        // RedirectIfAuthenticated::redirectUsing(static fn ($request) => route('dashboard'));
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * @see https://github.com/nandi95/laravel-starter/blob/main/app/Providers/AppServiceProvider.php
     */
    private function createUrls(): void
    {
        ResetPassword::createUrlUsing(
            static fn (object $notifiable, #[\SensitiveParameter] string $token): string => config('app.frontend_url')."/auth/reset/$token?email={$notifiable->getEmailForPasswordReset()}"
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
}
