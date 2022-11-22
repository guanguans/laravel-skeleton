<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

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
