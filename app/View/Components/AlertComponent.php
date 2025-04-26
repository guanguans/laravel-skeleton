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

namespace App\View\Components;

use Illuminate\View\Component;

final class AlertComponent extends Component
{
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    protected $except = [
        'type',
    ];

    public function __construct(
        /** alert 类型。 */
        public string $type,
        /** alert 消息。 */
        public string $message
    ) {}

    /**
     * {@inheritDoc}
     *
     * @noinspection LaravelUnknownViewInspection
     */
    #[\Override]
    public function render(): mixed
    {
        return view('components.alert');
    }
}
