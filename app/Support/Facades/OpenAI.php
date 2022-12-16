<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response completions(array $data, ?callable $writer = null)
 * @method static \Illuminate\Support\Collection completionsByCurl(array $data, ?callable $writer = null)
 *
 * @mixin \App\Support\OpenAI
 */
class OpenAI extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \App\Support\OpenAI::class;
    }
}
