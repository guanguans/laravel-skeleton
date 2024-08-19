<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\JWTUser::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();
        Gate::guessPolicyNamesUsing(
            static fn (string $modelClass): string => 'App\\Policies\\'.class_basename($modelClass).'Policy'
        );

        /** @see https://github.com/koel/koel/blob/master/app/Providers/AuthServiceProvider.php */
        ResetPassword::createUrlUsing(static function (User $user, #[\SensitiveParameter] string $token): string {
            $payload = base64_encode("{$user->getEmailForPasswordReset()}|$token");

            return url("/#/reset-password/$payload");
        });
    }
}
