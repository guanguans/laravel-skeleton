<?php

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
        if (\is_null(config('services.telegram-bot-api.channel'))) {
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
        $content .= "URL: {$this->url}";

        return $content;
    }
}
