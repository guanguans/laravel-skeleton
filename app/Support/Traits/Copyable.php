<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Traits;

use function DeepCopy\deep_copy;

trait Copyable
{
    public function copy()
    {
        return clone $this;
    }

    public function deepCopy()
    {
        // return unserialize(serialize($this), [get_class($this)]);
        return deep_copy($this);
    }
}
