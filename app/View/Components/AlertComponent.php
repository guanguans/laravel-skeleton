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

class AlertComponent extends Component
{
    /**
     * alert 类型。
     *
     * @var string
     */
    public $type;

    /**
     * alert 消息。
     *
     * @var string
     */
    public $message;
    protected $except = [
        'type',
    ];

    /**
     * Create a new component instance.
     */
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    #[\Override]
    public function render()
    {
        return view('components.alert');
    }
}
