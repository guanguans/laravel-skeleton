<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \Illuminate\Http\Client\Response messageList(int $limit = 10)
 *
 * @mixin \App\Support\PushDeer
 */
class PushDeer extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \App\Support\PushDeer::class;
    }
}
