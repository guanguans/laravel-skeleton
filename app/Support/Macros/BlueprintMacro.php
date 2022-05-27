<?php

namespace App\Support\Macros;

class BlueprintMacro
{
    /**
     * Add a comment to the table.
     *
     * @param  string  $comment
     * @return \Illuminate\Support\Fluent
     */
    public function comment(): callable
    {
        return function ($comment) {
            /* @var \Illuminate\Database\Schema\Blueprint $this */
            return $this->addCommand('tableComment', compact('comment'));
        };
    }
}
