<?php

namespace App\View\Creators;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestCreator
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
    public function create(View $view): void
    {
        $view->with('request', $this->request);
    }
}
