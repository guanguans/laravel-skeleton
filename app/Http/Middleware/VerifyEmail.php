<?php

namespace App\Http\Middleware;

class VerifyEmail extends AbortIf
{
    protected function condition(): bool
    {
        $user = \Illuminate\Support\Facades\Request::user();

        return ! optional($user)->email_verified_at;
    }

    protected function code()
    {
        return 403;
    }

    protected function message()
    {
        return '邮箱未验证';
    }
}
