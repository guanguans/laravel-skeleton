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

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class UserRegisteredMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build(): self
    {
        return $this
            ->subject('您已成功注册账号')
            // ->markdown('emails.users.registered')
            ->html('<div>您已成功注册账号</div>');
    }
}
