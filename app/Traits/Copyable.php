<?php

namespace App\Traits;

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
