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

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    // use ExceptionTrait;

    /** {@inheritDoc} */
    protected $dontReport = [
    ];

    /** {@inheritDoc} */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    #[\Override]
    public function register(): void
    {
        $this->reportable(static function (\Throwable $e): void {});
    }

    #[\Override]
    public function report(\Throwable $e): void
    {
        parent::report($e);
    }

    #[\Override]
    public function render($request, \Throwable $e)
    {
        return parent::render($request, $e);
    }
}
