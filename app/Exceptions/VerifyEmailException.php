<?php

namespace App\Exceptions;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class VerifyEmailException extends ValidationException
{
    public static function forUser(User $user): static
    {
        return static::withMessages([
            'email' => [__('You must :linkOpen verify :linkClose your email first.', [
                'linkOpen' => '<a href="/email/resend?email='.urlencode($user->email).'">',
                'linkClose' => '</a>',
            ])],
        ]);
    }
}
