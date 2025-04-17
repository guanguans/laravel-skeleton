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
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @see https://github.com/laravelio/laravel.io/blob/main/app/Notifications/SlowQueryLogged.php
 */
class SlowQueryLoggedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $query,
        private readonly ?float $duration,
        private readonly string $url
    ) {}

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line($this->content());
    }

    private function content(): string
    {
        $content = "*Slow query logged!*\n\n";
        $content .= "```$this->query```\n\n";
        $content .= "Duration: {$this->duration}ms\n";

        return $content."URL: $this->url";
    }
}
