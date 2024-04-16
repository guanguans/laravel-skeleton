<?php

declare(strict_types=1);

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
