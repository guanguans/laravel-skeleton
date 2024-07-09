<?php

declare(strict_types=1);

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
