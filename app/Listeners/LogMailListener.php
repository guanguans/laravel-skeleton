<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class LogMailListener
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
