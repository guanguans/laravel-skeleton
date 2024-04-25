<?php

namespace App\View\Composers;

use Illuminate\View\View;

class RequestComposer
{
    public function __construct(protected \Illuminate\Http\Request $request) {}

    /**
     * 绑定视图数据.
     */
    public function compose(View $view): void
    {
        $view->with('request', $this->request);
    }
}
