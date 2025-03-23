<?php

namespace Spatie\Health\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Logo extends Component
{
    #[\Override]
    public function render(): View
    {
        return view('health::logo');
    }
}
