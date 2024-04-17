<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Before
{
    /**
     * @param  callable|string  $callback
     *
     * @noinspection PhpMissingParamTypeInspection
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function __construct(public $callback) {}
}
