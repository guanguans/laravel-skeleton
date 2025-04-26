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

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

final class LogMailListener
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        $to = $message->getTo();
        $to = $to ? implode(', ', array_keys($to)) : 'No recipients';

        $subject = $message->getSubject();

        Log::info('Mail sent', ['to' => $to, 'subject' => $subject]);
    }
}
