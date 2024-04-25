<?php

namespace App\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestComposer
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 绑定视图数据.
     */
    public function compose(View $view): void
    {
        $view->with('request', $this->request);
    }
}
