<?php

namespace App\View\Creators;

use Illuminate\View\View;

class RequestCreator
{
    public function __construct(protected \Illuminate\Http\Request $request) {}

    /**
     * 绑定视图数据.
     */
    public function create(View $view): void
    {
        $view->with('request', $this->request);
    }
}
