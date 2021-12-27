<?php

namespace App\Traits;

trait Copyable
{
    public function copy()
    {
        return clone $this;
    }

    public function deepCopy()
    {
        return unserialize(serialize($this), [get_class($this)]);
    }
}
