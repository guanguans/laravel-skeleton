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

namespace App\Support\Clients;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

/**
 * @see http://www.pushdeer.com/dev.html
 */
class PushDeer extends AbstractClient
{
    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function messagePush(string $text, string $desp = '', string $type = 'markdown'): Response
    {
        return $this->pendingRequest()->post('message/push', $this->validate(
            ['text' => $text, 'desp' => $desp, 'type' => $type],
            [
                'text' => 'required|string',
                'desp' => 'string',
                'type' => 'in:markdown,text,image',
            ]
        ));
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function rules(): array
    {
        return [
            'key' => 'required|string',
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function buildPendingRequest(PendingRequest $pendingRequest): PendingRequest
    {
        return $pendingRequest
            ->throw()
            ->asJson()
            ->withOptions([
                RequestOptions::JSON => [
                    'pushkey' => $this->configRepository->get('key'),
                ],
            ]);
    }
}
