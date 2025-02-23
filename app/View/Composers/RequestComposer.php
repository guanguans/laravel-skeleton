<?php

namespace App\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestComposer
{
    public function __construct(protected Request $request) {}

    /**
     * 绑定视图数据.
     */
    public function compose(View $view): void
    {
        $view->with('request', $this->request);
    }
}
