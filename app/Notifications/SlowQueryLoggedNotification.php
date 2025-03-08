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

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

/**
 * @see https://github.com/laravelio/laravel.io/blob/main/app/Notifications/SlowQueryLogged.php
 */
class SlowQueryLoggedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $query, private ?float $duration, private string $url) {}

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        if (null === config('services.telegram-bot-api.channel')) {
            return;
        }

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.channel'))
            ->content($this->content());
    }

    private function content(): string
    {
        $content = "*Slow query logged!*\n\n";
        $content .= "```{$this->query}```\n\n";
        $content .= "Duration: {$this->duration}ms\n";

        return $content."URL: {$this->url}";
    }
}
