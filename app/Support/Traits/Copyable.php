<?php

declare(strict_types=1);

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
